param(
    [Parameter(Mandatory = $true)]
    [string]$ZipPath
)

$ErrorActionPreference = 'Stop'

$root = Split-Path -Parent $PSScriptRoot
$destBase = Join-Path $root 'public\vendor\fontawesome'

if (-not (Test-Path -LiteralPath $ZipPath)) {
    throw "Zip not found: $ZipPath"
}

$temp = Join-Path $env:TEMP ("fontawesome_" + [Guid]::NewGuid().ToString('N'))
New-Item -ItemType Directory -Force -Path $temp | Out-Null

try {
    Expand-Archive -LiteralPath $ZipPath -DestinationPath $temp -Force

    $allCss = Get-ChildItem -Path $temp -Recurse -File -Filter 'all.min.css' |
        Where-Object { $_.FullName -match "\\css\\all\.min\.css$" } |
        Select-Object -First 1

    if (-not $allCss) {
        throw "Could not find 'css/all.min.css' inside the zip. Expected a Font Awesome Free web distribution zip."
    }

    $srcBase = Split-Path -Parent (Split-Path -Parent $allCss.FullName)

    foreach ($folder in @('css', 'webfonts', 'js')) {
        $src = Join-Path $srcBase $folder
        if (Test-Path -LiteralPath $src) {
            New-Item -ItemType Directory -Force -Path $destBase | Out-Null
            Copy-Item -LiteralPath $src -Destination $destBase -Recurse -Force
        }
    }

    $license = Get-ChildItem -Path $srcBase -File -Filter 'LICENSE*' | Select-Object -First 1
    if ($license) {
        Copy-Item -LiteralPath $license.FullName -Destination (Join-Path $destBase 'LICENSE.txt') -Force
    }

    if (-not (Test-Path -LiteralPath (Join-Path $destBase 'css\all.min.css'))) {
        throw "Install completed but 'public/vendor/fontawesome/css/all.min.css' is still missing."
    }

    Write-Host "OK - Font Awesome installed to: $destBase"
    Write-Host "Next: reload the page; icons should work offline."
}
finally {
    if (Test-Path -LiteralPath $temp) {
        Remove-Item -LiteralPath $temp -Recurse -Force
    }
}
