<?php

namespace App\Http\Controllers;

use App\Enums\ApprovalStatus;
use App\Enums\TeacherAttendanceStatus;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use App\Services\TeacherAttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TeacherAttendanceAdminController extends Controller
{
    public function __construct(private readonly TeacherAttendanceService $service)
    {
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', TeacherAttendance::class);

        $from = $request->string('from')->toString() ?: now()->startOfMonth()->toDateString();
        $to = $request->string('to')->toString() ?: now()->endOfMonth()->toDateString();

        $attendances = TeacherAttendance::query()
            ->with(['teacher.teacherType', 'approver'])
            ->whereBetween('date', [$from, $to])
            ->when($request->filled('teacher_id'), fn ($q) => $q->where('teacher_id', $request->integer('teacher_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->when($request->filled('approval_status'), fn ($q) => $q->where('approval_status', $request->string('approval_status')->toString()))
            ->orderByDesc('date')
            ->orderBy('teacher_id')
            ->paginate(15)
            ->withQueryString();

        $summary = TeacherAttendance::query()
            ->whereBetween('date', [$from, $to])
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('teacher-attendance.admin-index', [
            'attendances' => $attendances,
            'teachers' => Teacher::query()->orderBy('nama')->get(),
            'summary' => $summary,
            'statuses' => TeacherAttendanceStatus::cases(),
            'filters' => array_merge(['from' => $from, 'to' => $to], $request->only(['teacher_id', 'status', 'approval_status'])),
            'pendingCount' => TeacherAttendance::query()->where('approval_status', ApprovalStatus::Pending->value)->count(),
        ]);
    }

    public function approvals(): View
    {
        Gate::authorize('approve', TeacherAttendance::class);

        return view('teacher-attendance.approvals', [
            'attendances' => TeacherAttendance::query()
                ->with('teacher.teacherType')
                ->where('approval_status', ApprovalStatus::Pending->value)
                ->orderBy('date')
                ->paginate(15),
        ]);
    }

    public function approve(Request $request, TeacherAttendance $attendance): RedirectResponse
    {
        Gate::authorize('approve', TeacherAttendance::class);

        $data = $request->validate(['note' => ['nullable', 'string', 'max:500']]);

        $this->service->decide($attendance, $request->user(), true, $data['note'] ?? null);

        return back()->with('success', 'Pengajuan disetujui.');
    }

    public function reject(Request $request, TeacherAttendance $attendance): RedirectResponse
    {
        Gate::authorize('approve', TeacherAttendance::class);

        $data = $request->validate(['note' => ['nullable', 'string', 'max:500']]);

        $this->service->decide($attendance, $request->user(), false, $data['note'] ?? null);

        return back()->with('success', 'Pengajuan ditolak dan dicatat sebagai alpa.');
    }
}
