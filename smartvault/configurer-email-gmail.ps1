# Script interactif pour configurer Gmail dans .env
# Usage: .\configurer-email-gmail.ps1

$envFile = ".env"

if (-not (Test-Path $envFile)) {
    Write-Host "Erreur: Le fichier .env n'existe pas!" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Configuration Email Gmail - SmartDataVault" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "ÉTAPE 1 : Créer un Mot de Passe d'Application Gmail" -ForegroundColor Yellow
Write-Host ""
Write-Host "1. Allez sur : https://myaccount.google.com/apppasswords" -ForegroundColor White
Write-Host "2. Connectez-vous avec votre compte Gmail" -ForegroundColor White
Write-Host "3. Si la validation en 2 étapes n'est pas activée, activez-la d'abord" -ForegroundColor White
Write-Host "4. Créez un mot de passe d'application :" -ForegroundColor White
Write-Host "   - Sélectionnez 'Autre (nom personnalisé)'" -ForegroundColor Gray
Write-Host "   - Entrez 'SmartDataVault' comme nom" -ForegroundColor Gray
Write-Host "   - Cliquez sur 'Générer'" -ForegroundColor Gray
Write-Host "   - COPIEZ le mot de passe (16 caractères)" -ForegroundColor Green
Write-Host ""
Write-Host "Appuyez sur Entrée quand vous avez créé le mot de passe d'application..." -ForegroundColor Yellow
Read-Host

Write-Host ""
Write-Host "ÉTAPE 2 : Entrer vos informations" -ForegroundColor Yellow
Write-Host ""

# Demander l'email
$email = Read-Host "Entrez votre adresse email Gmail (ex: votre-email@gmail.com)"
if ([string]::IsNullOrWhiteSpace($email)) {
    Write-Host "Erreur: L'email ne peut pas être vide!" -ForegroundColor Red
    exit 1
}

# Vérifier que c'est un email Gmail
if ($email -notmatch "@gmail\.com$") {
    Write-Host "Attention: Cet email ne semble pas être un Gmail (@gmail.com)" -ForegroundColor Yellow
    $continue = Read-Host "Continuer quand même? (O/N)"
    if ($continue -ne "O" -and $continue -ne "o") {
        exit 0
    }
}

# Demander le mot de passe d'application
Write-Host ""
Write-Host "Entrez le mot de passe d'application (16 caractères)" -ForegroundColor Cyan
Write-Host "Format: xxxx xxxx xxxx xxxx (vous pouvez enlever les espaces)" -ForegroundColor Gray
$password = Read-Host "Mot de passe d'application" -AsSecureString
$passwordPlain = [Runtime.InteropServices.Marshal]::PtrToStringAuto(
    [Runtime.InteropServices.Marshal]::SecureStringToBSTR($password)
)

if ([string]::IsNullOrWhiteSpace($passwordPlain)) {
    Write-Host "Erreur: Le mot de passe ne peut pas être vide!" -ForegroundColor Red
    exit 1
}

# Enlever les espaces du mot de passe
$passwordPlain = $passwordPlain -replace '\s', ''

Write-Host ""
Write-Host "Mise à jour du fichier .env..." -ForegroundColor Yellow

# Lire le contenu du fichier
$content = Get-Content $envFile
$newContent = @()
$mailKeysUpdated = @{}

foreach ($line in $content) {
    $updated = $false
    
    if ($line -match "^MAIL_MAILER=") {
        $newContent += "MAIL_MAILER=smtp"
        $mailKeysUpdated['MAIL_MAILER'] = $true
        $updated = $true
    }
    elseif ($line -match "^MAIL_HOST=") {
        $newContent += "MAIL_HOST=smtp.gmail.com"
        $mailKeysUpdated['MAIL_HOST'] = $true
        $updated = $true
    }
    elseif ($line -match "^MAIL_PORT=") {
        $newContent += "MAIL_PORT=587"
        $mailKeysUpdated['MAIL_PORT'] = $true
        $updated = $true
    }
    elseif ($line -match "^MAIL_USERNAME=") {
        $newContent += "MAIL_USERNAME=$email"
        $mailKeysUpdated['MAIL_USERNAME'] = $true
        $updated = $true
    }
    elseif ($line -match "^MAIL_PASSWORD=") {
        $newContent += "MAIL_PASSWORD=$passwordPlain"
        $mailKeysUpdated['MAIL_PASSWORD'] = $true
        $updated = $true
    }
    elseif ($line -match "^MAIL_ENCRYPTION=") {
        $newContent += "MAIL_ENCRYPTION=tls"
        $mailKeysUpdated['MAIL_ENCRYPTION'] = $true
        $updated = $true
    }
    elseif ($line -match "^MAIL_FROM_ADDRESS=") {
        $newContent += "MAIL_FROM_ADDRESS=$email"
        $mailKeysUpdated['MAIL_FROM_ADDRESS'] = $true
        $updated = $true
    }
    elseif ($line -match "^MAIL_FROM_NAME=") {
        $newContent += 'MAIL_FROM_NAME="SmartDataVault"'
        $mailKeysUpdated['MAIL_FROM_NAME'] = $true
        $updated = $true
    }
    
    if (-not $updated) {
        $newContent += $line
    }
}

# Ajouter les clés manquantes
if (-not $mailKeysUpdated.ContainsKey('MAIL_MAILER')) {
    $newContent += "MAIL_MAILER=smtp"
}
if (-not $mailKeysUpdated.ContainsKey('MAIL_HOST')) {
    $newContent += "MAIL_HOST=smtp.gmail.com"
}
if (-not $mailKeysUpdated.ContainsKey('MAIL_PORT')) {
    $newContent += "MAIL_PORT=587"
}
if (-not $mailKeysUpdated.ContainsKey('MAIL_USERNAME')) {
    $newContent += "MAIL_USERNAME=$email"
}
if (-not $mailKeysUpdated.ContainsKey('MAIL_PASSWORD')) {
    $newContent += "MAIL_PASSWORD=$passwordPlain"
}
if (-not $mailKeysUpdated.ContainsKey('MAIL_ENCRYPTION')) {
    $newContent += "MAIL_ENCRYPTION=tls"
}
if (-not $mailKeysUpdated.ContainsKey('MAIL_FROM_ADDRESS')) {
    $newContent += "MAIL_FROM_ADDRESS=$email"
}
if (-not $mailKeysUpdated.ContainsKey('MAIL_FROM_NAME')) {
    $newContent += 'MAIL_FROM_NAME="SmartDataVault"'
}

# Écrire le nouveau contenu
$newContent | Set-Content $envFile -Encoding UTF8

Write-Host "✓ Configuration email mise à jour avec succès!" -ForegroundColor Green
Write-Host ""
Write-Host "Configuration:" -ForegroundColor Cyan
Write-Host "  Email: $email" -ForegroundColor White
Write-Host "  Host: smtp.gmail.com" -ForegroundColor White
Write-Host "  Port: 587" -ForegroundColor White
Write-Host "  Encryption: tls" -ForegroundColor White
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
Write-Host "Pour tester l'envoi d'email:" -ForegroundColor Yellow
Write-Host "1. Allez sur http://127.0.0.1:8000" -ForegroundColor White
Write-Host "2. Cliquez sur 'Forgot Password'" -ForegroundColor White
Write-Host "3. Entrez votre email" -ForegroundColor White
Write-Host "4. Cliquez sur 'Send New Password'" -ForegroundColor White
Write-Host "5. Vérifiez votre boîte Gmail (et les spams)" -ForegroundColor White
Write-Host ""
Write-Host "Ou testez avec:" -ForegroundColor Yellow
Write-Host "  php artisan user:reset-password $email --send-email" -ForegroundColor Cyan
Write-Host ""

