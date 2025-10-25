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

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accès à la clé de déchiffrement (stockée en clair pour les algorithmes texte)
     */
    public function getDecryptionKey()
    {
        return $this->encryption_key;
    }

    /**
     * Vérifier l'intégrité du fichier
     */
    public function verifyIntegrity()
    {
        $currentHash = hash('sha256', $this->encrypted_content);
        return hash_equals($this->file_hash, $currentHash);
    }

    /**
     * Formater la taille du fichier
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        
        if ($bytes == 0) {
            return '0 bytes';
        }
        
        $units = ['bytes', 'KB', 'MB', 'GB'];
        $precision = 2;
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Obtenir l'icône selon le type de fichier texte
     */
    public function getFileIconAttribute()
    {
        return match(strtolower($this->file_type)) {
            'txt' => '📄',
            'doc' => '📝',
            'docx' => '📝',
            'rtf' => '📋',
            'md' => '📑',
            'pdf' => '📕',
            default => '📁'
        };
    }

    /**
     * Vérifier si le fichier est un fichier texte valide
     */
    public function getIsTextFileAttribute()
    {
        $textExtensions = ['txt', 'doc', 'docx', 'rtf', 'md', 'pdf'];
        return in_array(strtolower($this->file_type), $textExtensions);
    }

    /**
     * Scope pour les fichiers d'un utilisateur
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope pour les fichiers récents
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Accesseur pour le nom d'affichage
     */
    public function getDisplayNameAttribute()
    {
        return $this->original_name ?: 'Fichier sans nom';
    }

    /**
     * Accesseur pour la date formatée
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    /**
     * Vérifier si le fichier est récent (moins de 7 jours)
     */
    public function getIsRecentAttribute()
    {
        return $this->created_at->gt(now()->subDays(7));
    }

    /**
     * Obtenir le nom de l'algorithme formaté
     */
    public function getAlgorithmNameAttribute()
    {
        $algorithms = [
            'cesar' => 'César',
            'vigenere' => 'Vigenère',
            'xor-text' => 'XOR Textuel',
            'substitution' => 'Substitution',
            'reverse' => 'Inversion'
        ];

        return $algorithms[$this->encryption_method] ?? $this->encryption_method;
    }
}