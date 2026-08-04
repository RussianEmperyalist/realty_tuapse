$colors = @("3490dc","e3342f","38c172","f6993f","9561e2","f66d9b","4dc0b5","6574cd","f7941e","6cb2eb")
$directories = @{
    "storage\app\public\properties" = 3
    "storage\app\public\gallery\items" = 6
    "storage\app\public\news" = 2
}

foreach ($dir in $directories.Keys) {
    New-Item -ItemType Directory -Force -Path $dir | Out-Null
    $count = $directories[$dir]
    for ($i = 1; $i -le $count; $i++) {
        $color = $colors[($i - 1) % $colors.Length]
        $svg = @"
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="600" viewBox="0 0 800 600">
  <rect width="800" height="600" fill="#$color" opacity="0.15"/>
  <rect x="300" y="200" width="200" height="200" rx="20" fill="#$color" opacity="0.8"/>
  <circle cx="400" cy="300" r="60" fill="white" opacity="0.9"/>
  <text x="400" y="315" font-family="Arial" font-size="40" fill="#$color" text-anchor="middle" font-weight="bold">$i</text>
</svg>
"@
        $path = "$dir\image_$i.svg"
        Set-Content -Path $path -Value $svg -NoNewline
        Write-Host "Created: $path"
    }
}
Write-Host "Done! All placeholder images generated."