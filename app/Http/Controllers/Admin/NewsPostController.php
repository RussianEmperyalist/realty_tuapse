<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsPost;
use App\Support\ImageStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewsPostController extends Controller
{
    public function __construct(
        private readonly ImageStorageService $imageStorage,
    ) {
    }

    /**
     * List news posts.
     */
    public function index(): View
    {
        return view('admin.news.index', [
            'newsPosts' => NewsPost::query()->orderByDesc('published_at')->paginate(20),
        ]);
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        return view('admin.news.form', [
            'newsPost' => new NewsPost(['is_published' => true, 'published_at' => now()]),
            'formAction' => route('admin.news.store'),
            'method' => 'post',
        ]);
    }

    /**
     * Store news post.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['slug'] = $this->uniqueSlug(($data['slug'] ?? '') ?: $data['title']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $this->imageStorage->storePublicFile($request->file('image'), 'news');
        }

        $newsPost = NewsPost::query()->create($data);

        return redirect()
            ->route('admin.news.edit', $newsPost)
            ->with('status', 'Новость создана.');
    }

    /**
     * Show edit form.
     */
    public function edit(NewsPost $news): View
    {
        return view('admin.news.form', [
            'newsPost' => $news,
            'formAction' => route('admin.news.update', $news),
            'method' => 'put',
        ]);
    }

    /**
     * Update news post.
     */
    public function update(Request $request, NewsPost $news): RedirectResponse
    {
        $data = $this->validatedData($request, $news);
        $data['slug'] = $this->uniqueSlug(($data['slug'] ?? '') ?: $data['title'], $news->id);

        if ($request->boolean('delete_image')) {
            $this->deletePublicPath($news->image_path);
            $data['image_path'] = null;
        }

        if ($request->hasFile('image')) {
            $this->deletePublicPath($news->image_path);
            $data['image_path'] = $this->imageStorage->storePublicFile($request->file('image'), 'news');
        }

        $news->update($data);

        return redirect()
            ->route('admin.news.edit', $news)
            ->with('status', 'Новость обновлена.');
    }

    /**
     * Delete news post.
     */
    public function destroy(NewsPost $news): RedirectResponse
    {
        $this->deletePublicPath($news->image_path);
        $news->delete();

        return redirect()
            ->route('admin.news.index')
            ->with('status', 'Новость удалена.');
    }

    /**
     * Validation rules.
     *
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?NewsPost $newsPost = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:news_posts,slug' . ($newsPost ? ',' . $newsPost->id : '')],
            'legacy_path' => ['nullable', 'string', 'max:255', 'unique:news_posts,legacy_path' . ($newsPost ? ',' . $newsPost->id : '')],
            'excerpt' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'image' => ['nullable', 'image', 'max:8192'],
        ]);
    }

    /**
     * Unique slug builder.
     */
    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value);
        $slug = $baseSlug !== '' ? $baseSlug : 'news';
        $counter = 1;

        while (NewsPost::query()->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }

    /**
     * Delete file from the public disk.
     */
    private function deletePublicPath(?string $path): void
    {
        if ($path === null || !str_starts_with($path, 'storage/')) {
            return;
        }

        Storage::disk('public')->delete(Str::after($path, 'storage/'));
    }
}
