<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\GalleryAlbum;
use App\Models\GalleryItem;
use App\Models\NewsPost;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\SiteSetting;
use App\Support\LegalPageContent;
use App\Support\LegacyContentImporter;
use App\Support\MediaPath;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LegacyContentSeeder extends Seeder
{
    /**
     * Sync content that should be taken directly from the old public copy.
     */
    public function run(): void
    {
        $importer = new LegacyContentImporter();

        foreach (['politika-konfidencialnosti', 'polzovatelskoe-soglashenie'] as $slug) {
            $page = $slug === 'politika-konfidencialnosti'
                ? [
                    'title' => 'Политика конфиденциальности',
                    'content' => LegalPageContent::privacyPolicy(),
                ]
                : [
                    'title' => 'Пользовательское соглашение',
                    'content' => LegalPageContent::userAgreement(),
                ];

            SiteSetting::query()->updateOrCreate(
                ['key' => "page.{$slug}.title"],
                ['type' => 'string', 'value' => $page['title']],
            );

            SiteSetting::query()->updateOrCreate(
                ['key' => "page.{$slug}.content"],
                ['type' => 'html', 'value' => $page['content']],
            );
        }

        if (! $importer->isAvailable()) {
            return;
        }

        $newsPosts = $importer->importNewsPosts();
        $newsSlugs = [];

        foreach ($newsPosts as $newsPost) {
            $newsSlugs[] = $newsPost['slug'];

            NewsPost::query()->updateOrCreate(
                ['slug' => $newsPost['slug']],
                $newsPost,
            );
        }

        if ($newsSlugs !== []) {
            NewsPost::query()
                ->whereNotNull('legacy_path')
                ->whereNotIn('slug', $newsSlugs)
                ->delete();
        }

        $this->syncLegacyProperties($importer);
        $this->syncLegacyGallery($importer);

        Employee::query()
            ->where('slug', 'olga-petrovna-shevchenko')
            ->update([
                'photo_path' => 'legacy/uploads/editor/images/%20%D0%A8%D0%B5%D0%B2%D1%87%D0%B5%D0%BD%D0%BA%D0%BE.jpg',
            ]);

        GalleryItem::query()
            ->get()
            ->filter(function (GalleryItem $item): bool {
                if (! str_starts_with((string) $item->image_path, 'storage/')) {
                    return false;
                }

                return ! MediaPath::exists($item->image_path)
                    && ($item->thumb_path === null || ! MediaPath::exists($item->thumb_path));
            })
            ->each
            ->delete();

        GalleryAlbum::query()
            ->withCount('items')
            ->get()
            ->filter(function (GalleryAlbum $album): bool {
                if (! str_starts_with((string) $album->cover_image_path, 'storage/')) {
                    return false;
                }

                return ! MediaPath::exists($album->cover_image_path)
                    && $album->items_count === 0;
            })
            ->each
            ->delete();
    }

    /**
     * Sync original gallery sections in the requested legacy order.
     */
    private function syncLegacyGallery(LegacyContentImporter $importer): void
    {
        $legacyAlbums = $importer->importGalleryAlbums();

        if ($legacyAlbums === []) {
            return;
        }

        foreach ($legacyAlbums as $legacyAlbum) {
            $items = $legacyAlbum['items'] ?? [];
            unset($legacyAlbum['items']);

            $album = GalleryAlbum::query()->updateOrCreate(
                ['slug' => $legacyAlbum['slug']],
                $legacyAlbum,
            );

            $this->syncLegacyGalleryItems($album, $items);
        }

        GalleryAlbum::query()
            ->where('slug', 'fotogalereya-agentstva')
            ->delete();
    }

    /**
     * @param list<array<string, mixed>> $items
     */
    private function syncLegacyGalleryItems(GalleryAlbum $album, array $items): void
    {
        $paths = [];

        foreach ($items as $item) {
            $paths[] = (string) $item['image_path'];

            GalleryItem::query()->updateOrCreate(
                [
                    'gallery_album_id' => $album->id,
                    'image_path' => $item['image_path'],
                ],
                $item + ['gallery_album_id' => $album->id],
            );
        }

        GalleryItem::query()
            ->where('gallery_album_id', $album->id)
            ->whereNotIn('image_path', $paths)
            ->delete();
    }

    /**
     * Sync legacy real estate objects without changing existing employee order.
     */
    private function syncLegacyProperties(LegacyContentImporter $importer): void
    {
        $legacyProperties = $importer->importProperties();

        if ($legacyProperties === []) {
            return;
        }

        $legacyIds = [];

        foreach ($legacyProperties as $legacyProperty) {
            $legacyIds[] = (int) $legacyProperty['legacy_id'];
            $images = $legacyProperty['images'] ?? [];
            $employee = $this->resolveLegacyPropertyEmployee($legacyProperty);

            unset(
                $legacyProperty['images'],
                $legacyProperty['legacy_owner_id'],
                $legacyProperty['legacy_owner_name'],
                $legacyProperty['legacy_owner_photo_path'],
            );

            $legacyProperty['employee_id'] = $employee?->id;

            $property = Property::query()->updateOrCreate(
                ['legacy_id' => $legacyProperty['legacy_id']],
                $legacyProperty,
            );

            $this->syncLegacyPropertyImages($property, is_array($images) ? $images : []);
        }

        // Remove the early demo object that pointed at legacy media but never existed
        // as a real legacy listing id.
        Property::query()
            ->where('legacy_id', 201)
            ->whereNotIn('legacy_id', $legacyIds)
            ->delete();
    }

    /**
     * Resolve a legacy property owner. Existing employees keep their sort_order.
     *
     * @param array<string, mixed> $legacyProperty
     */
    private function resolveLegacyPropertyEmployee(array $legacyProperty): ?Employee
    {
        $legacyOwnerId = (int) ($legacyProperty['legacy_owner_id'] ?? 0);

        if ($legacyOwnerId <= 0) {
            return null;
        }

        $employee = Employee::query()->where('legacy_id', $legacyOwnerId)->first();

        if ($employee !== null) {
            return $employee;
        }

        $name = trim((string) ($legacyProperty['legacy_owner_name'] ?? ''));

        if ($name === '') {
            $name = "Сотрудник {$legacyOwnerId}";
        }

        return Employee::query()->create([
            'legacy_id' => $legacyOwnerId,
            'full_name' => $name,
            'slug' => $this->uniqueEmployeeSlug($name, $legacyOwnerId),
            'position' => 'Агент',
            'sort_order' => ((int) Employee::query()->max('sort_order')) + 1,
            'phone_primary' => config('realty.phones.0'),
            'phone_secondary' => null,
            'email' => config('realty.contact_email'),
            'photo_path' => $legacyProperty['legacy_owner_photo_path']
                ?: 'legacy/themes/dolphin/assets/images/no_photo_entry.png',
            'bio' => null,
            'is_admin' => false,
            'is_active' => true,
        ]);
    }

    /**
     * Build a unique employee slug without touching existing rows.
     */
    private function uniqueEmployeeSlug(string $name, int $legacyOwnerId): string
    {
        $baseSlug = Str::slug($name) ?: "employee-{$legacyOwnerId}";
        $slug = $baseSlug;
        $suffix = 2;

        while (Employee::query()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    /**
     * Sync imported property photos by path.
     *
     * @param list<array{path:string,thumb_path:string|null}> $images
     */
    private function syncLegacyPropertyImages(Property $property, array $images): void
    {
        $paths = [];

        foreach ($images as $index => $image) {
            if (! isset($image['path']) || ! is_string($image['path'])) {
                continue;
            }

            $paths[] = $image['path'];

            PropertyImage::query()->updateOrCreate(
                [
                    'property_id' => $property->id,
                    'path' => $image['path'],
                ],
                [
                    'thumb_path' => is_string($image['thumb_path'] ?? null) ? $image['thumb_path'] : null,
                    'alt' => $property->title,
                    'sort_order' => $index + 1,
                    'is_cover' => $index === 0,
                ],
            );
        }

        PropertyImage::query()
            ->where('property_id', $property->id)
            ->when($paths !== [], fn ($query) => $query->whereNotIn('path', $paths))
            ->delete();
    }

}
