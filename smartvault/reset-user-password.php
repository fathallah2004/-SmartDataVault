<?php
/**
 * Script pour réinitialiser le mot de passe d'un utilisateur
 * Usage: php reset-user-password.php email@example.com
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = $argv[1] ?? 'fathallahamine2004@gmail.com';

echo "Recherche de l'utilisateur: $email\n";

$user = User::where('email', $email)->first();

if (!$user) {
    echo "❌ Utilisateur non trouvé avec l'email: $email\n";
    exit(1);
}

echo "✓ Utilisateur trouvé: {$user->name}\n";

// Générer un nouveau mot de passe simple (sans caractères spéciaux)
$lowercase = 'abcdefghijklmnopqrstuvwxyz';
$uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
$numbers = '0123456789';
$all = $lowercase . $uppercase . $numbers;

$newPassword = '';
$newPassword .= $lowercase[random_int(0, strlen($lowercase) - 1)];
$newPassword .= $uppercase[random_int(0, strlen($uppercase) - 1)];
$newPassword .= $numbers[random_int(0, strlen($numbers) - 1)];
$newPassword .= $uppercase[random_int(0, strlen($uppercase) - 1)];
$newPassword .= $numbers[random_int(0, strlen($numbers) - 1)];

for ($i = 5; $i < 12; $i++) {
    $newPassword .= $all[random_int(0, strlen($all) - 1)];
}

$newPassword = str_shuffle($newPassword);

// Mettre à jour le mot de passe
$user->forceFill([
    'password' => Hash::make($newPassword),
])->save();

echo "\n";
echo "========================================\n";
echo "✓ Mot de passe réinitialisé!\n";
echo "========================================\n";
echo "\n";
echo "Nouveau mot de passe: $newPassword\n";
echo "\n";
echo "Vous pouvez maintenant vous connecter avec ce mot de passe.\n";
echo "⚠️  Changez ce mot de passe après votre première connexion!\n";
echo "\n";

