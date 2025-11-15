# 📧 Guide Étape par Étape : Configuration Email Gmail

## Étape 1 : Créer un Mot de Passe d'Application Gmail

1. **Allez sur** : https://myaccount.google.com/apppasswords
2. **Connectez-vous** avec votre compte Gmail
3. **Si la validation en 2 étapes n'est pas activée** :
   - Allez dans : Sécurité → Validation en 2 étapes
   - Activez-la d'abord
4. **Créez un mot de passe d'application** :
   - Sélectionnez "Autre (nom personnalisé)"
   - Entrez "SmartDataVault" comme nom
   - Cliquez sur "Générer"
   - **Copiez le mot de passe** (16 caractères, format : xxxx xxxx xxxx xxxx)
   - ⚠️ **Important** : Vous ne pourrez plus voir ce mot de passe après, alors copiez-le maintenant !

## Étape 2 : Ouvrir le fichier .env

### Méthode 1 : Avec VS Code (Recommandé)
1. Ouvrez VS Code
2. Fichier → Ouvrir un dossier
3. Sélectionnez le dossier `smartvault`
4. Dans l'explorateur de fichiers, cliquez sur `.env`

### Méthode 2 : Avec Notepad++
1. Ouvrez Notepad++
2. Fichier → Ouvrir
3. Naviguez vers : `C:\Users\fatha\Herd\-SmartDataVault\smartvault`
4. Sélectionnez `.env` (vous devrez peut-être choisir "Tous les fichiers" dans le filtre)

### Méthode 3 : Avec le Bloc-notes Windows
1. Ouvrez l'Explorateur de fichiers
2. Naviguez vers : `C:\Users\fatha\Herd\-SmartDataVault\smartvault`
3. Clic droit sur `.env` → Ouvrir avec → Bloc-notes

## Étape 3 : Modifier le fichier .env

Cherchez les lignes qui commencent par `MAIL_` et modifiez-les comme suit :

**Remplacez :**
```env
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Par :**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=xxxx xxxx xxxx xxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=votre-email@gmail.com
MAIL_FROM_NAME="SmartDataVault"
```

**⚠️ Important :**
- Remplacez `votre-email@gmail.com` par votre vraie adresse Gmail
- Remplacez `xxxx xxxx xxxx xxxx` par le mot de passe d'application que vous avez copié (vous pouvez enlever les espaces)
- Gardez les guillemets autour de "SmartDataVault"

**Exemple concret :**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=fathallahamine2004@gmail.com
MAIL_PASSWORD=abcd efgh ijkl mnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=fathallahamine2004@gmail.com
MAIL_FROM_NAME="SmartDataVault"
```

## Étape 4 : Sauvegarder le fichier

1. **Avec VS Code** : Ctrl + S
2. **Avec Notepad++** : Ctrl + S
3. **Avec Bloc-notes** : Fichier → Enregistrer

## Étape 5 : Vider le cache

Ouvrez PowerShell dans le dossier du projet et exécutez :

```powershell
php artisan config:clear
php artisan cache:clear
```

Vous devriez voir :
```
INFO  Configuration cache cleared successfully.
INFO  Application cache cleared successfully.
```

## Étape 6 : Tester l'envoi d'email

1. Allez sur votre site : http://127.0.0.1:8000
2. Cliquez sur "Forgot Password"
3. Entrez votre email
4. Cliquez sur "Send New Password"
5. Vérifiez votre boîte Gmail (et les spams si nécessaire)

## ✅ Vérification

Pour vérifier que la configuration est correcte, exécutez :

```powershell
php test-email.php
```

Vous devriez voir :
```
MAIL_MAILER: smtp
MAIL_HOST: smtp.gmail.com
MAIL_PORT: 587
MAIL_USERNAME: défini
MAIL_PASSWORD: défini
```

## ❌ Problèmes courants

### Erreur "Connection refused"
- Vérifiez que le port 587 n'est pas bloqué par le firewall
- Essayez le port 465 avec `MAIL_ENCRYPTION=ssl`

### Erreur "Authentication failed"
- Vérifiez que vous utilisez un **mot de passe d'application** (pas votre mot de passe Gmail)
- Vérifiez que la validation en 2 étapes est activée

### L'email n'arrive pas
- Vérifiez les spams/courrier indésirable
- Vérifiez les logs : `storage/logs/laravel.log`
- Attendez quelques minutes (parfois il y a un délai)

## 📝 Résumé rapide

1. Créer mot de passe d'application Gmail : https://myaccount.google.com/apppasswords
2. Ouvrir `.env` dans VS Code/Notepad++
3. Modifier les lignes `MAIL_*` avec vos informations
4. Sauvegarder (Ctrl + S)
5. Exécuter : `php artisan config:clear` et `php artisan cache:clear`
6. Tester l'envoi d'email

---

**Besoin d'aide ?** Exécutez `php test-email.php` pour voir l'état de votre configuration.

