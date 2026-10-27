# Génère les archives installables dans dist/
$root = Split-Path -Parent $PSScriptRoot
$dist = Join-Path $root 'dist'
New-Item -ItemType Directory -Force $dist | Out-Null

$targets = @(
    @{ Name = 'amidac-toolkit';       Path = Join-Path $root 'plugins\amidac-toolkit' },
    @{ Name = 'amidac-kadence-child'; Path = Join-Path $root 'theme\amidac-kadence-child' }
)

foreach ($t in $targets) {
    $zip = Join-Path $dist ($t.Name + '.zip')
    if (Test-Path $zip) { Remove-Item $zip }
    # Archive avec séparateurs "/" pour WordPress/Linux, dossier racine = nom du paquet
    Add-Type -AssemblyName System.IO.Compression.FileSystem
    $archive = [System.IO.Compression.ZipFile]::Open($zip, 'Create')
    Get-ChildItem $t.Path -Recurse -File | ForEach-Object {
        $rel = $_.FullName.Substring($t.Path.Length + 1).Replace('\', '/')
        [void][System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($archive, $_.FullName, "$($t.Name)/$rel")
    }
    $archive.Dispose()
    Write-Host "OK : $zip"
}
