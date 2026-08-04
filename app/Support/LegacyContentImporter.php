<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Illuminate\Support\Str;

class LegacyContentImporter
{
    public function __construct(
        private readonly ?string $legacyRoot = null,
    ) {
    }

    /**
     * Check whether the old Cyotek copy is available near the Laravel project.
     */
    public function isAvailable(): bool
    {
        return $this->resolveLegacyRoot() !== null;
    }

    /**
     * Import a legacy static page.
     *
     * @return array{slug:string,title:string,content:string}|null
     */
    public function importPage(string $slug): ?array
    {
        return $this->importStaticPageByPath("page/{$slug}.html", $slug);
    }

    /**
     * Import a standalone root-level page like /informaciya.html or /sitemap.html.
     *
     * @return array{slug:string,title:string,content:string}|null
     */
    public function importStandalonePage(string $slug): ?array
    {
        return $this->importStaticPageByPath("{$slug}.html", $slug);
    }

    /**
     * Import every news article from the legacy copy.
     *
     * @return list<array<string, mixed>>
     */
    public function importNewsPosts(): array
    {
        $directory = $this->legacyPath('news');

        if ($directory === null || ! is_dir($directory)) {
            return [];
        }

        $files = glob($directory . DIRECTORY_SEPARATOR . '*.html') ?: [];
        sort($files, SORT_NATURAL | SORT_FLAG_CASE);

        $posts = [];

        foreach ($files as $file) {
            $post = $this->importNewsPost($file);

            if ($post !== null) {
                $posts[] = $post;
            }
        }

        usort(
            $posts,
            static fn (array $left, array $right): int => strcmp(
                (string) ($right['published_at'] ?? ''),
                (string) ($left['published_at'] ?? ''),
            ),
        );

        return $posts;
    }

    /**
     * Import legacy article entries from /articles.
     *
     * @return list<array<string, mixed>>
     */
    public function importArticles(): array
    {
        return $this->importEntryDirectory('articles', 'article');
    }

    /**
     * Import legacy FAQ entries from /faq.
     *
     * @return list<array<string, mixed>>
     */
    public function importFaqEntries(): array
    {
        return $this->importEntryDirectory('faq', 'faq');
    }

    /**
     * Import every real estate object from /property.
     *
     * @return list<array<string, mixed>>
     */
    public function importProperties(): array
    {
        $directory = $this->legacyPath('property');

        if ($directory === null || ! is_dir($directory)) {
            return [];
        }

        $files = glob($directory . DIRECTORY_SEPARATOR . '*.html') ?: [];
        $files = array_values(array_filter(
            $files,
            fn (string $file): bool => ! $this->isDuplicatePropertyPage($file),
        ));

        sort($files, SORT_NATURAL | SORT_FLAG_CASE);

        $featuredIds = $this->featuredPropertyIds();
        $properties = [];

        foreach ($files as $file) {
            $property = $this->importProperty($file, $featuredIds);

            if ($property !== null) {
                $properties[] = $property;
            }
        }

        usort(
            $properties,
            static fn (array $left, array $right): int => ((int) $left['legacy_id']) <=> ((int) $right['legacy_id']),
        );

        return $properties;
    }

    /**
     * Import original photo gallery albums from /uploads/gallery.
     *
     * @return list<array<string, mixed>>
     */
    public function importGalleryAlbums(): array
    {
        $albums = [
            [
                'title' => 'Туапсе и Туапсинский район',
                'slug' => 'tuapse-i-tuapsinskiy-rayon',
                'directory' => 'new',
                'description' => 'Фотографии Туапсе и Туапсинского района из оригинальной фотогалереи.',
                'sort_order' => 1,
                'item_title_prefix' => 'Туапсе и Туапсинский район',
            ],
            [
                'title' => 'Старый Туапсе',
                'slug' => 'staryy-tuapse',
                'directory' => 'old',
                'description' => 'Исторические фотографии старого Туапсе.',
                'sort_order' => 2,
                'item_title_prefix' => 'Старый Туапсе',
            ],
            [
                'title' => 'Где мы находимся',
                'slug' => 'gde-my-nahodimsya',
                'directory' => 'we',
                'description' => 'Фотографии офиса и ориентиров агентства недвижимости "Туапсе".',
                'sort_order' => 3,
                'item_title_prefix' => 'Где мы находимся',
            ],
        ];

        $importedAlbums = [];

        foreach ($albums as $album) {
            $items = $this->galleryItems((string) $album['directory'], (string) $album['item_title_prefix']);

            if ($items === []) {
                continue;
            }

            unset($album['directory'], $album['item_title_prefix']);

            $album['cover_image_path'] = $items[0]['image_path'];
            $album['items'] = $items;
            $album['is_published'] = true;

            $importedAlbums[] = $album;
        }

        return $importedAlbums;
    }

