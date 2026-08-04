<?php

namespace App\Http\Controllers;

use App\Models\NewsPost;
use Illuminate\View\View;

class NewsController extends Controller
{
    /**
     * Display all published news.
     */
    public function index(): View
    {
        return view('news.index', [
            'bodyClass' => 'inner_page',
            'newsPosts' => NewsPost::query()
                ->where('is_published', true)
                ->orderByDesc('published_at')
                ->paginate(10),
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => 'Новости'],
            ],
        ]);
    }

    /**
     * Display the news article.
     */
    public function show(NewsPost $newsPost): View
    {
        return view('news.show', [
            'bodyClass' => 'inner_page',
            'newsPost' => $newsPost,
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => 'Новости', 'url' => route('news.index')],
                ['label' => $newsPost->title],
            ],
        ]);
    }
}
