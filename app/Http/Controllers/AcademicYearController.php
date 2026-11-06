<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Services\AuditLogger;
use App\Services\HomeroomService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function __construct(private readonly HomeroomService $homeroomService)
    {
    }

    public function index(): View
    {
        Gate::authorize('manage', AcademicYear::class);

        return view('academic-years.index', [
            'years' => AcademicYear::query()->withCount('classrooms')->latest('starts_on')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage', AcademicYear::class);

        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            $year = AcademicYear::create($data);

            foreach (range(1, 6) as $level) {
                Classroom::create([
                    'academic_year_id' => $year->id,
                    'name' => 'Kelas '.$level,
                    'level' => $level,
                ]);
            }

            if (! AcademicYear::query()->where('is_active', true)->exists()) {
                $year->update(['is_active' => true]);
            }
        });

        return back()->with('success', 'Tahun ajaran dan kelas 1-6 berhasil dibuat.');
    }

    public function update(Request $request, AcademicYear $academicYear): RedirectResponse
    {
        Gate::authorize('manage', AcademicYear::class);

        $data = $this->validated($request, $academicYear);

        $academicYear->update($data);

        return back()->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function activate(AcademicYear $academicYear): RedirectResponse
    {
        Gate::authorize('manage', AcademicYear::class);

        DB::transaction(function () use ($academicYear) {
            AcademicYear::query()->where('id', '!=', $academicYear->id)->update(['is_active' => false]);
            $academicYear->update(['is_active' => true]);

            foreach (range(1, 6) as $level) {
                Classroom::firstOrCreate(
                    ['academic_year_id' => $academicYear->id, 'level' => $level],
                    ['name' => 'Kelas '.$level]
                );
            }

            $this->homeroomService->syncAll();
        });

        AuditLogger::log('academic-year.activated', $academicYear, 'Mengaktifkan tahun ajaran '.$academicYear->name);

        return back()->with('success', 'Tahun ajaran '.$academicYear->name.' diaktifkan.');
    }

    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        Gate::authorize('manage', AcademicYear::class);

        $hasStudents = Classroom::query()
            ->where('academic_year_id', $academicYear->id)
            ->whereHas('students')
            ->exists();

        if ($hasStudents) {
            return back()->with('error', 'Tahun ajaran masih memiliki data siswa dan tidak bisa dihapus.');
        }

        if (AcademicYear::query()->where('is_active', true)->count() <= 1 && $academicYear->is_active) {
            return back()->with('error', 'Tahun ajaran aktif tidak bisa dihapus. Aktifkan tahun ajaran lain terlebih dahulu.');
        }

        $academicYear->delete();

        return back()->with('success', 'Tahun ajaran berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?AcademicYear $academicYear = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'regex:/^\d{4}\/\d{4}$/',
                Rule::unique('academic_years', 'name')->ignore($academicYear?->id),
            ],
            'starts_on' => ['required', 'date'],
            'ends_on' => ['required', 'date', 'after:starts_on'],
        ], [
            'name.regex' => 'Format tahun ajaran harus seperti 2025/2026.',
        ]);
    }
}
