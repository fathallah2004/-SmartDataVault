<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EncryptionTestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Remplacer la route dashboard existante
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Routes pour la gestion des fichiers chiffrés
Route::middleware(['auth'])->group(function () {
    Route::post('/files', [DashboardController::class, 'store'])->name('files.store');
    Route::post('/images', [DashboardController::class, 'storeImage'])->name('images.store');
    Route::get('/files/{file}/download', [DashboardController::class, 'download'])->name('files.download');
    Route::get('/files/{file}/download-encrypted', [DashboardController::class, 'downloadEncrypted'])->name('files.download.encrypted');
    Route::delete('/files/{file}', [DashboardController::class, 'destroy'])->name('files.destroy');
});

// Routes du profil et test de cryptage
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Routes pour l'authentification à deux facteurs
    Route::post('/user/two-factor-authentication', [App\Http\Controllers\TwoFactorAuthenticationController::class, 'store'])
        ->name('two-factor.enable');
    Route::delete('/user/two-factor-authentication', [App\Http\Controllers\TwoFactorAuthenticationController::class, 'destroy'])
        ->name('two-factor.disable');
    // Route GET pour afficher la page de confirmation (Fortify n'a que POST)
    Route::get('/user/two-factor-confirmation', [App\Http\Controllers\TwoFactorConfirmationController::class, 'show'])
        ->name('two-factor.confirmation.show');
    // Route GET pour rediriger vers notre page de confirmation (pour compatibilité avec Fortify)
    Route::get('/user/confirmed-two-factor-authentication', function () {
        return redirect()->route('two-factor.confirmation.show');
    });
    
    // Route pour la page de test de cryptage
    Route::get('/encryption-test', [EncryptionTestController::class, 'showTestPage'])->name('encryption.test');
});

// Routes API pour le test de cryptage
Route::middleware('auth')->group(function () {
    Route::post('/api/test-encryption', [EncryptionTestController::class, 'testEncryption']);
    Route::post('/api/test-decryption', [EncryptionTestController::class, 'testDecryption']);
    Route::get('/api/algorithm-info', [EncryptionTestController::class, 'getAlgorithmInfo']);
    Route::get('/api/generate-key', [EncryptionTestController::class, 'generateKey']);
});

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', [AdminDashboardController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminDashboardController::class, 'show'])->name('users.show');
        Route::delete('/users/{user}', [AdminDashboardController::class, 'destroy'])->name('users.destroy');
        
        // Routes pour la gestion des fichiers par les admins
        Route::get('/files/{file}/download', [AdminDashboardController::class, 'downloadFile'])->name('files.download');
        Route::delete('/files/{file}', [AdminDashboardController::class, 'deleteFile'])->name('files.destroy');
    });

require __DIR__.'/auth.php';