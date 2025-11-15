<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewPasswordMail;

class ResetUserPassword extends Command
{
    protected $signature = 'user:reset-password {email} {--send-email : Envoyer le nouveau mot de passe par email}';
    protected $description = 'Réinitialise le mot de passe d\'un utilisateur';

    public function handle(): int
    {
        $email = $this->argument('email');
        $sendEmail = $this->option('send-email');

        $this->info("Recherche de l'utilisateur: {$email}");

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("❌ Utilisateur non trouvé avec l'email: {$email}");
            return Command::FAILURE;
        }

        $this->info("✓ Utilisateur trouvé: {$user->name}");

        $newPassword = $this->generatePassword();

        $user->forceFill([
            'password' => Hash::make($newPassword),
        ])->save();

        $this->info("\n========================================");
        $this->info("✓ Mot de passe réinitialisé!");
        $this->info("========================================\n");
        $this->line("Nouveau mot de passe: <fg=green>{$newPassword}</>");

        if ($sendEmail) {
            try {
                Mail::to($user->email)->send(new NewPasswordMail($user, $newPassword));
                $this->info("\n✓ Email envoyé avec succès à: {$user->email}");
            } catch (\Exception $e) {
                $this->warn("\n⚠️  Erreur lors de l'envoi de l'email: " . $e->getMessage());
                $this->line("Le mot de passe a été mis à jour dans la base de données.");
            }
        }

        $this->warn("\n⚠️  Changez ce mot de passe après votre première connexion!");
        return Command::SUCCESS;
    }

    private function generatePassword(int $length = 12): string
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $all = $lowercase . $uppercase . $numbers;

        $password = '';
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];

        for ($i = 5; $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        return str_shuffle($password);
    }
}
