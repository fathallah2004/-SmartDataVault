<?php

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
    Route::get('/files/{file}/download', [DashboardController::class, 'download'])->name('files.download');
    Route::delete('/files/{file}', [DashboardController::class, 'destroy'])->name('files.destroy');
});

// Routes du profil et test de cryptage
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
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

require __DIR__.'/auth.php';