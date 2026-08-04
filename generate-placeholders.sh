#!/bin/bash
# Generate placeholder SVG images for the project

COLORS=("3490dc" "e3342f" "38c172" "f6993f" "9561e2" "f66d9b" "4dc0b5" "6574cd" "f7941e" "6cb2eb")

generate_svg() {
    local COLOR=$1
    local NUM=$2
    local LABEL=$3
    cat <<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="600" viewBox="0 0 800 600">
  <rect width="800" height="600" fill="#${COLOR}" opacity="0.15"/>
  <rect x="300" y="200" width="200" height="200" rx="20" fill="#${COLOR}" opacity="0.8"/>
  <circle cx="400" cy="300" r="60" fill="white" opacity="0.9"/>
  <text x="400" y="315" font-family="Arial" font-size="40" fill="#${COLOR}" text-anchor="middle" font-weight="bold">${NUM}</text>
  <text x="400" y="480" font-family="Arial" font-size="20" fill="#666" text-anchor="middle">${LABEL} — изображение ${NUM}</text>
</svg>
SVG
}

# Properties
for i in 1 2 3; do
  generate_svg "${COLORS[$((i-1))]}" "$i" "properties" > "storage/app/public/properties/image_${i}.svg"
done

# Gallery items
for i in 1 2 3 4 5 6; do
  generate_svg "${COLORS[$((i%10))]}" "$i" "gallery" > "storage/app/public/gallery/items/image_${i}.svg"
done

# News
for i in 1 2; do
  generate_svg "${COLORS[$((i+2))]}" "$i" "news" > "storage/app/public/news/image_${i}.svg"
done

echo "✅ Placeholder images generated!"