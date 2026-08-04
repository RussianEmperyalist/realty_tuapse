<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Support\LegalPageContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Render one of the preserved legacy pages or redirect category shortcuts.
     */
    public function show(string $slug): View|RedirectResponse
    {
        $redirects = [
            'poisk-na-karte' => route('search', ['ls' => 'map']),
            'prodazha' => route('search', ['apType' => 2]),
            'novostrojki' => route('search', ['apType' => 2, 'objType' => 5]),
            'arenda' => route('search', ['apType' => 1]),
            'gostinicy' => route('search', ['objType' => 6]),
        ];

        if (isset($redirects[$slug])) {
            return redirect($redirects[$slug], 301);
        }

        $pages = $this->pages();

        abort_unless(isset($pages[$slug]), 404);

        return view('pages.info', [
            'bodyClass' => 'inner_page',
            'page' => $pages[$slug],
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => $pages[$slug]['title']],
            ],
        ]);
    }

    /**
     * Render a legacy section placeholder.
     */
    public function placeholder(string $title): View
    {
        return view('pages.placeholder', [
            'bodyClass' => 'inner_page',
            'title' => $title,
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => $title],
            ],
        ]);
    }

    /**
     * Return static pages that should keep the old public text.
     *
     * @return array<string, array{title:string,content:string}>
     */
    private function pages(): array
    {
        $pages = [
            'politika-konfidencialnosti' => [
                'title' => 'Политика конфиденциальности',
                'content' => LegalPageContent::privacyPolicy(),
            ],
            'polzovatelskoe-soglashenie' => [
                'title' => 'Пользовательское соглашение',
                'content' => LegalPageContent::userAgreement(),
            ],
        ];

        foreach (array_keys($pages) as $slug) {
            $title = SiteSetting::query()->where('key', "page.{$slug}.title")->value('value');
            $content = SiteSetting::query()->where('key', "page.{$slug}.content")->value('value');

            if ($title !== null && $content !== null) {
                $pages[$slug] = [
                    'title' => (string) $title,
                    'content' => (string) $content,
                ];

                continue;
            }

        }

        return $pages;
    }

}
