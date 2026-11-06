<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicNewsController extends Controller
{
    public function index(Request $request): View
    {
        $news = News::query()
            ->published()
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->string('q')->toString();
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%'.$search.'%')
                        ->orWhere('excerpt', 'like', '%'.$search.'%');
                });
            })
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('public.news.index', ['news' => $news]);
    }

    public function show(string $slug): View
    {
        $news = News::query()->published()->where('slug', $slug)->firstOrFail();

        return view('public.news.show', [
            'news' => $news,
            'related' => News::query()
                ->published()
                ->where('id', '!=', $news->id)
                ->latest('published_at')
                ->limit(3)
                ->get(),
        ]);
    }
}
