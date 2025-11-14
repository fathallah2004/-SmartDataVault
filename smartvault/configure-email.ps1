# Script interactif pour configurer l'email Gmail
# Mot de passe d'application: oxwnuehrnjtcmbyi

$password = "oxwnuehrnjtcmbyi"
$envFile = ".env"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Configuration Email Gmail" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

if (-not (Test-Path $envFile)) {
    Write-Host "Erreur: Le fichier .env n'existe pas!" -ForegroundColor Red
    exit 1
}

# Demander l'email
$email = Read-Host "Entrez votre adresse email Gmail (ex: monemail@gmail.com)"

if ([string]::IsNullOrWhiteSpace($email)) {
    Write-Host "Erreur: L'email ne peut pas être vide!" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Mise à jour de la configuration..." -ForegroundColor Yellow

# Lire le contenu
$content = Get-Content $envFile
$newContent = @()

$mailEncryptionExists = $false
$mailFromAddressExists = $false
$mailFromNameExists = $false

foreach ($line in $content) {
    if ($line -match "^MAIL_MAILER=") {
        $newContent += "MAIL_MAILER=smtp"
    }
    elseif ($line -match "^MAIL_HOST=") {
        $newContent += "MAIL_HOST=smtp.gmail.com"
    }
    elseif ($line -match "^MAIL_PORT=") {
        $newContent += "MAIL_PORT=587"
    }
    elseif ($line -match "^MAIL_USERNAME=") {
        $newContent += "MAIL_USERNAME=$email"
    }
    elseif ($line -match "^MAIL_PASSWORD=") {
        $newContent += "MAIL_PASSWORD=$password"
    }
    elseif ($line -match "^MAIL_ENCRYPTION=") {
        $newContent += "MAIL_ENCRYPTION=tls"
        $mailEncryptionExists = $true
    }
    elseif ($line -match "^MAIL_FROM_ADDRESS=") {
        $newContent += "MAIL_FROM_ADDRESS=$email"
        $mailFromAddressExists = $true
    }
    elseif ($line -match "^MAIL_FROM_NAME=") {
        $newContent += "MAIL_FROM_NAME=`"SmartDataVault`""
        $mailFromNameExists = $true
    }
    else {
        $newContent += $line
    }
}

# Ajouter les lignes manquantes si elles n'existent pas
if (-not $mailEncryptionExists) {
    # Trouver où insérer (après MAIL_PASSWORD)
    $insertIndex = -1
    for ($i = 0; $i -lt $newContent.Length; $i++) {
        if ($newContent[$i] -match "^MAIL_PASSWORD=") {
            $insertIndex = $i + 1
            break
        }
    }
    if ($insertIndex -ge 0) {
        $newContent = $newContent[0..($insertIndex-1)] + "MAIL_ENCRYPTION=tls" + $newContent[$insertIndex..($newContent.Length-1)]
    } else {
        $newContent += "MAIL_ENCRYPTION=tls"
    }
}

if (-not $mailFromAddressExists) {
    $newContent += "MAIL_FROM_ADDRESS=$email"
}

if (-not $mailFromNameExists) {
    $newContent += "MAIL_FROM_NAME=`"SmartDataVault`""
}

# Écrire le nouveau contenu
$newContent | Set-Content $envFile

Write-Host ""
Write-Host "✓ Configuration email mise à jour avec succès!" -ForegroundColor Green
Write-Host ""
Write-Host "Configuration:" -ForegroundColor Cyan
Write-Host "  Email: $email" -ForegroundColor White
Write-Host "  Host: smtp.gmail.com" -ForegroundColor White
Write-Host "  Port: 587" -ForegroundColor White
Write-Host "  Encryption: tls" -ForegroundColor White
Write-Host ""
Write-Host "Étape suivante: Vider le cache..." -ForegroundColor Yellow
Write-Host ""

# Vider le cache
Write-Host "Exécution de: php artisan config:clear" -ForegroundColor Gray
php artisan config:clear
Write-Host "Exécution de: php artisan cache:clear" -ForegroundColor Gray
php artisan cache:clear

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "✓ Configuration terminée!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Vous pouvez maintenant tester l'envoi d'email:" -ForegroundColor Yellow
Write-Host "1. Allez sur votre site" -ForegroundColor White
Write-Host "2. Essayez de vous connecter avec un mauvais mot de passe" -ForegroundColor White
Write-Host "3. Cliquez sur 'Forgot Password'" -ForegroundColor White
Write-Host "4. Cliquez sur 'Send New Password'" -ForegroundColor White
Write-Host "5. Vérifiez votre boîte email Gmail" -ForegroundColor White
Write-Host ""

