<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PlaceholderMediaSeeder extends \Illuminate\Database\Seeder
{
    public function run(): void
    {
        $disk = Storage::disk('public');
        $baseDir = realpath(__DIR__ . '/../../storage/app/public');
        
        $this->generatePlaceholderImages($disk, 'properties', 3);
        $this->generatePlaceholderImages($disk, 'gallery/items', 6);
        $this->generatePlaceholderImages($disk, 'news', 2);
        $this->generatePlaceholderImages($disk, 'employees', 1);
        
        $this->command->info('Placeholder images generated successfully!');
    }

    private function generatePlaceholderImages($disk, string $path, int $count): void
    {
        $colors = ['3490dc', 'e3342f', '38c172', 'f6993f', '9561e2', 'f66d9b', '4dc0b5', '6574cd', 'f7941e', '6cb2eb'];
        
        for ($i = 1; $i <= $count; $i++) {
            $color = $colors[($i - 1) % count($colors)];
            $filename = "image_{$i}.svg";
            
            $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="600" viewBox="0 0 800 600">
  <rect width="800" height="600" fill="#{$color}" opacity="0.15"/>
  <rect x="300" y="200" width="200" height="200" rx="20" fill="#{$color}" opacity="0.8"/>
  <circle cx="400" cy="300" r="60" fill="white" opacity="0.9"/>
  <text x="400" y="315" font-family="Arial" font-size="40" fill="#{$color}" text-anchor="middle" font-weight="bold">{$i}</text>
  <text x="400" y="480" font-family="Arial" font-size="20" fill="#666" text-anchor="middle">{$path} — изображение {$i}</text>
</svg>
SVG;
            
            $disk->put("{$path}/{$filename}", $svg);
        }
    }
}