    /**
     * Import one news article from the legacy copy.
     *
     * @return array<string, mixed>|null
     */
    private function importNewsPost(string $file): ?array
    {
        [$dom, $xpath] = $this->createDom(file_get_contents($file) ?: '');

        $slug = pathinfo($file, PATHINFO_FILENAME);
        $title = $this->firstText($xpath, '//h1[1]') ?: $this->firstText($xpath, '//title');
        $bodyNode = $xpath->query(
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' entry-page-body ')]"
        )->item(0);

        if ($title === null || ! $bodyNode instanceof DOMElement) {
            return null;
        }

        $body = $this->sanitizeHtml($this->innerHtml($bodyNode));
        $body = preg_replace(
            '~<p>\s*(?:&nbsp;|\xC2\xA0|\s)*Назад к списку\s*</p>~iu',
            '',
            $body,
        ) ?? $body;
        $body = trim($body);

        $imageCandidate = $this->firstAttribute(
            $xpath,
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' entries-image ')]//a[1]",
            'href',
        ) ?: $this->firstAttribute(
            $xpath,
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' entries-image ')]//img[1]",
            'src',
        );

        $imagePath = $this->normalizeAssetPath($imageCandidate);
        if ($imagePath !== null && ! MediaPath::exists($imagePath)) {
            $imagePath = null;
        }

        $publishedAt = $this->extractPublishedAt($xpath);
        $plainText = Str::of(strip_tags(html_entity_decode($body, ENT_QUOTES | ENT_HTML5, 'UTF-8')))
            ->replace("\xc2\xa0", ' ')
            ->squish();

        return [
            'title' => html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'slug' => $slug,
            'legacy_path' => "news/{$slug}",
            'excerpt' => Str::limit((string) $plainText, 230),
            'body' => $body,
            'image_path' => $imagePath,
            'is_published' => true,
            'published_at' => $publishedAt?->toDateTimeString(),
        ];
    }

    /**
     * Import one legacy real estate detail page.
     *
     * @param array<int, bool> $featuredIds
     *
     * @return array<string, mixed>|null
     */
    private function importProperty(string $file, array $featuredIds): ?array
    {
        $html = file_get_contents($file) ?: '';
        [$dom, $xpath] = $this->createDom($html);
        $rows = $this->propertyRows($xpath);

        $legacyId = $this->integerValue($this->propertyRowText($rows, 'Уникальный номер объявления'))
            ?? $this->legacyObjectIdFromHtml($html);

        if ($legacyId === null) {
            return null;
        }

        $title = $this->firstText(
            $xpath,
            "//h1[contains(concat(' ', normalize-space(@class), ' '), ' title_property ')]"
        ) ?: $this->firstText($xpath, '//h1[1]');

        if ($title === null) {
            return null;
        }

        $priceInfo = $this->propertyPriceInfo($rows, $html);
        $floorInfo = $this->floorInfo($this->propertyRowText($rows, 'Этаж'));
        $owner = $this->propertyOwner($xpath, $html);
        $coordinates = $this->propertyCoordinates($xpath);
        $address = $this->propertyRowText($rows, 'Адрес');

        return [
            'legacy_id' => $legacyId,
            'legacy_owner_id' => $owner['legacy_id'],
            'legacy_owner_name' => $owner['name'],
            'legacy_owner_photo_path' => $owner['photo_path'],
            'title' => html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'slug' => pathinfo($file, PATHINFO_FILENAME),
            'deal_type' => $this->dealType($this->propertyRowText($rows, 'Тип сделки')),
            'property_type' => $this->propertyType($this->propertyRowText($rows, 'Тип недвижимости')),
            'city' => $this->propertyCity($address),
            'address' => $address,
            'price' => $priceInfo['price'],
            'price_label' => $priceInfo['label'],
            'currency' => $priceInfo['currency'],
            'rooms' => $this->integerValue($this->propertyRowText($rows, 'Количество комнат')),
            'floor' => $floorInfo['floor'],
            'floors_total' => $floorInfo['floors_total'],
            'square' => $this->decimalValue($this->propertyRowText($rows, 'Общая площадь')),
            'windows' => $this->propertyRowText($rows, 'Окна'),
            'description' => $this->textFromHtml((string) ($this->propertyRowHtml($rows, 'Описание') ?? '')),
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
            'phone_override' => null,
            'is_published' => true,
            'is_featured' => isset($featuredIds[$legacyId]),
            'published_at' => CarbonImmutable::create(2026, 4, 20, 12, 0, 0)
                ->addMinutes($legacyId)
                ->toDateTimeString(),
            'images' => $this->propertyImages($html, $legacyId),
        ];
    }

    /**
     * Decide whether the file is the compact duplicate saved as *-1.html.
     */
    private function isDuplicatePropertyPage(string $file): bool
    {
        $slug = pathinfo($file, PATHINFO_FILENAME);

        if (preg_match('~-\d+$~', $slug) !== 1) {
            return false;
        }

        $baseSlug = (string) preg_replace('~-\d+$~', '', $slug);

        return is_file(dirname($file) . DIRECTORY_SEPARATOR . $baseSlug . '.html');
    }

    /**
     * Extract table rows from the property characteristics block.
     *
     * @return array<string, array{text:string,html:string}>
     */
    private function propertyRows(DOMXPath $xpath): array
    {
        $rows = [];
        $nodes = $xpath->query('//tr');

        foreach ($nodes as $row) {
            if (! $row instanceof DOMElement) {
                continue;
            }

            $cells = $xpath->query('./td', $row);

            if ($cells->length < 2) {
                continue;
            }

            $labelNode = $cells->item(0);
            $valueNode = $cells->item(1);

            if (! $labelNode instanceof DOMNode || ! $valueNode instanceof DOMNode) {
                continue;
            }

            $label = trim($this->normalizeText($labelNode->textContent), ":\xc2\xa0 ");

            if ($label === '') {
                continue;
            }

            $rows[$label] = [
                'text' => $this->normalizeText($valueNode->textContent),
                'html' => $this->innerHtml($valueNode),
            ];
        }

        return $rows;
    }

    /**
     * Return text for a property row whose label contains a phrase.
     *
     * @param array<string, array{text:string,html:string}> $rows
     */
    private function propertyRowText(array $rows, string $needle): ?string
    {
        $row = $this->propertyRow($rows, $needle);

        return $row['text'] ?? null;
    }

    /**
     * Return HTML for a property row whose label contains a phrase.
     *
     * @param array<string, array{text:string,html:string}> $rows
     */
    private function propertyRowHtml(array $rows, string $needle): ?string
    {
        $row = $this->propertyRow($rows, $needle);

        return $row['html'] ?? null;
    }

    /**
     * Return a property row whose label contains a phrase.
     *
     * @param array<string, array{text:string,html:string}> $rows
     *
     * @return array{text:string,html:string}|null
     */
    private function propertyRow(array $rows, string $needle): ?array
    {
        $needle = Str::lower($needle);

        foreach ($rows as $label => $row) {
            if (str_contains(Str::lower($label), $needle)) {
                return $row;
            }
        }

        return null;
    }

    /**
     * Extract the object id from media paths as a fallback.
     */
    private function legacyObjectIdFromHtml(string $html): ?int
    {
        if (preg_match('~uploads/objects/(\d+)/~', $html, $matches) !== 1) {
            return null;
        }

        return (int) $matches[1];
    }

    /**
     * Extract owner metadata from the property sidebar.
     *
     * @return array{legacy_id:int|null,name:string|null,photo_path:string|null}
     */
    private function propertyOwner(DOMXPath $xpath, string $html): array
    {
        $legacyId = null;

        if (preg_match('~users/view(?:-\d+)?\.html\?id=(\d+)~', $html, $matches) === 1) {
            $legacyId = (int) $matches[1];
        }

        $photo = $this->firstAttribute(
            $xpath,
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' agent_info ')]"
            . "//img[contains(concat(' ', normalize-space(@class), ' '), ' message_ava ')][1]",
            'src',
        );

        $name = $this->firstAttribute(
            $xpath,
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' agent_info ')]"
            . "//img[contains(concat(' ', normalize-space(@class), ' '), ' message_ava ')][1]",
            'alt',
        ) ?: $this->firstText(
            $xpath,
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' agent_info ')]//li[contains(concat(' ', normalize-space(@class), ' '), ' h4 ')][1]"
        );

        return [
            'legacy_id' => $legacyId,
            'name' => $name === null ? null : html_entity_decode($name, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'photo_path' => $this->normalizeAssetPath($photo),
        ];
    }

    /**
     * Parse price, label, and currency from the property page.
     *
     * @param array<string, array{text:string,html:string}> $rows
     *
     * @return array{price:int|null,label:string|null,currency:string}
     */
    private function propertyPriceInfo(array $rows, string $pageHtml): array
    {
        $row = $this->propertyRow($rows, 'Цена');
        $html = (string) ($row['html'] ?? '');
        $text = (string) ($row['text'] ?? '');
        $label = null;
        $currency = 'руб.';

        if ($html !== '') {
            [$dom, $xpath] = $this->createDom($html);
            $label = $this->firstText($xpath, '//span[not(contains(concat(" ", normalize-space(@class), " "), " currency "))][1]');
            $currency = $this->firstText($xpath, '//span[contains(concat(" ", normalize-space(@class), " "), " currency ")][1]')
                ?: $currency;
        }

        if ($label === null && $text !== '') {
            $label = trim((string) preg_replace('~\s*(?:руб\.|Продажа|Аренда|в месяц).*$~u', '', $text));
        }

        return [
            'price' => $this->propertyJsonLdPrice($text, $pageHtml),
            'label' => $label,
            'currency' => $currency,
        ];
    }

    /**
     * Convert a Russian deal label into the internal enum.
     */
    private function dealType(?string $value): string
    {
        $value = Str::lower((string) $value);

        return str_contains($value, 'сдам') || str_contains($value, 'аренд')
            ? 'rent'
            : 'sale';
    }

    /**
     * Convert a Russian object type label into the internal enum.
     */
    private function propertyType(?string $value): string
    {
        $value = Str::lower((string) $value);

        return match (true) {
            str_contains($value, 'комната') => 'room',
            str_contains($value, 'дом') => 'house',
            str_contains($value, 'зем') => 'land',
            str_contains($value, 'гостини') => 'hotel',
            str_contains($value, 'гараж') => 'garage',
            str_contains($value, 'новост') => 'new_building',
            str_contains($value, 'коммер') => 'commercial',
            default => 'apartment',
        };
    }

    /**
     * Infer city filter from the address.
     */
    private function propertyCity(?string $address): ?string
    {
        $address = Str::lower((string) $address);

        if (str_contains($address, 'туапсинский район')) {
            return 'tuapsinskij-rajon';
        }

        if (str_contains($address, 'туапсе')) {
            return 'tuapse';
        }

        return null;
    }

    /**
     * Parse floor and total floor values.
     *
     * @return array{floor:int|null,floors_total:int|null}
     */
    private function floorInfo(?string $value): array
    {
        $value = (string) $value;

        if (preg_match('~(\d+)\s*/\s*(\d+)~u', $value, $matches) === 1) {
            return [
                'floor' => (int) $matches[1],
                'floors_total' => (int) $matches[2],
            ];
        }

        if (preg_match('~(\d+)\s*этаж\s*(\d+)~u', $value, $matches) === 1) {
            return [
                'floor' => (int) $matches[1],
                'floors_total' => (int) $matches[2],
            ];
        }

        return [
            'floor' => $this->integerValue($value),
            'floors_total' => null,
        ];
    }

    /**
     * Extract coordinates from JSON-LD.
     *
     * @return array{latitude:float|null,longitude:float|null}
     */
    private function propertyCoordinates(DOMXPath $xpath): array
    {
        foreach ($this->jsonLdBlocks($xpath) as $block) {
            $coordinates = $this->findGeoCoordinates($block);

            if ($coordinates !== null) {
                return $coordinates;
            }
        }

        return ['latitude' => null, 'longitude' => null];
    }

    /**
     * Extract full-size property images and matching thumbnails.
     *
     * @return list<array{path:string,thumb_path:string|null}>
     */
    private function propertyImages(string $html, int $legacyId): array
    {
        preg_match_all(
            '~(?:https?://[^/]+/|\.\./|/)?uploads/objects/' . preg_quote((string) $legacyId, '~') . '/modified/full_[^"\']+~i',
            $html,
            $matches,
        );

        $paths = [];

        foreach ($matches[0] ?? [] as $path) {
            $normalized = $this->normalizeAssetPath($path);

            if ($normalized !== null && ! in_array($normalized, $paths, true)) {
                $paths[] = $normalized;
            }
        }

        return array_map(fn (string $path): array => [
            'path' => $path,
            'thumb_path' => $this->matchingPropertyThumb($path),
        ], $paths);
    }

    /**
     * Find the best available thumbnail for a full property image.
     */
    private function matchingPropertyThumb(string $path): ?string
    {
        $filename = basename($path);

        if (! str_starts_with($filename, 'full_')) {
            return null;
        }

        $suffix = substr($filename, 5);
        $directory = trim(dirname($path), '.');

        foreach (['thumb_205x107_', 'thumb_359x286_', 'thumb_300x200_', 'thumb_150x100_', 'thumb_60x45_'] as $prefix) {
            $candidate = str_replace('\\', '/', $directory . '/' . $prefix . $suffix);

            if (MediaPath::exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Build gallery image rows from one legacy gallery directory.
     *
     * @return list<array<string, mixed>>
     */
    private function galleryItems(string $directoryName, string $titlePrefix): array
    {
        $directory = $this->galleryDirectory($directoryName);

        if ($directory === null) {
            return [];
        }

        $files = glob($directory . DIRECTORY_SEPARATOR . '[0-9][0-9][0-9].jpg') ?: [];
        sort($files, SORT_NATURAL | SORT_FLAG_CASE);

        $items = [];

        foreach ($files as $index => $file) {
            $filename = basename($file);
            $number = pathinfo($filename, PATHINFO_FILENAME);
            $imagePath = "legacy/uploads/gallery/{$directoryName}/{$filename}";
            $thumbPath = "legacy/uploads/gallery/{$directoryName}/{$number}_thumb.jpg";

            $items[] = [
                'title' => $titlePrefix . ' - фото ' . ((int) $number),
                'image_path' => $imagePath,
                'thumb_path' => MediaPath::exists($thumbPath) ? $thumbPath : $imagePath,
                'sort_order' => $index + 1,
                'is_published' => true,
            ];
        }

        return $items;
    }

    /**
     * Resolve the gallery source directory, preferring legacy-source, falling back to public/legacy.
     */
    private function galleryDirectory(string $directoryName): ?string
    {
        $candidates = [
            $this->legacyPath('uploads/gallery/' . $directoryName),
            public_path('legacy/uploads/gallery/' . $directoryName),
        ];

        foreach ($candidates as $candidate) {
            if ($candidate !== null && is_dir($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Extract property ids from the original hot offers block.
     *
     * @return array<int, bool>
     */
    private function featuredPropertyIds(): array
    {
        $file = $this->legacyPath('index.htm');

        if ($file === null || ! is_file($file)) {
            return [];
        }

        $html = file_get_contents($file) ?: '';
        $featuredHtml = $html;

        if (preg_match('~<div class="box hot_block catalog">(?P<html>.*?)(?:<div class="box news_block"|<script|\z)~is', $html, $matches) === 1) {
            $featuredHtml = $matches['html'];
        }

        preg_match_all('~data-ap-id="(\d+)"~', $featuredHtml, $matches);

        return array_fill_keys(array_map('intval', $matches[1] ?? []), true);
    }

    /**
     * Extract a numeric price from JSON-LD or visible price text.
     */
    private function propertyJsonLdPrice(string $text, string $html): ?int
    {
        if (preg_match('~"price"\s*:\s*"?(\d+)"?~', $html, $matches) === 1) {
            return (int) $matches[1];
        }

        if (preg_match('~(\d+(?:[.,]\d+)?)\s*млн~iu', $text, $matches) === 1) {
            return (int) round(((float) str_replace(',', '.', $matches[1])) * 1000000);
        }

        if (preg_match('~(\d+(?:[.,]\d+)?)\s*тыс~iu', $text, $matches) === 1) {
            return (int) round(((float) str_replace(',', '.', $matches[1])) * 1000);
        }

        return $this->integerValue($text);
    }

    /**
     * Parse the first integer from a string.
     */
    private function integerValue(?string $value): ?int
    {
        if (preg_match('~\d+~', (string) $value, $matches) !== 1) {
            return null;
        }

        return (int) $matches[0];
    }

    /**
     * Parse the first decimal number from a string.
     */
    private function decimalValue(?string $value): ?float
    {
        if (preg_match('~\d+(?:[.,]\d+)?~', (string) $value, $matches) !== 1) {
            return null;
        }

        return (float) str_replace(',', '.', $matches[0]);
    }

    /**
     * Convert an HTML fragment to readable plain text.
     */
    private function textFromHtml(string $html): ?string
    {
        if ($html === '') {
            return null;
        }

        $html = preg_replace('~<br\s*/?>~i', "\n", $html) ?? $html;
        $html = preg_replace('~</p\s*>~i', "\n\n", $html) ?? $html;
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\xc2\xa0", ' ', $text);
        $text = preg_replace("~[ \t]+\n~", "\n", $text) ?? $text;
        $text = preg_replace("~\n{3,}~", "\n\n", $text) ?? $text;

        return trim($text) === '' ? null : trim($text);
    }

    /**
     * Import detail pages from a legacy content directory.
     *
     * @return list<array<string, mixed>>
     */
    private function importEntryDirectory(string $directoryName, string $type): array
    {
        $directory = $this->legacyPath($directoryName);

        if ($directory === null || ! is_dir($directory)) {
            return [];
        }

        $files = glob($directory . DIRECTORY_SEPARATOR . '*.html') ?: [];
        sort($files, SORT_NATURAL | SORT_FLAG_CASE);

        $entries = [];

        foreach ($files as $file) {
            $entry = $this->importEntryPage($file, $type);

            if ($entry !== null) {
                $entries[] = $entry;
            }
        }

        usort(
            $entries,
            static fn (array $left, array $right): int => strcmp(
                (string) ($right['sort_date'] ?? ''),
                (string) ($left['sort_date'] ?? ''),
            ),
        );

        return array_map(static function (array $entry): array {
            unset($entry['sort_date']);

            return $entry;
        }, $entries);
    }

    /**
     * Import one legacy article or FAQ detail page.
     *
     * @return array<string, mixed>|null
     */
    private function importEntryPage(string $file, string $type): ?array
    {
        [$dom, $xpath] = $this->createDom(file_get_contents($file) ?: '');

        $slug = pathinfo($file, PATHINFO_FILENAME);
        $title = $type === 'faq'
            ? ($this->firstText($xpath, "//h2[contains(concat(' ', normalize-space(@class), ' '), ' h3 ')][1]") ?: $this->firstText($xpath, '//title'))
            : ($this->firstText($xpath, '//h1[1]') ?: $this->firstText($xpath, '//title'));

        $bodyNode = $type === 'faq'
            ? $xpath->query("(//ul[contains(concat(' ', normalize-space(@class), ' '), ' article-other-ul ')]/following-sibling::div[1])[1]")->item(0)
            : $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' entry-page-body ')]")->item(0);

        if ($title === null || ! $bodyNode instanceof DOMElement) {
            return null;
        }

        $body = $this->sanitizeHtml($this->innerHtml($bodyNode));

        if ($body === '') {
            return null;
        }

        $imageCandidate = $type === 'article'
            ? ($this->firstAttribute(
                $xpath,
                "//div[contains(concat(' ', normalize-space(@class), ' '), ' entries-image ')]//a[1]",
                'href',
            ) ?: $this->firstAttribute(
                $xpath,
                "//div[contains(concat(' ', normalize-space(@class), ' '), ' entries-image ')]//img[1]",
                'src',
            ))
            : null;

        $imagePath = $this->normalizeAssetPath($imageCandidate);
        if ($imagePath !== null && ! MediaPath::exists($imagePath)) {
            $imagePath = null;
        }

        $publishedAt = $this->extractPublishedAt($xpath);
        $plainText = Str::of(strip_tags(html_entity_decode($body, ENT_QUOTES | ENT_HTML5, 'UTF-8')))
            ->replace("\xc2\xa0", ' ')
            ->squish();

        return [
            'slug' => $slug,
            'title' => html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'date' => $this->formatDateLabel($publishedAt),
            'excerpt' => Str::limit((string) $plainText, 220),
            'body' => $body,
            'image' => $imagePath,
            'sort_date' => $publishedAt?->toDateTimeString() ?? '',
        ];
    }

    /**
     * Import one legacy static page from a relative path inside the dump.
     *
     * @return array{slug:string,title:string,content:string}|null
     */
    private function importStaticPageByPath(string $relativePath, string $slug): ?array
    {
        $file = $this->legacyPath($relativePath);

        if ($file === null || ! is_file($file)) {
            return null;
        }

        [$dom, $xpath] = $this->createDom(file_get_contents($file) ?: '');

        $contentNode = $xpath->query(
            "(//div[contains(concat(' ', normalize-space(@class), ' '), ' content_box ') "
            . "and contains(concat(' ', normalize-space(@class), ' '), ' content ')]"
            . "//div[contains(concat(' ', normalize-space(@class), ' '), ' box ')][1]"
            . " | "
            . "//div[contains(concat(' ', normalize-space(@class), ' '), ' content_box ') "
            . "and contains(concat(' ', normalize-space(@class), ' '), ' content ')])[1]"
        )->item(0);

        if (! $contentNode instanceof DOMElement) {
            $contentNode = $xpath->query(
                "(//div[contains(concat(' ', normalize-space(@class), ' '), ' site-map-main ')]"
                . " | "
                . "//div[contains(concat(' ', normalize-space(@class), ' '), ' block_entries ')])[1]"
            )->item(0);
        }

        if (! $contentNode instanceof DOMElement) {
            $contentNode = $xpath->query(
                "//div[contains(concat(' ', normalize-space(@class), ' '), ' property_block ')][1]"
            )->item(0);
        }

        if (! $contentNode instanceof DOMElement) {
            return null;
        }

        $title = $this->firstText($xpath, '//h1[1]') ?: $this->firstText($xpath, '//title');
        $content = $this->innerHtml($contentNode);
        $content = preg_replace('~<ul\b[^>]*class="[^"]*\bbreadcrumb\b[^"]*"[^>]*>.*?</ul>~is', '', $content, 1) ?? $content;
        $content = preg_replace('~<h1\b[^>]*>.*?</h1>~is', '', $content, 1) ?? $content;
        $content = $this->sanitizeHtml($content);

        if ($title === null || $content === '') {
            return null;
        }

        return [
            'slug' => $slug,
            'title' => html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'content' => $content,
        ];
    }

    /**
     * Resolve the legacy dump root.
     */
    private function resolveLegacyRoot(): ?string
    {
        if ($this->legacyRoot !== null && is_dir($this->legacyRoot)) {
            return $this->legacyRoot;
        }

        $candidates = array_filter([
            realpath(base_path('legacy-source')),
        ]);

        foreach ($candidates as $candidate) {
            if (is_dir($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Resolve a path inside the legacy dump.
     */
    private function legacyPath(string $relativePath): ?string
    {
        $root = $this->resolveLegacyRoot();

        if ($root === null) {
            return null;
        }

        return $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);
    }

    /**
     * Create a DOM parser for legacy HTML.
     *
     * @return array{0:DOMDocument,1:DOMXPath}
     */
    private function createDom(string $html): array
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $previousState = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        libxml_use_internal_errors($previousState);

        return [$dom, new DOMXPath($dom)];
    }

    /**
     * Return inner HTML for a node.
     */
    private function innerHtml(DOMNode $node): string
    {
        $html = '';

        foreach ($node->childNodes as $childNode) {
            $html .= $node->ownerDocument?->saveHTML($childNode) ?? '';
        }

        return $html;
    }

    /**
     * Return text of the first matched node.
     */
    private function firstText(DOMXPath $xpath, string $expression): ?string
    {
        $node = $xpath->query($expression)->item(0);

        if (! $node instanceof DOMNode) {
            return null;
        }

        return trim($node->textContent);
    }

    /**
     * Return the first matched attribute.
     */
    private function firstAttribute(DOMXPath $xpath, string $expression, string $attribute): ?string
    {
        $node = $xpath->query($expression)->item(0);

        if (! $node instanceof DOMElement || ! $node->hasAttribute($attribute)) {
            return null;
        }

        return trim($node->getAttribute($attribute));
    }

    /**
     * Extract publish date from JSON-LD.
     */
    private function extractPublishedAt(DOMXPath $xpath): ?CarbonImmutable
    {
        foreach ($this->jsonLdBlocks($xpath) as $block) {
            $date = $this->findDatePublished($block);

            if ($date !== null) {
                return $date;
            }
        }

        return null;
    }

    /**
     * Decode all JSON-LD blocks from a page.
     *
     * @return list<array<string, mixed>>
     */
    private function jsonLdBlocks(DOMXPath $xpath): array
    {
        $scripts = $xpath->query("//script[@type='application/ld+json']");
        $blocks = [];

        foreach ($scripts as $script) {
            if (! $script instanceof DOMNode) {
                continue;
            }

            $decoded = json_decode($script->textContent, true);

            if (is_array($decoded)) {
                $blocks[] = $decoded;
            }
        }

        return $blocks;
    }

    /**
     * Recursively find datePublished in a JSON-LD structure.
     */
    private function findDatePublished(array $data): ?CarbonImmutable
    {
        if (isset($data['datePublished']) && is_string($data['datePublished'])) {
            try {
                return CarbonImmutable::parse($data['datePublished']);
            } catch (\Throwable) {
                return null;
            }
        }

        foreach ($data as $value) {
            if (is_array($value)) {
                $date = $this->findDatePublished($value);

                if ($date !== null) {
                    return $date;
                }
            }
        }

        return null;
    }

    /**
     * Recursively find GeoCoordinates in a JSON-LD structure.
     *
     * @return array{latitude:float|null,longitude:float|null}|null
     */
    private function findGeoCoordinates(array $data): ?array
    {
        $type = $data['@type'] ?? null;

        if (
            $type === 'GeoCoordinates'
            && isset($data['latitude'], $data['longitude'])
            && is_numeric($data['latitude'])
            && is_numeric($data['longitude'])
        ) {
            return [
                'latitude' => (float) $data['latitude'],
                'longitude' => (float) $data['longitude'],
            ];
        }

        foreach ($data as $value) {
            if (is_array($value)) {
                $coordinates = $this->findGeoCoordinates($value);

                if ($coordinates !== null) {
                    return $coordinates;
                }
            }
        }

        return null;
    }

    /**
     * Normalize legacy asset paths so they match files inside /public/legacy.
     */
    private function normalizeAssetPath(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = html_entity_decode(trim($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = preg_replace('~^https?://[^/]+/~i', '', $value) ?? $value;
        $value = preg_replace('~^\.\./~', '', $value) ?? $value;
        $value = ltrim($value, '/');

        if ($value === '') {
            return null;
        }

        if (str_starts_with($value, 'legacy/')) {
            return $value;
        }

        return 'legacy/' . $value;
    }

    /**
     * Remove legacy scripts and normalize links/assets inside imported HTML.
     */
    private function sanitizeHtml(string $html): string
    {
        $html = preg_replace('~<script\b[^>]*>.*?</script>~is', '', $html) ?? $html;
        $html = preg_replace('~<style\b[^>]*>.*?</style>~is', '', $html) ?? $html;

        $html = preg_replace_callback(
            '~\b(href|src)=([\'"])([^\'"]+)\2~i',
            function (array $matches): string {
                $attribute = $matches[1];
                $quote = $matches[2];
                $value = html_entity_decode(trim($matches[3]), ENT_QUOTES | ENT_HTML5, 'UTF-8');

                if ($value === '') {
                    return $matches[0];
                }

                if ($attribute === 'href') {
                    $normalizedLink = $this->normalizeLegacyLink($value);

                    if ($normalizedLink !== null) {
                        return sprintf('%s=%s%s%s', $attribute, $quote, $normalizedLink, $quote);
                    }
                }

                $normalizedAssetPath = $this->normalizeAssetPath($value);

                if ($normalizedAssetPath !== null && $this->looksLikeLegacyAsset($value)) {
                    return sprintf('%s=%s/%s%s', $attribute, $quote, ltrim($normalizedAssetPath, '/'), $quote);
                }

                return $matches[0];
            },
            $html,
        ) ?? $html;

        return trim($html);
    }

    /**
     * Convert a legacy href into the new URL structure.
     */
    private function normalizeLegacyLink(string $value): ?string
    {
        if (
            preg_match('~^(?:#|mailto:|tel:|javascript:|data:)~i', $value) === 1
            || preg_match('~^//~', $value) === 1
        ) {
            return null;
        }

        $parts = parse_url($value);

        if ($parts === false) {
            return null;
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));

        if ($scheme !== '' && ! in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        if ($host !== '' && ! in_array($host, ['realty-tuapse.ru', 'www.realty-tuapse.ru'], true)) {
            return null;
        }

        $path = (string) ($parts['path'] ?? '');

        while (str_starts_with($path, '../') || str_starts_with($path, './')) {
            $path = (string) preg_replace('~^(?:\.\./|\./)+~', '', $path);
        }

        $path = ltrim($path, '/');

        if ($path === '' || preg_match('~^index(?:-\d+)?\.htm$~i', $path) === 1) {
            return $this->appendQueryAndFragment('/', $parts);
        }

        if ($this->looksLikeLegacyAsset($path)) {
            $assetPath = $this->normalizeAssetPath($path);

            return $assetPath === null
                ? null
                : $this->appendQueryAndFragment('/' . ltrim($assetPath, '/'), $parts);
        }

        if (preg_match('~\.(?:html|htm)$~i', $path) === 1) {
            $path = (string) preg_replace('~\.(?:html|htm)$~i', '', $path);

            return $this->appendQueryAndFragment('/' . ltrim($path, '/'), $parts);
        }

        if (! str_contains(basename($path), '.')) {
            return $this->appendQueryAndFragment('/' . ltrim($path, '/'), $parts);
        }

        return null;
    }

    /**
     * Normalize text extracted from legacy HTML.
     */
    private function normalizeText(string $value): string
    {
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = str_replace("\xc2\xa0", ' ', $value);
        $value = preg_replace('~\s+~u', ' ', $value) ?? $value;

        return trim($value);
    }

    /**
     * Decide whether a path points to copied legacy assets.
     */
    private function looksLikeLegacyAsset(string $value): bool
    {
        return preg_match(
            '~^(?:\.\./)*(?:uploads|themes|assets|common|images|favicon\.ico)(?:/|$)~i',
            ltrim($value, '/'),
        ) === 1;
    }

    /**
     * Rebuild a URL with its query string and fragment.
     *
     * @param array<string, mixed> $parts
     */
    private function appendQueryAndFragment(string $path, array $parts): string
    {
        $query = isset($parts['query']) && $parts['query'] !== '' ? '?' . $parts['query'] : '';
        $fragment = isset($parts['fragment']) && $parts['fragment'] !== '' ? '#' . $parts['fragment'] : '';

        return $path . $query . $fragment;
    }

    /**
     * Format a published date label for legacy content cards.
     */
    private function formatDateLabel(?CarbonImmutable $date): string
    {
        if ($date === null) {
            return '';
        }

        return $date->locale('ru')->translatedFormat('j F Y \\г.');
    }
}
