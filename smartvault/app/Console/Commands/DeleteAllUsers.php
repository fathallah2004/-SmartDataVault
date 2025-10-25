<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DeleteAllUsers extends Command
{
    protected $signature = 'users:delete-all {--keep-current}';
    protected $description = 'Supprimer tous les utilisateurs';

    public function handle()
    {
        $keepCurrent = $this->option('keep-current');
        
        if ($keepCurrent) {
            $currentUserId = auth()->id();
            $deletedCount = User::where('id', '!=', $currentUserId)->delete();
            $this->info("{$deletedCount} utilisateurs supprimés (sauf l'utilisateur actuel).");
        } else {
            $count = User::count();
            User::truncate();
            $this->info("Tous les {$count} utilisateurs ont été supprimés.");
        }

        return Command::SUCCESS;
    }
}