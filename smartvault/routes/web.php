<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EncryptionTestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::post('/files', [DashboardController::class, 'store'])->name('files.store');
    Route::post('/images', [DashboardController::class, 'storeImage'])->name('images.store');
    Route::get('/files/{file}/download', [DashboardController::class, 'download'])->name('files.download');
    Route::get('/files/{file}/download-encrypted', [DashboardController::class, 'downloadEncrypted'])->name('files.download.encrypted');
    Route::delete('/files/{file}', [DashboardController::class, 'destroy'])->name('files.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::post('/user/two-factor-authentication', [App\Http\Controllers\TwoFactorAuthenticationController::class, 'store'])
        ->name('two-factor.enable');
    Route::delete('/user/two-factor-authentication', [App\Http\Controllers\TwoFactorAuthenticationController::class, 'destroy'])
        ->name('two-factor.disable');
    Route::get('/user/two-factor-confirmation', [App\Http\Controllers\TwoFactorConfirmationController::class, 'show'])
        ->name('two-factor.confirmation.show');
    Route::get('/user/confirmed-two-factor-authentication', function () {
        return redirect()->route('two-factor.confirmation.show');
    });
    
    Route::get('/encryption-test', [EncryptionTestController::class, 'showTestPage'])->name('encryption.test');
});

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
        
        Route::get('/files/{file}/download', [AdminDashboardController::class, 'downloadFile'])->name('files.download');
        Route::delete('/files/{file}', [AdminDashboardController::class, 'deleteFile'])->name('files.destroy');
    });

require __DIR__.'/auth.php';