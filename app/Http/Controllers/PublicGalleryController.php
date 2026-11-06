<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\View\View;

class PublicGalleryController extends Controller
{
    public function index(): View
    {
        return view('public.gallery.index', [
            'galleries' => Gallery::query()
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->latest('id')
                ->paginate(12),
        ]);
    }
}
