# 📧 Guide de Configuration Email - SmartDataVault

## ⚠️ Problème actuel
Les emails sont enregistrés dans les logs au lieu d'être envoyés. Vous devez configurer l'envoi d'emails.

---

## ✅ Solution : Étapes à suivre

### **Méthode 1 : Script PowerShell (Recommandé)**

Exécutez simplement :
```powershell
.\configure-email.ps1
```

Le script vous guidera pour configurer votre email.

**Options avancées :**
```powershell
# Gmail
.\configure-email.ps1 -Email "votre-email@gmail.com" -Password "app-password" -Provider "gmail"

# Outlook
.\configure-email.ps1 -Email "votre-email@outlook.com" -Password "votre-mot-de-passe" -Provider "outlook"

# Mailtrap (pour développement)
.\configure-email.ps1 -Email "votre-username" -Password "votre-password" -Provider "mailtrap"
```

### **Méthode 2 : Configuration manuelle**

1. Ouvrez le fichier `.env` à la racine du projet
2. Ajoutez/modifiez ces lignes :

#### **Option A : Gmail (Recommandé pour les tests)**

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

**⚠️ Important pour Gmail :**
- Vous devez utiliser un **"Mot de passe d'application"** (pas votre mot de passe normal)
- Pour créer un mot de passe d'application :
  1. Allez sur https://myaccount.google.com
  2. Sécurité → Validation en 2 étapes (doit être activée)
  3. Mots de passe des applications → Créer
  4. Utilisez ce mot de passe dans `MAIL_PASSWORD`

#### **Option B : Outlook/Hotmail**

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@outlook.com
MAIL_PASSWORD=votre-mot-de-passe
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=votre-email@outlook.com
MAIL_FROM_NAME="SmartDataVault"
```

#### **Option C : Mailtrap (Pour développement - GRATUIT)**

1. Créez un compte sur https://mailtrap.io (gratuit)
2. Allez dans "Inboxes" → "SMTP Settings"
3. Copiez les identifiants et ajoutez dans `.env` :

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

### **Étape 3 : Vider le cache**

Après avoir modifié le `.env`, exécutez ces commandes :

```bash
php artisan config:clear
php artisan cache:clear
```

Ou utilisez le script PowerShell qui le fait automatiquement.

### **Étape 4 : Tester**

1. Allez sur votre site
2. Essayez de vous connecter avec un mauvais mot de passe
3. Cliquez sur "Forgot Password"
4. Cliquez sur "Send New Password"
5. Vérifiez votre boîte email (ou Mailtrap si vous l'utilisez)

---

## 🔍 Vérifier si ça fonctionne

### Si vous utilisez Mailtrap :
- Allez sur https://mailtrap.io
- Cliquez sur "Inboxes" → Votre inbox
- Vous verrez l'email avec le nouveau mot de passe

### Si vous utilisez Gmail/Outlook :
- Vérifiez votre boîte de réception
- Vérifiez aussi les spams/courrier indésirable

### Si ça ne fonctionne pas :
1. Vérifiez les logs : `storage/logs/laravel.log`
2. Vérifiez que `MAIL_MAILER=smtp` (pas `log`)
3. Vérifiez que tous les paramètres sont corrects
4. Pour Gmail, assurez-vous d'utiliser un "Mot de passe d'application"

---

## 🛠️ Commandes utiles

### Réinitialiser le mot de passe d'un utilisateur :
```bash
php artisan user:reset-password email@example.com
```

### Réinitialiser et envoyer par email :
```bash
php artisan user:reset-password email@example.com --send-email
```

---

## ❓ Besoin d'aide ?

Si vous avez des problèmes :
1. Vérifiez que le fichier `.env` est bien sauvegardé
2. Vérifiez que vous avez exécuté `php artisan config:clear`
3. Consultez les logs dans `storage/logs/laravel.log`
4. Essayez Mailtrap pour tester facilement
