<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable 
{
    use HasFactory, Notifiable, SoftDeletes, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'total_files_encrypted',
        'total_storage_used',
        'last_upload_at',
        'last_login_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_upload_at' => 'datetime',
            'last_login_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function encryptedFiles()
    {
        return $this->hasMany(EncryptedFile::class);
    }

    public function getFormattedStorageAttribute()
    {
        $bytes = $this->total_storage_used;
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' bytes';
    }

    public function updateStatsAfterUpload($fileSize)
    {
        $this->increment('total_files_encrypted');
        $this->increment('total_storage_used', $fileSize);
        $this->last_upload_at = now();
        $this->save();
    }

    public function updateStatsAfterDelete($fileSize)
    {
        $this->decrement('total_files_encrypted');
        $this->decrement('total_storage_used', $fileSize);
        $this->save();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}