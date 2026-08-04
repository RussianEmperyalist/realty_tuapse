<?php

namespace App\Http\Controllers;

use App\Support\LegacyContentImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentController extends Controller
{
    /**
     * Frequently asked questions list from the legacy site.
     */
    public function faqIndex(): View
    {
        return view('content.faq-index', [
            'bodyClass' => 'inner_page',
            'entries' => $this->faqEntries(),
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => 'Вопросы-ответы'],
            ],
        ]);
    }

    /**
     * FAQ article page.
     */
    public function faqShow(string $slug): View
    {
        $entry = collect($this->faqEntries())->firstWhere('slug', $slug);
        abort_if($entry === null, 404);

        return view('content.article-show', [
            'bodyClass' => 'inner_page',
            'entry' => $entry,
            'sectionTitle' => 'Вопросы-ответы',
            'backRoute' => route('faq'),
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => 'Вопросы-ответы', 'url' => route('faq')],
                ['label' => $entry['title']],
            ],
        ]);
    }

    /**
     * Reviews page with preserved external links.
     */
    public function review(): View
    {
        return view('content.review', [
            'bodyClass' => 'inner_page',
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => 'Отзывы'],
            ],
        ]);
    }

    /**
     * Client-side favorites page.
     */
    public function favorites(): View
    {
        return view('content.favorites', [
            'bodyClass' => 'inner_page',
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => 'Избранное'],
            ],
        ]);
    }

    /**
     * Legacy articles list.
     */
    public function articlesIndex(): View
    {
        return view('content.articles-index', [
            'bodyClass' => 'inner_page',
            'entries' => $this->articleEntries(),
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => 'Статьи'],
            ],
        ]);
    }

    /**
     * Legacy article page.
     */
    public function articlesShow(string $slug): View
    {
        $entry = collect($this->articleEntries())->firstWhere('slug', $slug);
        abort_if($entry === null, 404);

        return view('content.article-show', [
            'bodyClass' => 'inner_page',
            'entry' => $entry,
            'sectionTitle' => 'Статьи',
            'backRoute' => route('articles.index'),
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => 'Статьи', 'url' => route('articles.index')],
                ['label' => $entry['title']],
            ],
        ]);
    }

    /**
     * Preserve the city landing for Tuapse.
     */
    public function tuapse(): RedirectResponse
    {
        return redirect()->route('search', ['city' => [9]]);
    }

    /**
     * Preserve the district landing.
     */
    public function tuapsinskyDistrict(): RedirectResponse
    {
        return redirect()->route('search', ['city' => [10]]);
    }

    /**
     * The old "add listing" URL now points to the new managed backend.
     */
    public function guestAdd(Request $request): RedirectResponse
    {
        if ($request->user() !== null) {
            return redirect()->route('admin.properties.create');
        }

        return redirect()->route('login');
    }

    /**
     * Preserve the old information landing page from the legacy copy.
     */
    public function information(): View
    {
        $page = (new LegacyContentImporter())->importStandalonePage('informaciya') ?? [
            'title' => 'Информация',
            'content' => '<p>Раздел информации перенесен в новую версию сайта и использует актуальные маршруты.</p>',
        ];

        return view('pages.info', [
            'bodyClass' => 'inner_page',
            'page' => $page,
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => $page['title']],
            ],
        ]);
    }

    /**
     * Preserve the old sitemap page from the legacy copy.
     */
    public function sitemap(): View
    {
        $page = (new LegacyContentImporter())->importStandalonePage('sitemap') ?? [
            'title' => 'Карта сайта',
            'content' => '<p>Карта сайта обновлена и продолжает вести в действующие разделы новой версии проекта.</p>',
        ];

        return view('pages.info', [
            'bodyClass' => 'inner_page',
            'page' => $page,
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => $page['title']],
            ],
        ]);
    }

    /**
     * XML sitemap for search engines.
     */
    public function sitemapXml(): \Illuminate\Http\Response
    {
        $properties = \App\Models\Property::query()
            ->where('is_published', true)
            ->select('slug', 'updated_at')
            ->get();

        $news = \App\Models\NewsPost::query()
            ->where('is_published', true)
            ->select('slug', 'updated_at')
            ->get();

        $galleryAlbums = \App\Models\GalleryAlbum::query()
            ->where('is_published', true)
            ->select('slug', 'updated_at')
            ->get();

        $content = view('content.sitemap', [
            'properties' => $properties,
            'news' => $news,
            'galleryAlbums' => $galleryAlbums,
            'faqEntries' => $this->faqEntries(),
            'articles' => $this->articleEntries(),
        ])->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }

    /**
     * Static FAQ entries taken from the legacy public copy.
     *
     * @return list<array<string, string>>
     */
    private function faqEntries(): array
    {
        $imported = (new LegacyContentImporter())->importFaqEntries();

        if ($imported !== []) {
            return $imported;
        }

        return [
            [
                'slug' => 'luchshie-goroda-dlja-holostjakov',
                'title' => 'Лучшие города для холостяков',
                'date' => '5 января 2019 г.',
                'excerpt' => 'Короткий обзор городов, в которых удобно жить, работать и быстро решать бытовые вопросы.',
                'body' => '<p>Этот материал сохранен из старой структуры сайта как информационный раздел. В новой версии проекта он остается доступным по прежней логике навигации, но уже на новом backend.</p><p>При подборе жилья для одного человека важны транспортная доступность, возможность быстро найти подходящий объект и понятная инфраструктура района. В Туапсе и районе мы рекомендуем смотреть не только на цену, но и на удобство повседневной жизни: магазины, остановки, парковку и уровень шума.</p>',
            ],
            [
                'slug' => 'nailuchshie-goroda-dlja-semi',
                'title' => 'Наилучшие города для семьи',
                'date' => '5 января 2019 г.',
                'excerpt' => 'На что обращать внимание при покупке семейного жилья: школы, поликлиники, двор и спокойствие района.',
                'body' => '<p>Семейная недвижимость оценивается шире, чем просто площадь квартиры. Важны школы, детские сады, медицинские учреждения и качество самого двора.</p><p>При подборе семейного жилья в Туапсе удобно опираться на связку из цены, площади, этажности дома и реального состояния объекта. В новой версии сайта эти параметры останутся в привычной логике фильтров.</p>',
            ],
        ];
    }

    /**
     * Static article entries from the legacy copy.
     *
     * @return list<array<string, string>>
     */
    private function articleEntries(): array
    {
        $imported = (new LegacyContentImporter())->importArticles();

        if ($imported !== []) {
            return $imported;
        }

        return [
            [
                'slug' => 'nauluchshie-dlja-prozhivanija-goroda',
                'title' => 'Наилучшие для проживания города',
                'date' => '5 января 2019 г.',
                'excerpt' => 'Подборка городов с комфортной средой для жизни и понятными сценариями выбора недвижимости.',
                'image' => 'legacy/uploads/entries/thumb_531x256_sunset-17665_640.jpg',
                'body' => '<p>Информационный раздел перенесен из старой копии сайта без изменения URL-логики. Мы сохранили доступность материалов, чтобы привычная навигация не ломалась после миграции.</p><p>При выборе города для жизни важно смотреть не только на стоимость объекта, но и на транспорт, наличие рабочих мест, доступность социальных сервисов и качество городской среды.</p>',
            ],
            [
                'slug' => 'kakie-veshchi-vo-vremja-pereezda-lomajutsja-chashche-vsego',
                'title' => 'Какие вещи во время переезда ломаются чаще всего?',
                'date' => '5 января 2019 г.',
                'excerpt' => 'Небольшая памятка по переезду: как подготовить вещи, чтобы избежать неприятных потерь.',
                'image' => 'legacy/themes/dolphin/assets/images/no_photo_entry.png',
                'body' => '<p>Даже информационные статьи на сайте агентства поддерживают клиентский сценарий выбора жилья. Поэтому этот раздел тоже оставлен в публичной части нового проекта.</p><p>Во время переезда чаще всего страдают посуда, бытовая техника, зеркала и мелкие предметы интерьера. Продуманная упаковка и четкая маркировка коробок помогают избежать повреждений и сделать переезд заметно спокойнее.</p>',
            ],
        ];
    }
}
