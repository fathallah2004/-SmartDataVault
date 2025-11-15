<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUsers extends Command
{
    protected $signature = 'users:create {--admin : Créer seulement un compte admin} {--count=10 : Nombre d\'utilisateurs à créer}';
    protected $description = 'Créer un compte admin et des utilisateurs de test';

    public function handle(): int
    {
        if ($this->option('admin')) {
            return $this->createAdmin();
        }

        $this->info('Création d\'un compte admin et de ' . $this->option('count') . ' utilisateurs...');
        $this->newLine();

        $this->createAdmin();
        $this->newLine();

        $count = (int) $this->option('count');
        $this->createUsers($count);

        $this->newLine();
        $this->info('✓ Tous les comptes ont été créés avec succès!');
        
        return Command::SUCCESS;
    }

    private function createAdmin(): int
    {
        $adminEmail = 'admin@smartvault.com';
        $adminPassword = 'admin123';

        $existingAdmin = User::where('email', $adminEmail)->first();
        
        if ($existingAdmin) {
            if ($existingAdmin->role !== 'admin') {
                $existingAdmin->role = 'admin';
                $existingAdmin->password = Hash::make($adminPassword);
                $existingAdmin->save();
                $this->info("✓ Compte existant promu en administrateur: {$adminEmail}");
            } else {
                $this->warn("⚠️  L'administrateur {$adminEmail} existe déjà.");
                $this->line("   Pour réinitialiser le mot de passe: php artisan user:reset-password {$adminEmail}");
            }
        } else {
            User::create([
                'name' => 'Administrateur',
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);

            $this->info("✓ Compte administrateur créé:");
            $this->line("   Email: {$adminEmail}");
            $this->line("   Mot de passe: {$adminPassword}");
        }

        return Command::SUCCESS;
    }

    private function createUsers(int $count): void
    {
        $this->info("Création de {$count} utilisateurs...");
        
        $users = [];
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 1; $i <= $count; $i++) {
            $email = "user{$i}@smartvault.com";
            $password = "user{$i}123";

            $existingUser = User::where('email', $email)->first();
            
            if ($existingUser) {
                $bar->advance();
                continue;
            }

            $user = User::create([
                'name' => "Utilisateur {$i}",
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'user',
                'email_verified_at' => now(),
            ]);

            $users[] = [
                'email' => $email,
                'password' => $password,
            ];

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if (!empty($users)) {
            $this->table(
                ['Email', 'Mot de passe'],
                array_map(fn($u) => [$u['email'], $u['password']], $users)
            );
        } else {
            $this->warn('Tous les utilisateurs existent déjà.');
        }
    }
}
