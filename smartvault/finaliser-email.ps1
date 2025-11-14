# Script final pour remplacer le placeholder email
param(
    [Parameter(Mandatory=$false)]
    [string]$Email
)

$envFile = ".env"

if ([string]::IsNullOrWhiteSpace($Email)) {
    $Email = Read-Host "Entrez votre adresse email Gmail (ex: monemail@gmail.com)"
}

if ([string]::IsNullOrWhiteSpace($Email)) {
    Write-Host "Erreur: L'email ne peut pas être vide!" -ForegroundColor Red
    exit 1
}

Write-Host "Remplacement de VOTRE_EMAIL@gmail.com par $Email..." -ForegroundColor Yellow

$content = Get-Content $envFile
$newContent = $content -replace "VOTRE_EMAIL@gmail.com", $Email

$newContent | Set-Content $envFile

Write-Host "✓ Email remplacé avec succès!" -ForegroundColor Green
Write-Host ""
Write-Host "Configuration finale:" -ForegroundColor Cyan
Get-Content $envFile | Select-String -Pattern "^MAIL_" | ForEach-Object { Write-Host "  $_" -ForegroundColor White }
Write-Host ""
Write-Host "✓ Configuration terminée! Vous pouvez maintenant tester l'envoi d'email." -ForegroundColor Green

