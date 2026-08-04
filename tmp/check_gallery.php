<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$items = App\Models\GalleryItem::all();
echo "Total GalleryItems: " . $items->count() . "\n";
foreach ($items as $item) {
    echo "ID: {$item->id} | image_path: {$item->image_path} | filename: {$item->filename}\n";
}
echo "\n---\n";

$albums = App\Models\GalleryAlbum::all();
echo "Total GalleryAlbums: " . $albums->count() . "\n";
foreach ($albums as $album) {
    echo "ID: {$album->id} | title: {$album->title} | slug: {$album->slug}\n";
}

echo "\n---\n";
echo "Files in storage/app/public/gallery/items:\n";
$files = glob('/var/www/storage/app/public/gallery/items/*');
foreach ($files as $f) {
    echo "  " . basename($f) . "\n";
}