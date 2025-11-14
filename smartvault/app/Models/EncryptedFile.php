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

    /**
     * Chiffre automatiquement la clé avant de la sauvegarder dans la base de données
     */
    public function setEncryptionKeyAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['encryption_key'] = null;
            return;
        }
        
        // Vérifier si la valeur est déjà chiffrée (commence par "eyJ" = base64 de JSON)
        // Les valeurs chiffrées par Laravel Crypt ont une structure spécifique
        if ($this->isAlreadyEncrypted($value)) {
            $this->attributes['encryption_key'] = $value;
        } else {
            // Chiffrer la clé avec Laravel Crypt (utilise APP_KEY)
            $this->attributes['encryption_key'] = Crypt::encryptString($value);
        }
    }

    /**
     * Déchiffre automatiquement la clé lors de la récupération depuis la base de données
     */
    public function getEncryptionKeyAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }
        
        // Essayer de déchiffrer (si c'est une clé chiffrée avec Laravel Crypt)
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // Si le déchiffrement échoue, c'est probablement une ancienne clé en base64
            // Retourner la valeur telle quelle pour la compatibilité avec les anciennes données
            return $value;
        }
    }

    /**
     * Chiffre automatiquement l'IV avant de la sauvegarder dans la base de données
     */
    public function setIvAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['iv'] = null;
            return;
        }
        
        if ($this->isAlreadyEncrypted($value)) {
            $this->attributes['iv'] = $value;
        } else {
            $this->attributes['iv'] = Crypt::encryptString($value);
        }
    }

    /**
     * Déchiffre automatiquement l'IV lors de la récupération depuis la base de données
     */
    public function getIvAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }
        
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // Compatibilité avec les anciennes données
            return $value;
        }
    }

    /**
     * Vérifie si une chaîne est déjà chiffrée avec Laravel Crypt
     * Les valeurs chiffrées par Laravel commencent par "eyJ" (base64 de {"iv":...})
     */
    private function isAlreadyEncrypted($value): bool
    {
        if (empty($value) || strlen($value) < 20) {
            return false;
        }
        
        // Les valeurs chiffrées par Laravel Crypt commencent par "eyJ" (base64 de JSON)
        return str_starts_with($value, 'eyJ');
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
                default => 'AES-CTR Image'
            };
        }
        return ['cesar' => 'César', 'vigenere' => 'Vigenère', 'xor-text' => 'XOR Textuel', 'xor-image' => 'XOR Image', 'substitution' => 'Substitution', 'reverse' => 'Inversion'][$this->encryption_method] ?? $this->encryption_method;
    }

    public function isEncrypted()
    {
        return !empty($this->encrypted_content) && !empty($this->encryption_method) && !empty($this->encryption_key);
    }
}