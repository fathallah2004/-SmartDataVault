# 📧 Guide : Configurer l'envoi d'emails

## ⚠️ Problème actuel
Les emails sont enregistrés dans les logs au lieu d'être envoyés. Vous devez configurer l'envoi d'emails.

---

## ✅ Solution : Étapes à suivre

### **Étape 1 : Ouvrir le fichier .env**

1. Allez dans le dossier du projet : `C:\Users\fatha\Herd\-SmartDataVault\smartvault`
2. Ouvrez le fichier `.env` avec un éditeur de texte (Notepad++, VS Code, etc.)

### **Étape 2 : Configurer l'email SMTP**

Ajoutez ou modifiez ces lignes dans votre fichier `.env` :

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

Après avoir modifié le `.env`, exécutez ces commandes dans le terminal :

```bash
php artisan config:clear
php artisan cache:clear
```

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

## 📝 Exemple complet de configuration .env

```env
# ... autres configurations ...

# Configuration Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=monemail@gmail.com
MAIL_PASSWORD=abcd efgh ijkl mnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=monemail@gmail.com
MAIL_FROM_NAME="SmartDataVault"
```

---

## ❓ Besoin d'aide ?

Si vous avez des problèmes :
1. Vérifiez que le fichier `.env` est bien sauvegardé
2. Vérifiez que vous avez exécuté `php artisan config:clear`
3. Consultez les logs dans `storage/logs/laravel.log`
4. Essayez Mailtrap pour tester facilement

