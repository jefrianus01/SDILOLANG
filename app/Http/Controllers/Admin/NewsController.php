<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', News::class);

        return view('admin.news.index', [
            'news' => News::query()->with('author')->latest('created_at')->paginate(10),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', News::class);

        return view('admin.news.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', News::class);

        $data = $this->validated($request);
        $data['slug'] = News::makeSlug($data['title']);
        $data['author_id'] = $request->user()->id;

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('news', 'public');
        }

        $news = News::create($data);

        AuditLogger::log('news.created', $news, 'Membuat berita: '.$news->title);

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil dibuat.');
    }

    public function edit(News $news): View
    {
        Gate::authorize('update', $news);

        return view('admin.news.edit', ['news' => $news]);
    }

    public function update(Request $request, News $news): RedirectResponse
    {
        Gate::authorize('update', $news);

        $data = $this->validated($request);

        if ($data['title'] !== $news->title) {
            $data['slug'] = News::makeSlug($data['title'], $news->id);
        }

        if ($request->hasFile('image')) {
            if ($news->image_path) {
                Storage::disk('public')->delete($news->image_path);
            }

            $data['image_path'] = $request->file('image')->store('news', 'public');
        }

        $news->update($data);

        AuditLogger::log('news.updated', $news, 'Mengubah berita: '.$news->title);

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(News $news): RedirectResponse
    {
        Gate::authorize('delete', $news);

        if ($news->image_path) {
            Storage::disk('public')->delete($news->image_path);
        }

        $news->delete();

        AuditLogger::log('news.deleted', null, 'Menghapus berita: '.$news->title);

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['published_at'] ?? ($data['is_published'] ? now() : null);
        unset($data['image']);

        return $data;
    }
}
