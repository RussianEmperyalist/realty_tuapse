param(
    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]]$ArtisanArgs
)

$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$phpCommand = Get-Command php -ErrorAction SilentlyContinue
$php = if ($phpCommand) { $phpCommand.Source } else { $null }

if (-not $php -or -not (Test-Path -LiteralPath $php)) {
    throw "PHP not found. Install PHP 8.3 and add it to PATH, or run via Docker (podman compose)."
}

if ($null -eq $ArtisanArgs -or $ArtisanArgs.Count -eq 0) {
    $ArtisanArgs = @('list')
}

Set-Location -LiteralPath $projectRoot

& $php artisan @ArtisanArgs

exit $LASTEXITCODE
