<?php

namespace App\Http\Controllers;

use App\Enums\StudentStatus;
use App\Exports\StudentsExport;
use App\Exports\StudentTemplateExport;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Student;
use App\Services\AuditLogger;
use App\Services\StudentImportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StudentController extends Controller
{
    public function __construct(private readonly StudentImportService $importService)
    {
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Student::class);

        $user = $request->user();
        $classrooms = $this->availableClassrooms($request);

        $students = Student::query()
            ->with('classroom')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', '%'.$search.'%')
                        ->orWhere('nomor_induk_siswa', 'like', '%'.$search.'%');
                });
            })
            ->when($request->filled('classroom_id'), fn ($q) => $q->where('classroom_id', $request->integer('classroom_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->orderBy('classroom_id')
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('students.index', [
            'students' => $students,
            'classrooms' => $classrooms,
            'filters' => $request->only(['search', 'classroom_id', 'status']),
            'homeroom' => $user->isWaliKelas() ? $user->homeroomClassroom() : null,
            'statuses' => StudentStatus::cases(),
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', Student::class);

        $classroom = $request->user()->homeroomClassroom();

        if (! $classroom) {
            abort(403, 'Anda belum ditunjuk sebagai wali kelas.');
        }

        return view('students.create', ['classroom' => $classroom]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Student::class);

        $classroom = $request->user()->homeroomClassroom();

        if (! $classroom) {
            abort(403, 'Anda belum ditunjuk sebagai wali kelas.');
        }

        $data = $this->validated($request);

        $student = Student::create([
            'nomor_induk_siswa' => $data['nomor_induk_siswa'],
            'nama' => $data['nama'],
            'classroom_id' => $classroom->id,
            'status' => StudentStatus::Aktif->value,
        ]);

        AuditLogger::log('student.created', $student, 'Menambah siswa: '.$student->nama);

        return redirect()
            ->route('students.index')
            ->with('success', 'Siswa berhasil ditambahkan ke '.$classroom->name.'.');
    }

    public function edit(Student $student): View
    {
        Gate::authorize('update', $student);

        return view('students.edit', [
            'student' => $student->load('classroom'),
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        Gate::authorize('update', $student);

        $data = $this->validated($request, $student);

        $student->update([
            'nomor_induk_siswa' => $data['nomor_induk_siswa'],
            'nama' => $data['nama'],
        ]);

        AuditLogger::log('student.updated', $student, 'Mengubah siswa: '.$student->nama);

        return redirect()
            ->route('students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        Gate::authorize('delete', $student);

        $student->delete();

        AuditLogger::log('student.deleted', $student, 'Menghapus siswa: '.$student->nama);

        return redirect()
            ->route('students.index')
            ->with('success', 'Data siswa dihapus (soft delete).');
    }

    public function importForm(Request $request): View
    {
        Gate::authorize('import', Student::class);

        $classroom = $request->user()->homeroomClassroom();

        if (! $classroom) {
            abort(403, 'Anda belum ditunjuk sebagai wali kelas.');
        }

        return view('students.import', ['classroom' => $classroom]);
    }

    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('import', Student::class);

        $classroom = $request->user()->homeroomClassroom();

        if (! $classroom) {
            abort(403, 'Anda belum ditunjuk sebagai wali kelas.');
        }

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        $result = $this->importService->import($request->file('file'), $classroom, $request->user());

        return redirect()
            ->route('students.import.form')
            ->with('import_result', [
                'imported' => $result->imported,
                'failures' => $result->failures,
                'accounts' => [],
            ]);
    }

    public function template(): BinaryFileResponse
    {
        Gate::authorize('import', Student::class);

        return Excel::download(new StudentTemplateExport, 'template-import-siswa.xlsx');
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        Gate::authorize('viewAny', Student::class);

        $students = $this->filteredQuery($request)->with('classroom')->orderBy('classroom_id')->orderBy('nama')->get();

        return Excel::download(new StudentsExport($students), 'data-siswa-'.now()->format('Ymd-His').'.xlsx');
    }

    public function exportPdf(Request $request)
    {
        Gate::authorize('viewAny', Student::class);

        $students = $this->filteredQuery($request)->with('classroom')->orderBy('classroom_id')->orderBy('nama')->get();

        return Pdf::loadView('exports.pdf.students', ['students' => $students])
            ->setPaper('a4', 'landscape')
            ->download('data-siswa-'.now()->format('Ymd-His').'.pdf');
    }

    public function promoteForm(): View
    {
        Gate::authorize('promote', Student::class);

        $years = AcademicYear::query()->with('classrooms')->orderByDesc('starts_on')->get();

        return view('students.promote', [
            'years' => $years,
            'classrooms' => Classroom::query()
                ->with('academicYear')
                ->withCount(['students' => fn ($q) => $q->where('status', 'aktif')])
                ->orderBy('academic_year_id')
                ->orderBy('level')
                ->get(),
        ]);
    }

    public function promote(Request $request): RedirectResponse
    {
        Gate::authorize('promote', Student::class);

        $data = $request->validate([
            'source_classroom_id' => ['required', 'exists:classrooms,id'],
            'target_classroom_id' => ['nullable', 'exists:classrooms,id', 'different:source_classroom_id'],
        ]);

        $source = Classroom::findOrFail($data['source_classroom_id']);
        $target = $data['target_classroom_id'] ? Classroom::find($data['target_classroom_id']) : null;

        $moved = DB::transaction(function () use ($source, $target) {
            $query = Student::query()
                ->where('classroom_id', $source->id)
                ->where('status', StudentStatus::Aktif->value);

            if ($target) {
                return $query->update(['classroom_id' => $target->id]);
            }

            return $query->update(['status' => StudentStatus::Lulus->value]);
        });

        AuditLogger::log(
            'student.promoted',
            $source,
            $target
                ? 'Kenaikan kelas '.$source->name.' -> '.$target->name.' ('.$moved.' siswa)'
                : 'Meluluskan siswa '.$source->name.' ('.$moved.' siswa)'
        );

        return redirect()
            ->route('students.promote.form')
            ->with('success', $target
                ? $moved.' siswa dipindahkan ke '.$target->name.'.'
                : $moved.' siswa kelas '.$source->name.' ditandai lulus.');
    }

    private function filteredQuery(Request $request)
    {
        return Student::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', '%'.$search.'%')
                        ->orWhere('nomor_induk_siswa', 'like', '%'.$search.'%');
                });
            })
            ->when($request->filled('classroom_id'), fn ($q) => $q->where('classroom_id', $request->integer('classroom_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()));
    }

    private function availableClassrooms(Request $request)
    {
        $user = $request->user();

        if ($user->canSeeAllStudents()) {
            return Classroom::query()
                ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
                ->orderBy('level')
                ->get();
        }

        return $user->homeroomClassrooms();
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Student $student = null): array
    {
        return $request->validate([
            'nomor_induk_siswa' => [
                'required',
                'string',
                'max:30',
                Rule::unique('students', 'nomor_induk_siswa')->ignore($student?->id),
            ],
            'nama' => ['required', 'string', 'max:150'],
        ], [
            'nomor_induk_siswa.unique' => 'Nomor Induk Siswa sudah digunakan.',
        ]);
    }
}
