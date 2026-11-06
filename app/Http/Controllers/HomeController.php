<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Gallery;
use App\Models\News;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $academicYear = AcademicYear::current();

        return view('public.home', [
            'news' => News::query()->published()->latest('published_at')->limit(3)->get(),
            'galleries' => Gallery::query()->where('is_published', true)->orderBy('sort_order')->limit(6)->get(),
            'classrooms' => $academicYear
                ? Classroom::query()
                    ->where('academic_year_id', $academicYear->id)
                    ->withCount(['students' => fn ($q) => $q->where('status', 'aktif')])
                    ->orderBy('level')
                    ->get()
                : collect(),
            'teacherCount' => Teacher::query()->where('status', 'aktif')->count(),
            'studentCount' => Student::query()->where('status', 'aktif')->count(),
        ]);
    }

    public function profile(): View
    {
        return view('public.profile');
    }
}
