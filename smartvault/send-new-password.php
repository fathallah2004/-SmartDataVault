<?php
/**
 * Script pour envoyer un nouveau mot de passe par email
 * Usage: php send-new-password.php email@example.com
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Mail\NewPasswordMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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

echo "\n";
echo "Nouveau mot de passe généré: $newPassword\n";
echo "\n";

// Mettre à jour le mot de passe dans la base de données
$user->forceFill([
    'password' => Hash::make($newPassword),
])->save();

echo "✓ Mot de passe mis à jour dans la base de données\n";

// Envoyer l'email
try {
    echo "Envoi de l'email...\n";
    
    Mail::to($user->email)->send(new NewPasswordMail($user, $newPassword));
    
    echo "\n";
    echo "========================================\n";
    echo "✓ Email envoyé avec succès!\n";
    echo "========================================\n";
    echo "\n";
    echo "Le nouveau mot de passe a été envoyé à: {$user->email}\n";
    echo "Nouveau mot de passe: $newPassword\n";
    echo "\n";
    echo "⚠️  Vérifiez votre boîte email (et les spams si nécessaire)\n";
    echo "\n";
    
    Log::info('New password sent via script to: ' . $user->email . ' - Password: ' . $newPassword);
    
} catch (\Exception $e) {
    echo "\n";
    echo "❌ Erreur lors de l'envoi de l'email: " . $e->getMessage() . "\n";
    echo "\n";
    echo "Mais le mot de passe a été mis à jour dans la base de données.\n";
    echo "Nouveau mot de passe: $newPassword\n";
    echo "\n";
    echo "Vous pouvez vous connecter avec ce mot de passe.\n";
    echo "\n";
    
    Log::error('Failed to send password email: ' . $e->getMessage());
}

