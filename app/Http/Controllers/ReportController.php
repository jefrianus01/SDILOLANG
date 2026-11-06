<?php

namespace App\Http\Controllers;

use App\Exports\ClassRecapExport;
use App\Exports\TeacherRecapExport;
use App\Models\Classroom;
use App\Models\TeacherAttendance;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $service)
    {
    }

    public function classRecap(Request $request, Classroom $classroom): View
    {
        Gate::authorize('view', $classroom);

        $month = $this->service->parseMonth($this->monthInput($request));

        return view('reports.class-recap', [
            'recap' => $this->service->classRecap($classroom, $month),
            'month' => $month,
        ]);
    }

    public function classRecapPdf(Request $request, Classroom $classroom)
    {
        Gate::authorize('view', $classroom);

        $recap = $this->service->classRecap($classroom, $this->service->parseMonth($this->monthInput($request)));

        return Pdf::loadView('exports.pdf.class-recap', ['recap' => $recap])
            ->setPaper('a4', 'landscape')
            ->download('rekap-'.$classroom->name.'-'.$recap['month']->format('m-Y').'.pdf');
    }

    public function classRecapExcel(Request $request, Classroom $classroom): BinaryFileResponse
    {
        Gate::authorize('view', $classroom);

        $recap = $this->service->classRecap($classroom, $this->service->parseMonth($this->monthInput($request)));

        return Excel::download(new ClassRecapExport($recap), 'rekap-'.$classroom->name.'-'.$recap['month']->format('m-Y').'.xlsx');
    }

    public function teacherRecap(Request $request): View
    {
        Gate::authorize('viewAny', TeacherAttendance::class);

        $month = $this->service->parseMonth($this->monthInput($request));

        return view('reports.teacher-recap', [
            'recap' => $this->service->teacherRecap($month),
            'month' => $month,
        ]);
    }

    public function teacherRecapPdf(Request $request)
    {
        Gate::authorize('viewAny', TeacherAttendance::class);

        $recap = $this->service->teacherRecap($this->service->parseMonth($this->monthInput($request)));

        return Pdf::loadView('exports.pdf.teacher-recap', ['recap' => $recap])
            ->setPaper('a4', 'landscape')
            ->download('rekap-guru-'.$recap['month']->format('m-Y').'.pdf');
    }

    public function teacherRecapExcel(Request $request): BinaryFileResponse
    {
        Gate::authorize('viewAny', TeacherAttendance::class);

        $recap = $this->service->teacherRecap($this->service->parseMonth($this->monthInput($request)));

        return Excel::download(new TeacherRecapExport($recap), 'rekap-guru-'.$recap['month']->format('m-Y').'.xlsx');
    }

    private function monthInput(Request $request): string
    {
        return $request->string('month')->toString() ?: now()->format('Y-m');
    }
}
