<?php

namespace App\Http\Controllers;

use App\Models\TeacherAttendance;
use App\Services\TeacherAttendanceService;
use App\Support\SchoolSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TeacherAttendanceController extends Controller
{
    public function __construct(private readonly TeacherAttendanceService $service)
    {
    }

    public function index(Request $request): View
    {
        Gate::authorize('checkIn', TeacherAttendance::class);

        $teacher = $request->user()->teacher;

        $history = TeacherAttendance::query()
            ->where('teacher_id', $teacher->id)
            ->when($request->filled('month'), function ($query) use ($request) {
                $month = $request->string('month')->toString();
                $query->whereYear('date', substr($month, 0, 4))
                    ->whereMonth('date', substr($month, 5, 2));
            })
            ->latest('date')
            ->paginate(12)
            ->withQueryString();

        return view('teacher-attendance.index', [
            'teacher' => $teacher,
            'today' => $this->service->todayFor($request->user()),
            'history' => $history,
            'radius' => SchoolSetting::radius(),
            'hasCoordinates' => SchoolSetting::hasCoordinates(),
            'lateAfter' => SchoolSetting::lateAfter(),
        ]);
    }

    public function checkIn(Request $request): RedirectResponse
    {
        Gate::authorize('checkIn', TeacherAttendance::class);

        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $this->service->checkIn($request->user(), (float) $data['latitude'], (float) $data['longitude']);

        return back()->with('success', 'Absen masuk berhasil dicatat.');
    }

    public function checkOut(Request $request): RedirectResponse
    {
        Gate::authorize('checkIn', TeacherAttendance::class);

        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $this->service->checkOut($request->user(), (float) $data['latitude'], (float) $data['longitude']);

        return back()->with('success', 'Absen pulang berhasil dicatat.');
    }

    public function requestPermission(Request $request): RedirectResponse
    {
        Gate::authorize('requestPermission', TeacherAttendance::class);

        $data = $request->validate([
            'date' => ['required', 'date', 'before_or_equal:today'],
            'type' => ['required', 'in:izin,dinas_luar,sakit'],
            'reason' => ['required', 'string', 'max:1000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
        ]);

        $this->service->requestPermission($request->user(), [
            'date' => $data['date'],
            'type' => $data['type'],
            'reason' => $data['reason'],
            'attachment' => $request->file('attachment'),
        ]);

        return back()->with('success', 'Pengajuan izin/dinas luar terkirim dan menunggu persetujuan Kepala Sekolah.');
    }
}
