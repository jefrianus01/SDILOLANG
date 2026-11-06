<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Gallery::class);

        return view('admin.gallery.index', [
            'galleries' => Gallery::query()->orderBy('sort_order')->orderByDesc('id')->paginate(12),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Gallery::class);

        return view('admin.gallery.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Gallery::class);

        $data = $this->validated($request, true);
        $data['image_path'] = $request->file('image')->store('gallery', 'public');

        $gallery = Gallery::create($data);

        AuditLogger::log('gallery.created', $gallery, 'Menambah galeri: '.$gallery->title);

        return redirect()->route('admin.gallery.index')->with('success', 'Foto galeri berhasil ditambahkan.');
    }

    public function edit(Gallery $gallery): View
    {
        Gate::authorize('update', $gallery);

        return view('admin.gallery.edit', ['gallery' => $gallery]);
    }

    public function update(Request $request, Gallery $gallery): RedirectResponse
    {
        Gate::authorize('update', $gallery);

        $data = $this->validated($request, false);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($gallery->image_path);
            $data['image_path'] = $request->file('image')->store('gallery', 'public');
        }

        $gallery->update($data);

        AuditLogger::log('gallery.updated', $gallery, 'Mengubah galeri: '.$gallery->title);

        return redirect()->route('admin.gallery.index')->with('success', 'Galeri berhasil diperbarui.');
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        Gate::authorize('delete', $gallery);

        Storage::disk('public')->delete($gallery->image_path);
        $gallery->delete();

        AuditLogger::log('gallery.deleted', null, 'Menghapus galeri: '.$gallery->title);

        return redirect()->route('admin.gallery.index')->with('success', 'Foto galeri berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, bool $imageRequired): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'caption' => ['required', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
            'image' => [$imageRequired ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_published'] = $request->boolean('is_published');
        unset($data['image']);

        return $data;
    }
}
