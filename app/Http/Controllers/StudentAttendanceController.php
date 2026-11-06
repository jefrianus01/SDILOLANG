<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Services\StudentAttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class StudentAttendanceController extends Controller
{
    public function __construct(private readonly StudentAttendanceService $service)
    {
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', StudentAttendance::class);

        $user = $request->user();
        $date = $request->string('date')->toString() ?: now()->toDateString();

        $classrooms = $user->canSeeAllStudents()
            ? Classroom::query()
                ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
                ->with('waliKelas')
                ->withCount(['students' => fn ($q) => $q->where('status', 'aktif')])
                ->orderBy('level')
                ->get()
            : $user->homeroomClassrooms()->load('waliKelas')->loadCount(['students' => fn ($q) => $q->where('status', 'aktif')]);

        $recorded = StudentAttendance::query()
            ->whereDate('date', $date)
            ->whereIn('classroom_id', $classrooms->pluck('id'))
            ->selectRaw('classroom_id, count(*) as total')
            ->groupBy('classroom_id')
            ->pluck('total', 'classroom_id');

        return view('student-attendance.index', [
            'classrooms' => $classrooms,
            'recorded' => $recorded,
            'date' => $date,
            'academicYear' => AcademicYear::current(),
        ]);
    }

    public function show(Request $request, Classroom $classroom): View
    {
        Gate::authorize('view', $classroom);

        $date = $request->string('date')->toString() ?: now()->toDateString();

        $students = Student::query()
            ->where('classroom_id', $classroom->id)
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        $existing = StudentAttendance::query()
            ->where('classroom_id', $classroom->id)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('student_id');

        return view('student-attendance.show', [
            'classroom' => $classroom->load('waliKelas', 'academicYear'),
            'students' => $students,
            'existing' => $existing,
            'date' => $date,
            'canEdit' => Gate::allows('recordAttendance', $classroom),
            'statuses' => AttendanceStatus::cases(),
        ]);
    }

    public function store(Request $request, Classroom $classroom): RedirectResponse
    {
        Gate::authorize('recordAttendance', $classroom);

        $data = $request->validate([
            'date' => ['required', 'date', 'before_or_equal:today'],
            'statuses' => ['required', 'array', 'min:1'],
            'statuses.*' => ['required', 'in:'.implode(',', AttendanceStatus::values())],
        ]);

        $saved = $this->service->saveForClass($classroom, $data['date'], $data['statuses'], $request->user());

        return redirect()
            ->route('student-attendance.show', ['classroom' => $classroom, 'date' => $data['date']])
            ->with('success', 'Absensi tersimpan untuk '.$saved.' siswa.');
    }
}
