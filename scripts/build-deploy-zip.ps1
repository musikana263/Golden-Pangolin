<#
    build-deploy-zip.ps1
    --------------------
    Produces two zips for a split-folder DirectAdmin deploy.

        deploy\golden-pangolin-app.zip
            Extract at the ROOT of File Manager (~/).
            Creates  ~/goldenpangolin/  with the Laravel app + a .env template.

        deploy\golden-pangolin-public.zip
            Extract inside  ~/domains/goldenpangolin.com/public_html/
            Drops in index.php, install.php, .htaccess, css/, js/, images/, ...

    Steps you do AFTER uploading:
        a) DirectAdmin → MySQL Management → create a database + user.
        b) File Manager → open  ~/goldenpangolin/.env  → replace the
           <your_db_*> placeholders, save.
        c) Visit  https://goldenpangolin.com/install.php  once. Done.
#>

$ErrorActionPreference = 'Stop'

$root        = (Resolve-Path "$PSScriptRoot\..").Path
$build       = Join-Path $root 'deploy'
$appStage    = Join-Path $build '_app_stage'
$pubStage    = Join-Path $build '_public_stage'
$appZip      = Join-Path $build 'golden-pangolin-app.zip'
$pubZip      = Join-Path $build 'golden-pangolin-public.zip'
$appFolder   = 'goldenpangolin'

Write-Host "Project root : $root"
Write-Host "App zip      : $appZip"     -ForegroundColor Yellow
Write-Host "Public zip   : $pubZip"     -ForegroundColor Yellow

# 1. Clean previous build.
if (Test-Path $build) { Remove-Item $build -Recurse -Force }
New-Item -ItemType Directory -Path $build    | Out-Null
New-Item -ItemType Directory -Path $appStage | Out-Null
New-Item -ItemType Directory -Path $pubStage | Out-Null

# 2. composer install --no-dev.
Write-Host "`n[1/5] composer install --no-dev..." -ForegroundColor Cyan
Push-Location $root
try {
    composer install --no-dev --optimize-autoloader --no-progress
} finally {
    Pop-Location
}

# 3. Stage the APP zip — everything except public/ and junk.
Write-Host "`n[2/5] Staging app files..." -ForegroundColor Cyan
$appTarget = Join-Path $appStage $appFolder
New-Item -ItemType Directory -Path $appTarget | Out-Null

$excludeDirs = @(
    'public', 'node_modules', '.git', 'tests', 'deploy', '.idea', '.vscode',
    'storage\framework\sessions', 'storage\framework\views',
    'storage\framework\cache\data', 'storage\logs'
)
$excludeFiles = @(
    '.env', '.env.backup', '*.log', '.phpunit.result.cache',
    'phpunit.xml', 'webpack.mix.js', 'package.json', 'package-lock.json',
    'Dockerfile', 'docker-compose.yml', 'nixpacks.toml', 'Procfile',
    'railway-deploy.sh', 'server.php', '.gitignore', '.gitattributes',
    'CONTRIBUTING.md', 'SECURITY.md', 'LICENSE', 'README.md',
    'deploy-env-template'
)
$xd = @(); foreach ($d in $excludeDirs)  { $xd += '/XD'; $xd += (Join-Path $root $d) }
$xf = @(); foreach ($f in $excludeFiles) { $xf += '/XF'; $xf += $f }

robocopy $root $appTarget /E /NFL /NDL /NJH /NJS /MT:8 @xd @xf | Out-Null
if ($LASTEXITCODE -ge 8) { throw "robocopy (app) failed with code $LASTEXITCODE" }

# Drop the pre-filled .env template inside the app folder for the user to edit.
Copy-Item -Path (Join-Path $root 'deploy-env-template') `
          -Destination (Join-Path $appTarget '.env') -Force

# Ensure Laravel's runtime directories exist in the zip. We excluded their
# CONTENTS above to avoid shipping local dev cache/session files, but the
# directories themselves must be present (Laravel will not auto-create them
# during bootstrap, and a missing storage/framework/views explodes on first
# render with "Please provide a valid cache path"). A .gitkeep file forces
# the zip to retain the directory entry on extract.
$ensureDirs = @(
    'storage\logs',
    'storage\framework\sessions',
    'storage\framework\views',
    'storage\framework\cache\data',
    'storage\app\public',
    'bootstrap\cache'
)
foreach ($d in $ensureDirs) {
    $fullDir = Join-Path $appTarget $d
    New-Item -ItemType Directory -Path $fullDir -Force | Out-Null
    [System.IO.File]::WriteAllText((Join-Path $fullDir '.gitkeep'), '')
}

# 4. Stage the PUBLIC zip — contents of public/.
Write-Host "`n[3/5] Staging public files..." -ForegroundColor Cyan
$publicSource = Join-Path $root 'public'
robocopy $publicSource $pubStage /E /NFL /NDL /NJH /NJS /MT:8 /XF '_setup.php' | Out-Null
if ($LASTEXITCODE -ge 8) { throw "robocopy (public) failed with code $LASTEXITCODE" }

