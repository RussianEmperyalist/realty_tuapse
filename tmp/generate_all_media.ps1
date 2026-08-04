$colors = @("3490dc","e3342f","38c172","f6993f","9561e2","f66d9b","4dc0b5","6574cd","f7941e","6cb2eb")

function New-PlaceholderSvg {
    param([string]$Path, [int]$Number, [string]$Color)
    $svg = @"
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="600" viewBox="0 0 800 600">
  <rect width="800" height="600" fill="#$Color" opacity="0.15"/>
  <rect x="300" y="200" width="200" height="200" rx="20" fill="#$Color" opacity="0.8"/>
  <circle cx="400" cy="300" r="60" fill="white" opacity="0.9"/>
  <text x="400" y="315" font-family="Arial" font-size="40" fill="#$Color" text-anchor="middle" font-weight="bold">$Number</text>
</svg>
"@
    New-Item -ItemType Directory -Force -Path (Split-Path $Path -Parent) | Out-Null
    Set-Content -Path $Path -Value $svg -NoNewline
    Write-Host "  Created: $Path"
}

$base = "storage\app\public"

# 1. Gallery items (6 items: 001.jpg..006.jpg + thumbs)
Write-Host "=== Gallery items ==="
1..6 | ForEach-Object {
    $num = $_.ToString("000")
    $color = $colors[($_ - 1) % $colors.Length]
    New-PlaceholderSvg -Path "$base\legacy\uploads\gallery\new\$num.svg" -Number $_ -Color $color
    New-PlaceholderSvg -Path "$base\legacy\uploads\gallery\new\${num}_thumb.svg" -Number $_ -Color $color
}

# 2. Property images for property 189 (5 images)
Write-Host "=== Property 189 images ==="
$images189 = @(
    "full_46e763bbbae9a037320a5677d1cd9fbf",
    "full_96a78d5f9a7170302647a37ff2d3298f",
    "full_cb21e078a1fb61b09eee773efae4e100",
    "full_163aa8bb0fdd2d90a7cb4cc53caf6222",
    "full_3ec1cb535701886d3ea61ed5c0d3a41a"
)
$i = 1
foreach ($img in $images189) {
    $color = $colors[($i - 1) % $colors.Length]
    New-PlaceholderSvg -Path "$base\legacy\uploads\objects\189\modified\$img.svg" -Number $i -Color $color
    New-PlaceholderSvg -Path "$base\legacy\uploads\objects\189\modified\thumb_205x107_$($img.Substring(5)).svg" -Number $i -Color $color
    $i++
}

# 3. Property images for property 104 (5 images)
Write-Host "=== Property 104 images ==="
$images104 = @(
    "full_0d3a7944975213a598408654924f66fe",
    "full_852b8f776679b646a8fdcd36a5da733e",
    "full_9efd01f438404d0bd3c52ad15187ba74",
    "full_c195a13750097aa02384b1ad89fe966a",
    "full_e4f4b101fe770107a50f749d7cf333f9"
)
$i = 1
foreach ($img in $images104) {
    $color = $colors[($i - 1) % $colors.Length]
    New-PlaceholderSvg -Path "$base\legacy\uploads\objects\104\modified\$img.svg" -Number $i -Color $color
    New-PlaceholderSvg -Path "$base\legacy\uploads\objects\104\modified\thumb_205x107_$($img.Substring(5)).svg" -Number $i -Color $color
    $i++
}

# 4. Property images for property 180 (5 images)
Write-Host "=== Property 180 images ==="
$images180 = @(
    "full_8f173d9ad6f89205b13052ecc84aa157",
    "full_b862b09f8672dd3e6a94439b721c5503",
    "full_4f9d7de1299ca45e6cb9c0871f1e4116",
    "full_3e59f9da775e219c4f6b2c0af91a9bbb",
    "full_8eb157e9d39f8d97b81318c6cc9e24e9"
)
$i = 1
foreach ($img in $images180) {
    $color = $colors[($i - 1) % $colors.Length]
    New-PlaceholderSvg -Path "$base\legacy\uploads\objects\180\modified\$img.svg" -Number $i -Color $color
    New-PlaceholderSvg -Path "$base\legacy\uploads\objects\180\modified\thumb_205x107_$($img.Substring(5)).svg" -Number $i -Color $color
    $i++
}

# 5. Employee photos
Write-Host "=== Employee photos ==="
New-PlaceholderSvg -Path "$base\legacy\images\contact\c3e0ea4f724d96baf93c5d706cf11525.svg" -Number 1 -Color $colors[0]
New-PlaceholderSvg -Path "$base\legacy\images\contact\2493d82a1fb0dfdc208da02a5a51a240.svg" -Number 2 -Color $colors[1]
New-PlaceholderSvg -Path "$base\legacy\images\contact\d1be6581dad19743feb66a1034a80dfc.svg" -Number 3 -Color $colors[2]
New-PlaceholderSvg -Path "$base\legacy\images\contact\118205652ef9776fd3358c755cb5f013.svg" -Number 4 -Color $colors[3]
New-PlaceholderSvg -Path "$base\legacy\images\contact\25989b664f2b71f225002d7f741a7f03.svg" -Number 5 -Color $colors[4]
New-PlaceholderSvg -Path "$base\legacy\images\contact\e905978c6bd9c3a911c34fb6ef2793c9.svg" -Number 6 -Color $colors[5]
New-PlaceholderSvg -Path "$base\legacy\uploads\editor\images\%20%D0%A8%D0%B5%D0%B2%D1%87%D0%B5%D0%BD%D0%BA%D0%BE.svg" -Number 7 -Color $colors[6]
New-PlaceholderSvg -Path "$base\legacy\themes\dolphin\assets\images\no_photo_entry.svg" -Number 0 -Color $colors[7]

# 6. News image reference (points to gallery) - already covered above

# 7. Also ensure the non-legacy placeholder images exist
Write-Host "=== Non-legacy placeholders ==="
@("properties", "gallery\items", "news") | ForEach-Object {
    $dir = $_
    $count = if ($dir -eq "gallery\items") {6} elseif ($dir -eq "news") {2} else {3}
    1..$count | ForEach-Object {
        $p = "$base\$dir\image_$_.svg"
        if (-not (Test-Path $p)) {
            $color = $colors[($_ - 1) % $colors.Length]
            New-PlaceholderSvg -Path $p -Number $_ -Color $color
        }
    }
}

Write-Host "`n✅ ALL MEDIA GENERATED SUCCESSFULLY!"