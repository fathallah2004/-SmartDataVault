# Script pour configurer l'email dans .env
# Usage: .\configure-email.ps1 [-Email "email@gmail.com"] [-Password "app-password"] [-Provider "gmail|outlook|mailtrap"]

param(
    [string]$Email,
    [string]$Password,
    [ValidateSet("gmail", "outlook", "mailtrap")]
    [string]$Provider = "gmail"
)

$envFile = ".env"

if (-not (Test-Path $envFile)) {
    Write-Host "Erreur: Le fichier .env n'existe pas!" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Configuration Email - SmartDataVault" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Configuration par provider
$config = @{
    "gmail" = @{
        Host = "smtp.gmail.com"
        Port = 587
        Encryption = "tls"
    }
    "outlook" = @{
        Host = "smtp-mail.outlook.com"
        Port = 587
        Encryption = "tls"
    }
    "mailtrap" = @{
        Host = "smtp.mailtrap.io"
        Port = 2525
        Encryption = "tls"
    }
}

$mailConfig = $config[$Provider]

# Demander l'email si non fourni
if ([string]::IsNullOrWhiteSpace($Email)) {
    $Email = Read-Host "Entrez votre adresse email"
    if ([string]::IsNullOrWhiteSpace($Email)) {
    Write-Host "Erreur: L'email ne peut pas être vide!" -ForegroundColor Red
    exit 1
    }
}

# Demander le mot de passe si non fourni
if ([string]::IsNullOrWhiteSpace($Password)) {
    if ($Provider -eq "gmail") {
        Write-Host "Pour Gmail, vous devez utiliser un 'Mot de passe d'application'" -ForegroundColor Yellow
        Write-Host "Créez-en un ici: https://myaccount.google.com/apppasswords" -ForegroundColor Yellow
    }
    $Password = Read-Host "Entrez le mot de passe (ou mot de passe d'application)" -AsSecureString
    $Password = [Runtime.InteropServices.Marshal]::PtrToStringAuto(
        [Runtime.InteropServices.Marshal]::SecureStringToBSTR($Password)
    )
}

Write-Host ""
Write-Host "Mise à jour de la configuration..." -ForegroundColor Yellow

# Lire le contenu
$content = Get-Content $envFile
$newContent = @()
$mailKeys = @{
    "MAIL_MAILER" = "smtp"
    "MAIL_HOST" = $mailConfig.Host
    "MAIL_PORT" = $mailConfig.Port.ToString()
    "MAIL_USERNAME" = $Email
    "MAIL_PASSWORD" = $Password
    "MAIL_ENCRYPTION" = $mailConfig.Encryption
    "MAIL_FROM_ADDRESS" = $Email
    "MAIL_FROM_NAME" = "SmartDataVault"
}

$foundKeys = @{}

foreach ($line in $content) {
    $updated = $false
    foreach ($key in $mailKeys.Keys) {
        if ($line -match "^$key=") {
            $newContent += "$key=$($mailKeys[$key])"
            $foundKeys[$key] = $true
            $updated = $true
            break
        }
    }
    if (-not $updated) {
        $newContent += $line
    }
}

# Ajouter les clés manquantes
foreach ($key in $mailKeys.Keys) {
    if (-not $foundKeys.ContainsKey($key)) {
        $newContent += "$key=$($mailKeys[$key])"
    }
}

# Écrire le nouveau contenu
$newContent | Set-Content $envFile

Write-Host "✓ Configuration email mise à jour avec succès!" -ForegroundColor Green
Write-Host ""
Write-Host "Configuration:" -ForegroundColor Cyan
Write-Host "  Provider: $Provider" -ForegroundColor White
Write-Host "  Email: $Email" -ForegroundColor White
Write-Host "  Host: $($mailConfig.Host)" -ForegroundColor White
Write-Host "  Port: $($mailConfig.Port)" -ForegroundColor White
Write-Host "  Encryption: $($mailConfig.Encryption)" -ForegroundColor White
Write-Host ""

# Vider le cache
Write-Host "Vidage du cache..." -ForegroundColor Yellow
php artisan config:clear | Out-Null
php artisan cache:clear | Out-Null

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "✓ Configuration terminée!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
