<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryAlbum;
use App\Models\GalleryItem;
use App\Support\ImageStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GalleryAlbumController extends Controller
{
    public function __construct(
        private readonly ImageStorageService $imageStorage,
    ) {
    }

    /**
     * Display albums.
     */
    public function index(): View
    {
        return view('admin.gallery.index', [
            'albums' => GalleryAlbum::query()->withCount('items')->orderBy('sort_order')->paginate(20),
        ]);
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        return view('admin.gallery.form', [
            'album' => new GalleryAlbum(['is_published' => true]),
            'formAction' => route('admin.gallery.store'),
            'method' => 'post',
        ]);
    }

    /**
     * Store album.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['slug'] = $this->uniqueSlug(($data['slug'] ?? '') ?: $data['title']);

        if ($request->hasFile('cover_image')) {
            $data['cover_image_path'] = $this->imageStorage->storePublicFile(
                $request->file('cover_image'),
                'gallery/covers',
            );
        }

        $album = GalleryAlbum::query()->create($data);
        $this->syncItems($request, $album);

        return redirect()
            ->route('admin.gallery.edit', $album)
            ->with('status', 'Альбом создан.');
    }

    /**
     * Show edit form.
     */
    public function edit(GalleryAlbum $gallery): View
    {
        $gallery->load('items');

        return view('admin.gallery.form', [
            'album' => $gallery,
            'formAction' => route('admin.gallery.update', $gallery),
            'method' => 'put',
        ]);
    }

    /**
     * Update album.
     */
    public function update(Request $request, GalleryAlbum $gallery): RedirectResponse
    {
        $data = $this->validatedData($request, $gallery);
        $data['slug'] = $this->uniqueSlug(($data['slug'] ?? '') ?: $data['title'], $gallery->id);

        if ($request->boolean('delete_cover_image')) {
            $this->deletePublicPath($gallery->cover_image_path);
            $data['cover_image_path'] = null;
        }

        if ($request->hasFile('cover_image')) {
            $this->deletePublicPath($gallery->cover_image_path);
            $data['cover_image_path'] = $this->imageStorage->storePublicFile(
                $request->file('cover_image'),
                'gallery/covers',
            );
        }

        $gallery->update($data);
        $this->syncItems($request, $gallery);

        return redirect()
            ->route('admin.gallery.edit', $gallery)
            ->with('status', 'Альбом обновлен.');
    }

    /**
     * Delete album.
     */
    public function destroy(GalleryAlbum $gallery): RedirectResponse
    {
        $gallery->load('items');

        foreach ($gallery->items as $item) {
            $this->deletePublicPath($item->image_path);
            $this->deletePublicPath($item->thumb_path);
        }

        $this->deletePublicPath($gallery->cover_image_path);
        $gallery->delete();

        return redirect()
            ->route('admin.gallery.index')
            ->with('status', 'Альбом удален.');
    }

    /**
     * Validation rules.
     *
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?GalleryAlbum $gallery = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:gallery_albums,slug' . ($gallery ? ',' . $gallery->id : '')],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
            'cover_image' => ['nullable', 'image', 'max:8192'],
            'items.*' => ['nullable', 'image', 'max:8192'],
            'delete_items' => ['nullable', 'array'],
            'delete_items.*' => ['integer', 'exists:gallery_items,id'],
            'item_titles' => ['nullable', 'array'],
            'item_titles.*' => ['nullable', 'string', 'max:255'],
            'item_sort_orders' => ['nullable', 'array'],
            'item_sort_orders.*' => ['nullable', 'integer', 'min:0'],
            'item_published' => ['nullable', 'array'],
        ]);
    }

    /**
     * Sync album items.
     */
    private function syncItems(Request $request, GalleryAlbum $gallery): void
    {
        $gallery->load('items');

        foreach ((array) $request->input('delete_items', []) as $itemId) {
            $item = $gallery->items->firstWhere('id', (int) $itemId);
            if ($item === null) {
                continue;
            }

            $this->deletePublicPath($item->image_path);
            $this->deletePublicPath($item->thumb_path);
            $item->delete();
        }

        foreach ($gallery->items as $item) {
            $item->forceFill([
                'title' => $request->input('item_titles.' . $item->id),
                'sort_order' => (int) $request->input('item_sort_orders.' . $item->id, $item->sort_order),
                'is_published' => in_array((string) $item->id, array_map('strval', (array) $request->input('item_published', [])), true),
            ])->save();
        }

        if ($request->hasFile('items')) {
            $sortOrder = ((int) GalleryItem::query()->where('gallery_album_id', $gallery->id)->max('sort_order')) + 1;
            foreach ($request->file('items', []) as $file) {
                if ($file === null) {
                    continue;
                }

                $storedImage = $this->imageStorage->storePublicImageWithThumbnail(
                    $file,
                    'gallery/items',
                    'gallery/items/thumbs',
                    960,
                    640,
                );

                GalleryItem::query()->create([
                    'gallery_album_id' => $gallery->id,
                    'title' => $gallery->title,
                    'image_path' => $storedImage['path'],
                    'thumb_path' => $storedImage['thumb_path'],
                    'sort_order' => $sortOrder++,
                    'is_published' => true,
                ]);
            }
        }
    }

    /**
     * Generate a unique slug.
     */
    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value);
        $slug = $baseSlug !== '' ? $baseSlug : 'gallery';
        $counter = 1;

        while (GalleryAlbum::query()->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }

    /**
     * Delete file from public disk.
     */
    private function deletePublicPath(?string $path): void
    {
        if ($path === null || !str_starts_with($path, 'storage/')) {
            return;
        }

        Storage::disk('public')->delete(Str::after($path, 'storage/'));
    }
}
