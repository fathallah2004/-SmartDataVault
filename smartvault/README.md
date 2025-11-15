# 🔒 SmartDataVault

Application web sécurisée de gestion et chiffrement de fichiers développée avec Laravel 12.

## ✨ Fonctionnalités

- 🔐 **Chiffrement de fichiers** : Support de multiples algorithmes (César, Vigenère, XOR, AES, etc.)
- 🖼️ **Chiffrement d'images** : Chiffrement spécialisé pour les fichiers image
- 👥 **Gestion multi-utilisateurs** : Système de rôles (admin/user)
- 🔑 **Authentification à deux facteurs** : Sécurité renforcée avec 2FA
- 📊 **Tableau de bord** : Statistiques et gestion des fichiers
- 📧 **Notifications email** : Réinitialisation de mot de passe par email

## 🚀 Installation

### Prérequis

- PHP 8.2+
- Composer
- Node.js & npm
- SQLite (ou MySQL/PostgreSQL)

### Étapes d'installation

1. **Cloner le projet**
```bash
git clone <repository-url>
cd smartvault
```

2. **Installer les dépendances**
```bash
composer install
npm install
```

3. **Configuration**
```bash
# Copier le fichier .env
cp .env.example .env

# Générer la clé d'application
php artisan key:generate

# Créer la base de données SQLite
touch database/database.sqlite
```

4. **Migrations**
```bash
php artisan migrate
```

5. **Compiler les assets**
```bash
npm run build
```

6. **Lancer le serveur**
```bash
php artisan serve
```

L'application sera accessible sur `http://127.0.0.1:8000`

## 📧 Configuration Email

### Méthode rapide (Script PowerShell)

```powershell
.\configure-email.ps1
```

### Configuration manuelle

Voir le guide complet : [GUIDE_CONFIGURATION_EMAIL.md](GUIDE_CONFIGURATION_EMAIL.md)

## 🛠️ Commandes utiles

### Réinitialiser le mot de passe d'un utilisateur
```bash
php artisan user:reset-password email@example.com
```

### Réinitialiser et envoyer par email
```bash
php artisan user:reset-password email@example.com --send-email
```

### Visualiser les fichiers dans la base de données
```powershell
.\view-files.ps1
.\view-files.ps1 -Summary
.\view-files.ps1 -UserId 1
```

## 📁 Structure des données

Les fichiers chiffrés sont stockés dans la base de données SQLite :
- **Emplacement** : `database/database.sqlite`
- **Table principale** : `encrypted_files`
- Le contenu est chiffré et stocké dans la colonne `encrypted_content`

Pour plus de détails, consultez l'interface web ou utilisez le script `view-files.ps1`.

## 🔐 Algorithmes de chiffrement

### Textes
- César
- Vigenère
- XOR Textuel
- Substitution
- Inversion
- AES-256

### Images
- XOR Image
- AES-CTR Image
- AES-CBC Image

## 📝 Documentation

- [Guide de configuration email](GUIDE_CONFIGURATION_EMAIL.md)

## 🧪 Tests

```bash
php artisan test
```

## 📄 Licence

MIT License