# 5. Patch index.php + write install.php so they resolve ~/goldenpangolin/.
Write-Host "`n[4/5] Patching index.php + install.php for split layout..." -ForegroundColor Cyan

$splitIndex = @'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// The Laravel app lives outside public_html, in ~/goldenpangolin/.
// __DIR__ here is ~/domains/<domain>/public_html.
$app_root = __DIR__ . '/../../../goldenpangolin';

if (file_exists($maintenance = $app_root . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $app_root . '/vendor/autoload.php';

(require_once $app_root . '/bootstrap/app.php')
    ->handleRequest(Request::capture());
'@
[System.IO.File]::WriteAllText((Join-Path $pubStage 'index.php'), $splitIndex)

# Take public/_setup.php and rewrite its $root lookup for the split layout,
# then save as install.php in the public zip.
$setupSrc = Get-Content -Path (Join-Path $publicSource '_setup.php') -Raw
$setupSplit = $setupSrc -replace [regex]::Escape('$root = dirname(__DIR__);'),
                                  "`$root = __DIR__ . '/../../../goldenpangolin';"
[System.IO.File]::WriteAllText((Join-Path $pubStage 'install.php'), $setupSplit)

# 6. Zip both — manual entry loop. We explicitly replace every \ with /
#    in entry names because both Compress-Archive AND ZipFile::CreateFromDirectory
#    can leak backslashes on Windows, which Linux unzip then treats as literal
#    filename characters (producing the dreaded "flat dump" extraction).
Write-Host "`n[5/5] Compressing zips (forward-slash paths)..." -ForegroundColor Cyan
Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

function New-ZipForwardSlash {
    param([string]$SourceDir, [string]$ZipPath)

    if (Test-Path $ZipPath) { Remove-Item $ZipPath -Force }
    $sourceFull = (Resolve-Path $SourceDir).Path.TrimEnd('\') + '\'

    $fs  = [System.IO.File]::Open($ZipPath, [System.IO.FileMode]::Create)
    try {
        $zip = New-Object System.IO.Compression.ZipArchive(
            $fs, [System.IO.Compression.ZipArchiveMode]::Create)
        try {
            Get-ChildItem -Path $SourceDir -Recurse -File | ForEach-Object {
                # Build the entry path relative to the stage folder and force /
                $relPath  = $_.FullName.Substring($sourceFull.Length) -replace '\\', '/'
                $entry    = $zip.CreateEntry(
                    $relPath, [System.IO.Compression.CompressionLevel]::Optimal)
                $entryS   = $entry.Open()
                try {
                    $bytes = [System.IO.File]::ReadAllBytes($_.FullName)
                    $entryS.Write($bytes, 0, $bytes.Length)
                } finally {
                    $entryS.Close()
                }
            }
        } finally {
            $zip.Dispose()
        }
    } finally {
        $fs.Close()
    }
}

New-ZipForwardSlash -SourceDir $appStage -ZipPath $appZip
New-ZipForwardSlash -SourceDir $pubStage -ZipPath $pubZip

Remove-Item $appStage -Recurse -Force
Remove-Item $pubStage -Recurse -Force

$appSize = [math]::Round((Get-Item $appZip).Length / 1MB, 1)
$pubSize = [math]::Round((Get-Item $pubZip).Length / 1MB, 1)

Write-Host "`n========================================================" -ForegroundColor Green
Write-Host " Two zips ready. Upload BOTH to DirectAdmin." -ForegroundColor Green
Write-Host ""
Write-Host " 1) Extract AT THE ROOT (~/):" -ForegroundColor Yellow
Write-Host "    $appZip   ($appSize MB)"
Write-Host "    Result: ~/goldenpangolin/"
Write-Host ""
Write-Host " 2) Extract INSIDE public_html:" -ForegroundColor Yellow
Write-Host "    $pubZip   ($pubSize MB)"
Write-Host "    Path:   ~/domains/goldenpangolin.com/public_html/"
Write-Host ""
Write-Host " 3) Edit ~/goldenpangolin/.env (DB creds), then visit:" -ForegroundColor Yellow
Write-Host "    https://goldenpangolin.com/install.php"
Write-Host "========================================================" -ForegroundColor Green
