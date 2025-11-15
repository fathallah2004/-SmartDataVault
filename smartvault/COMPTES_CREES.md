# 👥 Comptes Créés - SmartDataVault

## ✅ Comptes créés avec succès !

### 🔑 Administrateur

| Email | Mot de passe | Rôle |
|-------|--------------|------|
| `admin@smartvault.com` | `admin123` | Admin |

**Accès :** Tableau de bord administrateur avec gestion des utilisateurs et fichiers.

---

### 👤 Utilisateurs (10)

| # | Email | Mot de passe |
|---|-------|--------------|
| 1 | `user1@smartvault.com` | `user1123` |
| 2 | `user2@smartvault.com` | `user2123` |
| 3 | `user3@smartvault.com` | `user3123` |
| 4 | `user4@smartvault.com` | `user4123` |
| 5 | `user5@smartvault.com` | `user5123` |
| 6 | `user6@smartvault.com` | `user6123` |
| 7 | `user7@smartvault.com` | `user7123` |
| 8 | `user8@smartvault.com` | `user8123` |
| 9 | `user9@smartvault.com` | `user9123` |
| 10 | `user10@smartvault.com` | `user10123` |

**Accès :** Tableau de bord utilisateur avec gestion des fichiers chiffrés.

---

## 🚀 Comment se connecter

1. Allez sur : **http://127.0.0.1:8000/login**
2. Entrez l'email et le mot de passe du compte souhaité
3. Cliquez sur "Se connecter"

---

## 📝 Commandes utiles

### Créer plus d'utilisateurs :
```bash
php artisan users:create --count=20
```

### Créer seulement un admin :
```bash
php artisan users:create --admin
```

### Réinitialiser un mot de passe :
```bash
php artisan user:reset-password email@example.com
```

---

## ⚠️ Important

- **Changez les mots de passe** après la première connexion pour la sécurité
- Les comptes sont créés avec `email_verified_at` défini (emails vérifiés)
- Tous les comptes utilisateurs ont le rôle `user` par défaut
- Le compte admin a le rôle `admin` et accès au tableau de bord administrateur

---

**Date de création :** 2025-11-15

