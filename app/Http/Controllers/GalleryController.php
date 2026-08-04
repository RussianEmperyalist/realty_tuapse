<?php

namespace App\Http\Controllers;

use App\Models\GalleryAlbum;
use Illuminate\View\View;

class GalleryController extends Controller
{
    /**
     * Display the gallery page.
     */
    public function index(): View
    {
        $legacyGallerySlugs = [
            'tuapse-i-tuapsinskiy-rayon',
            'staryy-tuapse',
            'gde-my-nahodimsya',
        ];
        $legacyGalleryOrder = array_flip($legacyGallerySlugs);

        $legacyAlbums = GalleryAlbum::query()
            ->with(['items' => fn ($query) => $query->where('is_published', true)])
            ->where('is_published', true)
            ->whereIn('slug', $legacyGallerySlugs)
            ->get()
            ->sortBy(fn (GalleryAlbum $album): int => $legacyGalleryOrder[$album->slug] ?? 999)
            ->values();

        return view('gallery.index', [
            'bodyClass' => 'inner_page',
            'albums' => $legacyAlbums->isNotEmpty()
                ? $legacyAlbums
                : GalleryAlbum::query()
                    ->with(['items' => fn ($query) => $query->where('is_published', true)])
                    ->where('is_published', true)
                    ->orderBy('sort_order')
                    ->get(),
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => 'Фотогалерея'],
            ],
        ]);
    }
}
