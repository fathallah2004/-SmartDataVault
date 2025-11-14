# Script pour mettre à jour la configuration email dans .env
# Usage: .\update-mail-config.ps1 -Email "votre-email@gmail.com" -Password "oxwnuehrnjtcmbyi"

param(
    [Parameter(Mandatory=$true)]
    [string]$Email,
    
    [Parameter(Mandatory=$true)]
    [string]$Password
)

$envFile = ".env"

if (-not (Test-Path $envFile)) {
    Write-Host "Erreur: Le fichier .env n'existe pas!" -ForegroundColor Red
    exit 1
}

Write-Host "Mise à jour de la configuration email..." -ForegroundColor Yellow

# Lire le contenu du fichier
$content = Get-Content $envFile

# Remplacer les lignes MAIL_
$newContent = $content | ForEach-Object {
    if ($_ -match "^MAIL_MAILER=") {
        "MAIL_MAILER=smtp"
    }
    elseif ($_ -match "^MAIL_HOST=") {
        "MAIL_HOST=smtp.gmail.com"
    }
    elseif ($_ -match "^MAIL_PORT=") {
        "MAIL_PORT=587"
    }
    elseif ($_ -match "^MAIL_USERNAME=") {
        "MAIL_USERNAME=$Email"
    }
    elseif ($_ -match "^MAIL_PASSWORD=") {
        "MAIL_PASSWORD=$Password"
    }
    elseif ($_ -match "^MAIL_ENCRYPTION=") {
        "MAIL_ENCRYPTION=tls"
    }
    elseif ($_ -match "^MAIL_FROM_ADDRESS=") {
        "MAIL_FROM_ADDRESS=$Email"
    }
    elseif ($_ -match "^MAIL_FROM_NAME=") {
        "MAIL_FROM_NAME=`"SmartDataVault`""
    }
    else {
        $_
    }
}

# Écrire le nouveau contenu
$newContent | Set-Content $envFile

Write-Host "Configuration email mise à jour avec succès!" -ForegroundColor Green
Write-Host "Email: $Email" -ForegroundColor Cyan
Write-Host ""
Write-Host "Maintenant, exécutez:" -ForegroundColor Yellow
Write-Host "  php artisan config:clear" -ForegroundColor White
Write-Host "  php artisan cache:clear" -ForegroundColor White

