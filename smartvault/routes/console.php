<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('user:make-admin {email} {--name=} {--password=}', function (string $email) {
    $name = $this->option('name') ?? $this->ask('Nom complet', 'Administrateur');
    $password = $this->option('password');

    if (!$password) {
        $password = $this->secret('Mot de passe');
    }

    if (!$password) {
        $this->error('Un mot de passe est requis.');
        return;
    }

    $user = User::firstWhere('email', $email);

    if ($user) {
        $user->name = $name ?? $user->name;
        $user->password = bcrypt($password);
        $user->role = 'admin';
        $user->save();

        $this->info("L'utilisateur existant {$email} a été promu administrateur.");
    } else {
        User::create([
            'name' => $name ?? 'Administrateur',
            'email' => $email,
            'password' => bcrypt($password),
            'role' => 'admin',
        ]);

        $this->info("Compte administrateur créé pour {$email}.");
    }

    $this->line("Identifiants : {$email} / {$password}");
})->purpose('Créer ou promouvoir un compte administrateur')
  ->describe('Utilisation : php artisan user:make-admin email@example.com --name=\"Admin\" --password=\"secret\"');
