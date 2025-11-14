# Configuration Email - SmartDataVault

## Problème actuel

Le système est actuellement configuré pour **logger les emails** au lieu de les envoyer réellement. Cela signifie que les emails sont enregistrés dans `storage/logs/laravel.log` au lieu d'être envoyés par email.

## Solution : Configurer l'envoi d'emails réels

### Option 1 : Configuration SMTP (Recommandé)

1. Ouvrez votre fichier `.env` à la racine du projet
2. Ajoutez/modifiez ces lignes :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=votre-mot-de-passe-app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=votre-email@gmail.com
MAIL_FROM_NAME="SmartDataVault"
```

**Pour Gmail :**
- Vous devez utiliser un "Mot de passe d'application" au lieu de votre mot de passe normal
- Allez dans : Paramètres Google → Sécurité → Validation en 2 étapes → Mots de passe des applications

**Pour d'autres services SMTP :**
- **Outlook/Hotmail** : `smtp-mail.outlook.com`, port 587
- **Yahoo** : `smtp.mail.yahoo.com`, port 587
- **Autre serveur SMTP** : Utilisez les paramètres fournis par votre hébergeur

### Option 2 : Mailtrap (Pour le développement)

Mailtrap est un service qui capture les emails pour les tests :

1. Créez un compte sur [mailtrap.io](https://mailtrap.io)
2. Configurez dans `.env` :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=votre-username-mailtrap
MAIL_PASSWORD=votre-password-mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@smartvault.com
MAIL_FROM_NAME="SmartDataVault"
```

### Option 3 : Mailgun (Pour la production)

1. Créez un compte sur [mailgun.com](https://mailgun.com)
2. Configurez dans `.env` :

```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=votre-domaine.mailgun.org
MAILGUN_SECRET=votre-secret-key
MAIL_FROM_ADDRESS=noreply@votre-domaine.com
MAIL_FROM_NAME="SmartDataVault"
```

Et dans `config/services.php`, ajoutez :

```php
'mailgun' => [
    'domain' => env('MAILGUN_DOMAIN'),
    'secret' => env('MAILGUN_SECRET'),
    'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    'scheme' => 'https',
],
```

## Vérifier la configuration

Après avoir modifié le `.env`, exécutez :

```bash
php artisan config:clear
php artisan cache:clear
```

## Tester l'envoi d'email

1. Essayez de vous connecter avec un mauvais mot de passe
2. Cliquez sur "Forgot Password"
3. Cliquez sur "Send New Password"
4. Vérifiez votre boîte email (ou les logs si toujours en mode log)

## Voir les emails dans les logs (Mode développement)

Si vous êtes toujours en mode `log`, vous pouvez voir les emails dans :

```
storage/logs/laravel.log
```

Recherchez les lignes contenant "New password email" ou "Your New Password".

## Dépannage

### L'email n'est toujours pas envoyé

1. Vérifiez que `MAIL_MAILER` n'est pas `log` dans `.env`
2. Vérifiez les logs : `storage/logs/laravel.log`
3. Vérifiez que les paramètres SMTP sont corrects
4. Testez avec Mailtrap pour isoler le problème

### Erreur "Connection refused"

- Vérifiez que le port n'est pas bloqué par le firewall
- Vérifiez que les identifiants SMTP sont corrects
- Essayez un autre port (465 pour SSL, 587 pour TLS)

### Erreur "Authentication failed"

- Pour Gmail, utilisez un "Mot de passe d'application"
- Vérifiez que l'email et le mot de passe sont corrects
- Activez "Accès aux applications moins sécurisées" (non recommandé)

