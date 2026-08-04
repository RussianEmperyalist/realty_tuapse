param(
    [int]$Port = 8090
)

$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$workspaceRoot = Split-Path -Parent $projectRoot
$defaultPhp = 'C:\Users\thetr\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe'
$defaultIni = Join-Path $workspaceRoot 'realty-tuapse.ru\tools\php.ini'
$phpCommand = Get-Command php -ErrorAction SilentlyContinue
$php = if ($phpCommand) { $phpCommand.Source } else { $defaultPhp }
$ini = if (Test-Path -LiteralPath $defaultIni) { $defaultIni } else { $null }
$publicStorage = Join-Path $projectRoot 'public\storage'

if (-not (Test-Path -LiteralPath $php)) {
    throw "PHP not found at $php"
}

if ($ini -eq $null) {
    Write-Host 'Custom php.ini not found; using the default PHP configuration from PATH.' -ForegroundColor Yellow
}

Set-Location -LiteralPath $projectRoot

if (-not (Test-Path -LiteralPath $publicStorage)) {
    Write-Host 'Creating storage link for uploaded files...' -ForegroundColor Yellow
    if ($ini) {
        & $php -c $ini artisan storage:link | Out-Host
    } else {
        & $php artisan storage:link | Out-Host
    }
}

Write-Host ''
Write-Host "Starting local server for Realty Tuapse..." -ForegroundColor Cyan
Write-Host "URL: http://127.0.0.1:$Port" -ForegroundColor Green
Write-Host "Press Ctrl+C to stop the server." -ForegroundColor Yellow
Write-Host ''

if ($ini) {
    & $php -c $ini -S "127.0.0.1:$Port" -t public server.php
} else {
    & $php -S "127.0.0.1:$Port" -t public server.php
}
