<?php

$colors = ['3490dc','e3342f','38c172','f6993f','9561e2','f66d9b','4dc0b5','6574cd','f7941e','6cb2eb'];
$directories = ['properties' => 3, 'gallery/items' => 6, 'news' => 2];

foreach ($directories as $dir => $count) {
    for ($i = 1; $i <= $count; $i++) {
        $color = $colors[($i - 1) % count($colors)];
        $svg = sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" width="800" height="600" viewBox="0 0 800 600">'
            . '<rect width="800" height="600" fill="#%s" opacity="0.15"/>'
            . '<rect x="300" y="200" width="200" height="200" rx="20" fill="#%s" opacity="0.8"/>'
            . '<circle cx="400" cy="300" r="60" fill="white" opacity="0.9"/>'
            . '<text x="400" y="315" font-family="Arial" font-size="40" fill="#%s" text-anchor="middle" font-weight="bold">%d</text>'
            . '</svg>',
            $color, $color, $color, $i
        );
        $path = sprintf('%s/../storage/app/public/%s/image_%d.svg', __DIR__, $dir, $i);
        file_put_contents($path, $svg);
        echo "Created: $path\n";
    }
}

echo "Done! All placeholder images generated.\n";