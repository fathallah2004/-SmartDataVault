<?php

use App\Http\Controllers\EncryptionTestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::post('/test-encryption', [EncryptionTestController::class, 'testEncryption']);
    Route::post('/test-decryption', [EncryptionTestController::class, 'testDecryption']);
    Route::post('/algorithm-info', [EncryptionTestController::class, 'getAlgorithmInfo']);
    Route::post('/generate-key', [EncryptionTestController::class, 'generateKey']);
});