<?php

namespace App\Http\Controllers;

use App\Enums\TeacherStatus;
use App\Exports\TeachersExport;
use App\Exports\TeacherTemplateExport;
use App\Models\Teacher;
use App\Models\TeacherType;
use App\Services\TeacherImportService;
use App\Services\TeacherService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TeacherController extends Controller
{
    public function __construct(
        private readonly TeacherService $teacherService,
        private readonly TeacherImportService $importService,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Teacher::class);

        $teachers = $this->filteredQuery($request)
            ->with(['teacherType', 'classrooms'])
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('teachers.index', [
            'teachers' => $teachers,
            'types' => TeacherType::query()->orderBy('name')->get(),
            'filters' => $request->only(['search', 'teacher_type_id', 'status']),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Teacher::class);

        return view('teachers.create', [
            'types' => TeacherType::query()->where('is_active', true)->orderBy('name')->get(),
            'statuses' => TeacherStatus::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Teacher::class);

        $data = $this->validated($request);

        [$teacher, $password] = $this->teacherService->create($data);

        return redirect()
            ->route('teachers.index')
            ->with('generated_account', [
                'name' => $teacher->nama,
                'username' => $teacher->nip_nik,
                'password' => $password,
            ])
            ->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function edit(Teacher $teacher): View
    {
        Gate::authorize('update', $teacher);

        return view('teachers.edit', [
            'teacher' => $teacher->load('teacherType'),
            'types' => TeacherType::query()->orderBy('name')->get(),
            'statuses' => TeacherStatus::cases(),
        ]);
    }

    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        Gate::authorize('update', $teacher);

        $data = $this->validated($request, $teacher);

        $this->teacherService->update($teacher, $data);

        return redirect()
            ->route('teachers.index')
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        Gate::authorize('delete', $teacher);

        $this->teacherService->delete($teacher);

        return redirect()
            ->route('teachers.index')
            ->with('success', 'Data guru dihapus (soft delete) dan akun login dinonaktifkan.');
    }

    public function resetPassword(Teacher $teacher): RedirectResponse
    {
        Gate::authorize('resetPassword', $teacher);

        $password = $this->teacherService->resetPassword($teacher);

        return redirect()
            ->route('teachers.index')
            ->with('generated_account', [
                'name' => $teacher->nama,
                'username' => $teacher->nip_nik,
                'password' => $password,
            ])
            ->with('success', 'Password guru berhasil direset. Tampilkan sekali ini saja.');
    }

    public function importForm(): View
    {
        Gate::authorize('import', Teacher::class);

        return view('teachers.import');
    }

    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('import', Teacher::class);

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        $result = $this->importService->import($request->file('file'), $request->user());

        return redirect()
            ->route('teachers.import.form')
            ->with('import_result', [
                'imported' => $result->imported,
                'failures' => $result->failures,
                'accounts' => $result->accounts,
            ]);
    }

    public function template(): BinaryFileResponse
    {
        Gate::authorize('import', Teacher::class);

        return Excel::download(new TeacherTemplateExport, 'template-import-guru.xlsx');
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        Gate::authorize('viewAny', Teacher::class);

        $teachers = $this->filteredQuery($request)->with('teacherType')->orderBy('nama')->get();

        return Excel::download(new TeachersExport($teachers), 'data-guru-'.now()->format('Ymd-His').'.xlsx');
    }

    public function exportPdf(Request $request)
    {
        Gate::authorize('viewAny', Teacher::class);

        $teachers = $this->filteredQuery($request)->with('teacherType')->orderBy('nama')->get();

        return Pdf::loadView('exports.pdf.teachers', ['teachers' => $teachers])
            ->setPaper('a4', 'landscape')
            ->download('data-guru-'.now()->format('Ymd-His').'.pdf');
    }

    private function filteredQuery(Request $request)
    {
        return Teacher::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', '%'.$search.'%')
                        ->orWhere('nip_nik', 'like', '%'.$search.'%');
                });
            })
            ->when($request->filled('teacher_type_id'), fn ($q) => $q->where('teacher_type_id', $request->integer('teacher_type_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()));
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Teacher $teacher = null): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'nip_nik' => [
                'required',
                'regex:/^\d{16,18}$/',
                Rule::unique('teachers', 'nip_nik')->ignore($teacher?->id),
                Rule::unique('users', 'username')->ignore($teacher?->user_id),
            ],
            'teacher_type_id' => ['required', 'exists:teacher_types,id'],
            'status' => ['required', Rule::in(TeacherStatus::values())],
        ], [
            'nip_nik.regex' => 'NIP/NIK harus berupa 16-18 digit angka.',
            'nip_nik.unique' => 'NIP/NIK sudah digunakan.',
        ]);
    }
}
