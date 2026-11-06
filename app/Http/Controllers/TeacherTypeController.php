<?php

namespace App\Http\Controllers;

use App\Models\TeacherType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeacherTypeController extends Controller
{
    public function index(): View
    {
        Gate::authorize('manage', TeacherType::class);

        return view('teacher-types.index', [
            'types' => TeacherType::query()->withCount('teachers')->orderBy('name')->paginate(15),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage', TeacherType::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('teacher_types', 'name')],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        TeacherType::create([
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name']),
            'description' => $data['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Jenis guru berhasil ditambahkan.');
    }

    public function update(Request $request, TeacherType $teacherType): RedirectResponse
    {
        Gate::authorize('manage', TeacherType::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('teacher_types', 'name')->ignore($teacherType->id)],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $teacherType->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Jenis guru berhasil diperbarui.');
    }

    public function destroy(TeacherType $teacherType): RedirectResponse
    {
        Gate::authorize('manage', TeacherType::class);

        if ($teacherType->teachers()->exists()) {
            return back()->with('error', 'Jenis guru masih dipakai oleh data guru, tidak bisa dihapus.');
        }

        $teacherType->delete();

        return back()->with('success', 'Jenis guru berhasil dihapus.');
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'jenis-guru';
        $slug = $base;
        $i = 2;

        while (TeacherType::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
