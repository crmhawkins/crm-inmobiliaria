# Script para descargar ChromeDriver automáticamente
Write-Host "Downloading ChromeDriver..." -ForegroundColor Green

# URL base de ChromeDriver
$baseUrl = "https://chromedriver.storage.googleapis.com"

# Obtener la versión de Chrome instalada
try {
    $chromeVersion = (Get-ItemProperty "HKLM:\SOFTWARE\Google\Chrome\BLBeacon" -ErrorAction SilentlyContinue).version
    if (-not $chromeVersion) {
        $chromeVersion = (Get-ItemProperty "HKLM:\SOFTWARE\WOW6432Node\Microsoft\Windows\CurrentVersion\Uninstall\Google Chrome" -ErrorAction SilentlyContinue).DisplayVersion
    }

    if (-not $chromeVersion) {
        Write-Host "Could not detect Chrome version. Using latest stable version." -ForegroundColor Yellow
        $chromeVersion = "120.0.6099.109" # Versión estable reciente
    }

    Write-Host "Detected Chrome version: $chromeVersion" -ForegroundColor Green

    # Extraer versión mayor
    $majorVersion = $chromeVersion.Split('.')[0]
    Write-Host "Major version: $majorVersion" -ForegroundColor Green

} catch {
    Write-Host "Error detecting Chrome version. Using latest stable version." -ForegroundColor Yellow
    $majorVersion = "120"
}

# Descargar ChromeDriver
$downloadUrl = "$baseUrl/LATEST_RELEASE_$majorVersion"
Write-Host "Getting latest version for Chrome $majorVersion..." -ForegroundColor Green

try {
    $latestVersion = Invoke-RestMethod -Uri $downloadUrl
    Write-Host "Latest ChromeDriver version: $latestVersion" -ForegroundColor Green

    $chromedriverUrl = "$baseUrl/$latestVersion/chromedriver_win32.zip"
    $zipPath = "chromedriver_win32.zip"

    Write-Host "Downloading ChromeDriver..." -ForegroundColor Green
    Invoke-WebRequest -Uri $chromedriverUrl -OutFile $zipPath

    Write-Host "Extracting ChromeDriver..." -ForegroundColor Green
    Expand-Archive -Path $zipPath -DestinationPath "." -Force

    # Mover chromedriver.exe a la raíz del proyecto
    if (Test-Path "chromedriver.exe") {
        Write-Host "ChromeDriver extracted successfully!" -ForegroundColor Green
    } else {
        Write-Host "Error: chromedriver.exe not found in extracted files" -ForegroundColor Red
    }

    # Limpiar archivo ZIP
    Remove-Item $zipPath -Force

} catch {
    Write-Host "Error downloading ChromeDriver: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Please download manually from: https://chromedriver.chromium.org/" -ForegroundColor Yellow
}

Write-Host "Done!" -ForegroundColor Green
