<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class EncryptedFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'original_name',
        'file_size', 
        'file_type',
        'file_category',
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

    public function setEncryptionKeyAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['encryption_key'] = null;
            return;
        }
        
        $this->attributes['encryption_key'] = $this->isAlreadyEncrypted($value) 
            ? $value 
            : Crypt::encryptString($value);
        }

    public function getEncryptionKeyAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }
        
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function setIvAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['iv'] = null;
            return;
        }
        
        $this->attributes['iv'] = $this->isAlreadyEncrypted($value) 
            ? $value 
            : Crypt::encryptString($value);
        }

    public function getIvAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }
        
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    private function isAlreadyEncrypted($value): bool
    {
        return !empty($value) && strlen($value) >= 20 && str_starts_with($value, 'eyJ');
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
        if ($this->file_category === 'image') {
            return '🖼️';
        }

        return match(strtolower($this->file_type)) {
            'txt' => '📄', 'doc', 'docx' => '📝', 'rtf' => '📋', 'md' => '📑', 'pdf' => '📕', default => '📁'
        };
    }

    public function getAlgorithmNameAttribute()
    {
        if ($this->file_category === 'image') {
            return match($this->encryption_method) {
                'aes-ctr-image' => 'AES-CTR Image',
                'aes-image' => 'AES-CBC Image',
                'xor-image' => 'XOR Image',
                default => 'AES-CTR Image'
            };
        }
        
        return [
            'cesar' => 'César',
            'vigenere' => 'Vigenère',
            'xor-text' => 'XOR Textuel',
            'substitution' => 'Substitution',
            'reverse' => 'Inversion'
        ][$this->encryption_method] ?? $this->encryption_method;
    }

    public function isEncrypted()
    {
        return !empty($this->encrypted_content) && !empty($this->encryption_method) && !empty($this->encryption_key);
    }
}