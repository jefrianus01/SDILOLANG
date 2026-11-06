<?php

namespace App\Http\Controllers;

use App\Enums\ApprovalStatus;
use App\Enums\AttendanceStatus;
use App\Enums\TeacherAttendanceStatus;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\News;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isSuperAdmin() || $user->isKepalaSekolah()) {
            return $this->adminDashboard($user);
        }

        if ($user->isWaliKelas()) {
            return $this->homeroomDashboard($user);
        }

        return $this->teacherDashboard($user);
    }

    private function adminDashboard(User $user): View
    {
        $academicYear = AcademicYear::current();
        $today = now()->toDateString();

        $classrooms = $academicYear
            ? Classroom::query()
                ->where('academic_year_id', $academicYear->id)
                ->with('waliKelas')
                ->withCount(['students' => fn ($q) => $q->where('status', 'aktif')])
                ->orderBy('level')
                ->get()
            : collect();

        $teacherStatusCounts = TeacherAttendance::query()
            ->whereDate('date', $today)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $pendingApprovals = TeacherAttendance::query()
            ->where('approval_status', ApprovalStatus::Pending->value)
            ->count();

        $recentNews = News::query()->latest('created_at')->limit(5)->get();

        $pendingRequests = TeacherAttendance::query()
            ->with('teacher')
            ->where('approval_status', ApprovalStatus::Pending->value)
            ->latest('date')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'mode' => 'admin',
            'academicYear' => $academicYear,
            'classrooms' => $classrooms,
            'totalTeachers' => Teacher::query()->where('status', 'aktif')->count(),
            'totalStudents' => Student::query()->where('status', 'aktif')->count(),
            'teacherStatusCounts' => $teacherStatusCounts,
            'pendingApprovals' => $pendingApprovals,
            'recentNews' => $recentNews,
            'pendingRequests' => $pendingRequests,
            'today' => $today,
            'statuses' => TeacherAttendanceStatus::cases(),
        ]);
    }

    private function homeroomDashboard(User $user): View
    {
        $classroom = $user->homeroomClassroom();
        $today = now()->toDateString();

        $students = $classroom
            ? Student::query()->where('classroom_id', $classroom->id)->where('status', 'aktif')->get()
            : collect();

        $todayCounts = $classroom
            ? StudentAttendance::query()
                ->where('classroom_id', $classroom->id)
                ->whereDate('date', $today)
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
            : collect();

        $weekTrend = $classroom
            ? StudentAttendance::query()
                ->where('classroom_id', $classroom->id)
                ->whereBetween('date', [now()->subDays(6)->toDateString(), $today])
                ->selectRaw('date, status, count(*) as total')
                ->groupBy('date', 'status')
                ->orderBy('date')
                ->get()
                ->map(fn ($row) => [
                    'date' => $row->date->format('Y-m-d'),
                    'status' => $row->status instanceof AttendanceStatus ? $row->status->value : (string) $row->status,
                    'total' => (int) $row->total,
                ])
                ->groupBy('date')
            : collect();

        $teacherAttendance = $user->teacher
            ? TeacherAttendance::query()
                ->where('teacher_id', $user->teacher->id)
                ->whereDate('date', $today)
                ->first()
            : null;

        return view('dashboard', [
            'mode' => 'homeroom',
            'classroom' => $classroom,
            'students' => $students,
            'todayCounts' => $todayCounts,
            'weekTrend' => $weekTrend,
            'teacherAttendance' => $teacherAttendance,
            'today' => $today,
            'statuses' => AttendanceStatus::cases(),
        ]);
    }

    private function teacherDashboard(User $user): View
    {
        $teacher = $user->teacher;

        $today = $teacher
            ? TeacherAttendance::query()
                ->where('teacher_id', $teacher->id)
                ->whereDate('date', now()->toDateString())
                ->first()
            : null;

        $history = $teacher
            ? TeacherAttendance::query()
                ->where('teacher_id', $teacher->id)
                ->latest('date')
                ->limit(10)
                ->get()
            : collect();

        return view('dashboard', [
            'mode' => 'teacher',
            'teacher' => $teacher,
            'teacherAttendance' => $today,
            'history' => $history,
            'today' => now()->toDateString(),
        ]);
    }
}
