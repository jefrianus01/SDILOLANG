<?php

namespace App\Http\Controllers;

use App\Enums\TeacherStatus;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Teacher;
use App\Services\AuditLogger;
use App\Services\HomeroomService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ClassroomController extends Controller
{
    public function __construct(private readonly HomeroomService $homeroomService)
    {
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Classroom::class);

        $years = AcademicYear::query()->orderByDesc('starts_on')->get();
        $selectedYear = $request->filled('academic_year_id')
            ? $years->firstWhere('id', $request->integer('academic_year_id'))
            : ($years->firstWhere('is_active', true) ?? $years->first());

        $classrooms = $selectedYear
            ? Classroom::query()
                ->where('academic_year_id', $selectedYear->id)
                ->with('waliKelas')
                ->withCount(['students' => fn ($q) => $q->where('status', 'aktif')])
                ->orderBy('level')
                ->get()
            : collect();

        return view('classrooms.index', [
            'years' => $years,
            'selectedYear' => $selectedYear,
            'classrooms' => $classrooms,
        ]);
    }

    public function edit(Classroom $classroom): View
    {
        Gate::authorize('assignHomeroom', Classroom::class);

        $teachers = Teacher::query()
            ->with('teacherType')
            ->where('status', TeacherStatus::Aktif->value)
            ->orderBy('nama')
            ->get();

        $assignedElsewhere = Classroom::query()
            ->where('academic_year_id', $classroom->academic_year_id)
            ->where('id', '!=', $classroom->id)
            ->whereNotNull('wali_kelas_id')
            ->pluck('wali_kelas_id')
            ->all();

        return view('classrooms.edit', [
            'classroom' => $classroom->load('academicYear', 'waliKelas'),
            'teachers' => $teachers,
            'assignedElsewhere' => $assignedElsewhere,
        ]);
    }

    public function update(Request $request, Classroom $classroom): RedirectResponse
    {
        Gate::authorize('assignHomeroom', Classroom::class);

        $data = $request->validate([
            'wali_kelas_id' => ['nullable', 'integer', 'exists:teachers,id'],
        ]);

        $teacher = $data['wali_kelas_id'] ? Teacher::find($data['wali_kelas_id']) : null;

        if ($teacher && $teacher->status !== TeacherStatus::Aktif) {
            throw ValidationException::withMessages([
                'wali_kelas_id' => 'Guru yang dipilih berstatus nonaktif.',
            ]);
        }

        if ($teacher) {
            $alreadyAssigned = Classroom::query()
                ->where('academic_year_id', $classroom->academic_year_id)
                ->where('wali_kelas_id', $teacher->id)
                ->where('id', '!=', $classroom->id)
                ->exists();

            if ($alreadyAssigned) {
                throw ValidationException::withMessages([
                    'wali_kelas_id' => 'Guru ini sudah menjadi wali kelas di kelas lain pada tahun ajaran yang sama.',
                ]);
            }
        }

        $this->homeroomService->assign($classroom, $teacher);

        AuditLogger::log(
            'classroom.homeroom-assigned',
            $classroom,
            'Wali kelas '.$classroom->name.': '.($teacher?->nama ?? 'dikosongkan')
        );

        return redirect()
            ->route('classrooms.index', ['academic_year_id' => $classroom->academic_year_id])
            ->with('success', 'Wali kelas berhasil diperbarui. Role wali kelas otomatis disinkronkan.');
    }
}
