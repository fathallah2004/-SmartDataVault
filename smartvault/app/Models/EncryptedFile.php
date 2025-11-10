<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncryptedFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_name',
        'file_size', 
        'file_type',
        'encrypted_content',
        'encryption_method',
        'encryption_key',
        'iv',
        'file_hash',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDecryptionKey()
    {
        return $this->encryption_key;
    }

    public function getFormattedSizeAttribute()
    {
        if (($bytes = $this->file_size) == 0) return '0 bytes';
        $pow = min(floor(log($bytes) / log(1024)), 3);
        return round($bytes / pow(1024, $pow), 2) . ' ' . ['bytes', 'KB', 'MB', 'GB'][$pow];
    }

    public function getFileIconAttribute()
    {
        return match(strtolower($this->file_type)) {
            'txt' => '📄', 'doc', 'docx' => '📝', 'rtf' => '📋', 'md' => '📑', 'pdf' => '📕', default => '📁'
        };
    }

    public function getAlgorithmNameAttribute()
    {
        return ['cesar' => 'César', 'vigenere' => 'Vigenère', 'xor-text' => 'XOR Textuel', 'substitution' => 'Substitution', 'reverse' => 'Inversion'][$this->encryption_method] ?? $this->encryption_method;
    }

    public function isEncrypted()
    {
        return !empty($this->encrypted_content) && !empty($this->encryption_method) && !empty($this->encryption_key);
    }
}