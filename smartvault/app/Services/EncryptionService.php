<?php

namespace App\Services;

class EncryptionService
{
    private $availableAlgorithms = [
        'cesar' => 'Chiffrement César',
        'vigenere' => 'Chiffrement Vigenère', 
        'xor-text' => 'XOR Textuel',
        'substitution' => 'Substitution Simple',
        'reverse' => 'Inversion + Décalage'
    ];

    private $imageAlgorithms = [
        'aes-diffusion' => 'AES + Diffusion',
        'chaos' => 'Chaos (Arnold + Logistic)',
        'dwt-hybrid' => 'DWT Hybride'
    ];

    public function encryptText($content, $method = 'cesar')
    {
        if (!array_key_exists($method, $this->availableAlgorithms)) {
            throw new \Exception("Algorithme non supporté: {$method}");
        }

        // Vérifier que c'est bien du texte
        if (!$this->isTextContent($content)) {
            throw new \Exception("Le fichier contient des données binaires. Seuls les fichiers texte sont supportés.");
        }

        switch ($method) {
            case 'cesar':
                return $this->encryptCesar($content);
            case 'vigenere':
                return $this->encryptVigenere($content);
            case 'xor-text':
                return $this->encryptXORText($content);
            case 'substitution':
                return $this->encryptSubstitution($content);
            case 'reverse':
                return $this->encryptReverse($content);
            default:
                return $this->encryptCesar($content);
        }
    }

    public function decryptText($encryptedContent, $key, $method = 'cesar')
    {
        switch ($method) {
            case 'cesar':
                return $this->decryptCesar($encryptedContent, $key);
            case 'vigenere':
                return $this->decryptVigenere($encryptedContent, $key);
            case 'xor-text':
                return $this->decryptXORText($encryptedContent, $key);
            case 'substitution':
                return $this->decryptSubstitution($encryptedContent, $key);
            case 'reverse':
                return $this->decryptReverse($encryptedContent, $key);
            default:
                return $this->decryptCesar($encryptedContent, $key);
        }
    }

    /**
     * Vérifier si le contenu est du texte pur
     */
    private function isTextContent($content)
    {
        // Vérifier les caractères non-textuels (binaires)
        if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $content)) {
            return false;
        }
        
        // Vérifier la longueur maximale pour les fichiers texte
        if (strlen($content) > 1000000) {
            return false;
        }
        
        return true;
    }

    /**
     * Chiffrement César Classique (amélioré pour UTF-8)
     */
    private function encryptCesar($text)
    {
        $shift = rand(1, 25);
        $encrypted = '';
        
        // Utiliser strlen pour être cohérent avec le déchiffrement
        // Les caractères UTF-8 multi-octets seront préservés tels quels
        $length = strlen($text);
        
        for ($i = 0; $i < $length; $i++) {
            $char = $text[$i];
            $ascii = ord($char);
            
            // Vérifier si c'est une lettre latine (a-z, A-Z)
            if (ctype_alpha($char)) {
                $isUpper = ctype_upper($char);
                $base = $isUpper ? 65 : 97;
                $encrypted .= chr(($ascii - $base + $shift) % 26 + $base);
            } else {
                // Conserver les caractères spéciaux, accents, etc.
                $encrypted .= $char;
            }
        }
        
        return [
            'encrypted_content' => base64_encode($encrypted),
            'key' => (string)$shift,
            'iv' => null,
            'hash' => hash('sha256', $encrypted),
            'method' => 'cesar'
        ];
    }

    private function decryptCesar($encryptedContent, $key)
    {
        $text = base64_decode($encryptedContent, true);
        if ($text === false) {
            throw new \Exception('Erreur de décodage base64');
        }
        
        $shift = (int)$key;
        $decrypted = '';
        
        // Utiliser strlen car le texte décodé est binaire
        $length = strlen($text);
        
        for ($i = 0; $i < $length; $i++) {
            $char = $text[$i];
            $ascii = ord($char);
            
            // Vérifier si c'est une lettre latine (a-z, A-Z)
            if (ctype_alpha($char)) {
                $isUpper = ctype_upper($char);
                $base = $isUpper ? 65 : 97;
                $decrypted .= chr(($ascii - $base - $shift + 26) % 26 + $base);
            } else {
                // Conserver les caractères spéciaux, accents, etc.
                $decrypted .= $char;
            }
        }
        
        return $decrypted;
    }

    /**
     * Chiffrement Vigenère (amélioré pour UTF-8)
     */
    private function encryptVigenere($text)
    {
        // Générer une clé avec seulement des lettres pour Vigenère
        $key = $this->generateVigenereKey(8);
        $encrypted = '';
        $keyIndex = 0;
        
        // Normaliser la clé (convertir en majuscules)
        $key = strtoupper($key);
        $keyLen = strlen($key);
        
        // Utiliser strlen pour être cohérent avec le déchiffrement
        // Les caractères UTF-8 multi-octets seront préservés tels quels
        $length = strlen($text);
        
        for ($i = 0; $i < $length; $i++) {
            $char = $text[$i];
            $ascii = ord($char);
            
            // Vérifier si c'est une lettre latine (a-z, A-Z)
            if (ctype_alpha($char)) {
                $isUpper = ctype_upper($char);
                $base = $isUpper ? 65 : 97;
                
                $keyChar = $key[$keyIndex % $keyLen];
                $keyShift = ord($keyChar) - 65;
                
                $encrypted .= chr(($ascii - $base + $keyShift) % 26 + $base);
                $keyIndex++;
            } else {
                // Conserver les caractères spéciaux, accents, etc.
                $encrypted .= $char;
            }
        }
        
        return [
            'encrypted_content' => base64_encode($encrypted),
            'key' => $key,
            'iv' => null,
            'hash' => hash('sha256', $encrypted),
            'method' => 'vigenere'
        ];
    }

    private function decryptVigenere($encryptedContent, $key)
    {
        $text = base64_decode($encryptedContent, true);
        if ($text === false) {
            throw new \Exception('Erreur de décodage base64');
        }
        
        $decrypted = '';
        $keyIndex = 0;
        
        // Utiliser strlen car le texte décodé est binaire
        $length = strlen($text);
        $keyLen = strlen($key);
        
        // Normaliser la clé pour Vigenère
        // Si la clé contient des chiffres, les convertir en lettres (0=A, 1=B, etc.)
        // Sinon, utiliser la clé telle quelle
        $normalizedKey = '';
        for ($i = 0; $i < strlen($key); $i++) {
            $char = $key[$i];
            if (ctype_alpha($char)) {
                $normalizedKey .= strtoupper($char);
            } elseif (ctype_digit($char)) {
                // Convertir les chiffres en lettres (0=A, 1=B, ..., 9=J)
                $normalizedKey .= chr(65 + (int)$char);
            }
        }
        
        if (empty($normalizedKey)) {
            throw new \Exception('Clé Vigenère invalide - doit contenir au moins une lettre ou un chiffre');
        }
        
        $key = $normalizedKey;
        $keyLen = strlen($key);
        
        for ($i = 0; $i < $length; $i++) {
            $char = $text[$i];
            $ascii = ord($char);
            
            // Vérifier si c'est une lettre latine (a-z, A-Z)
            if (ctype_alpha($char)) {
                $isUpper = ctype_upper($char);
                $base = $isUpper ? 65 : 97;
                
                $keyChar = $key[$keyIndex % $keyLen];
                $keyShift = ord($keyChar) - 65;
                
                $decrypted .= chr(($ascii - $base - $keyShift + 26) % 26 + $base);
                $keyIndex++;
            } else {
                // Conserver les caractères spéciaux, accents, etc.
                $decrypted .= $char;
            }
        }
        
        return $decrypted;
    }

    /**
     * XOR pour texte (amélioré pour éviter les caractères invalides)
     */
    private function encryptXORText($text)
    {
        $key = $this->generateRandomKey(12);
        $encrypted = '';
        
        // Utiliser mb_* pour gérer UTF-8 correctement
        $textBytes = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        $keyBytes = mb_convert_encoding($key, 'UTF-8', 'UTF-8');
        $textLen = strlen($textBytes);
        $keyLen = strlen($keyBytes);
        
        for ($i = 0; $i < $textLen; $i++) {
            $byte = ord($textBytes[$i]) ^ ord($keyBytes[$i % $keyLen]);
            // S'assurer que le résultat est un octet valide (0-255)
            $encrypted .= chr($byte & 0xFF);
        }
        
        return [
            'encrypted_content' => base64_encode($encrypted),
            'key' => base64_encode($keyBytes), // Encoder la clé aussi pour éviter les problèmes
            'iv' => null,
            'hash' => hash('sha256', $encrypted),
            'method' => 'xor-text'
        ];
    }

    private function decryptXORText($encryptedContent, $key)
    {
        $encrypted = base64_decode($encryptedContent, true);
        if ($encrypted === false) {
            throw new \Exception('Erreur de décodage base64 du contenu');
        }
        
        // Décoder la clé si elle est en base64 (nouveau format)
        $keyBytes = base64_decode($key, true);
        if ($keyBytes === false || empty($keyBytes)) {
            // Si ce n'est pas du base64, utiliser directement (ancien format)
            $keyBytes = $key;
        }
        
        // S'assurer que la clé est en UTF-8
        if (!mb_check_encoding($keyBytes, 'UTF-8')) {
            $keyBytes = mb_convert_encoding($keyBytes, 'UTF-8', 'auto');
        }
        
        $decrypted = '';
        $encryptedLen = strlen($encrypted);
        $keyLen = strlen($keyBytes);
        
        if ($keyLen === 0) {
            throw new \Exception('Clé de déchiffrement invalide');
        }
        
        for ($i = 0; $i < $encryptedLen; $i++) {
            $byte = ord($encrypted[$i]) ^ ord($keyBytes[$i % $keyLen]);
            // S'assurer que le résultat est un octet valide
            $decrypted .= chr($byte & 0xFF);
        }
        
        // Convertir de retour en UTF-8 et nettoyer
        $decrypted = mb_convert_encoding($decrypted, 'UTF-8', 'UTF-8');
        
        // Nettoyer les caractères de contrôle invalides (préserver \n=0x0A, \r=0x0D, \t=0x09)
        $decrypted = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $decrypted);
        
        return $decrypted;
    }

    /**
     * Substitution alphabétique (amélioré pour UTF-8)
     */
    private function encryptSubstitution($text)
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyz';
        $substitution = str_shuffle($alphabet);
        
        $encrypted = '';
        $length = strlen($text);
        
        for ($i = 0; $i < $length; $i++) {
            $char = $text[$i];
            
            // Vérifier si c'est une lettre latine (a-z, A-Z)
            if (ctype_alpha($char)) {
                $lowerChar = strtolower($char);
                $pos = strpos($alphabet, $lowerChar);
                if ($pos !== false) {
                    $newChar = $substitution[$pos];
                    $encrypted .= ctype_upper($char) ? strtoupper($newChar) : $newChar;
                } else {
                    $encrypted .= $char;
                }
            } else {
                // Conserver les caractères spéciaux, accents, etc.
                $encrypted .= $char;
            }
        }
        
        return [
            'encrypted_content' => base64_encode($encrypted),
            'key' => $substitution,
            'iv' => null,
            'hash' => hash('sha256', $encrypted),
            'method' => 'substitution'
        ];
    }

    private function decryptSubstitution($encryptedContent, $key)
    {
        $text = base64_decode($encryptedContent, true);
        if ($text === false) {
            throw new \Exception('Erreur de décodage base64');
        }
        
        $alphabet = 'abcdefghijklmnopqrstuvwxyz';
        $substitution = $key;
        
        $decrypted = '';
        $length = strlen($text);
        
        for ($i = 0; $i < $length; $i++) {
            $char = $text[$i];
            
            // Vérifier si c'est une lettre latine (a-z, A-Z)
            if (ctype_alpha($char)) {
                $lowerChar = strtolower($char);
                $pos = strpos($substitution, $lowerChar);
                if ($pos !== false) {
                    $newChar = $alphabet[$pos];
                    $decrypted .= ctype_upper($char) ? strtoupper($newChar) : $newChar;
                } else {
                    $decrypted .= $char;
                }
            } else {
                // Conserver les caractères spéciaux, accents, etc.
                $decrypted .= $char;
            }
        }
        
        return $decrypted;
    }

    /**
     * Inversion + décalage (amélioré pour UTF-8)
     */
    private function encryptReverse($text)
    {
        $shift = rand(1, 10);
        
        // Inverser la chaîne
        $reversed = strrev($text);
        
        $encrypted = '';
        $length = strlen($reversed);
        
        for ($i = 0; $i < $length; $i++) {
            $char = $reversed[$i];
            $ascii = ord($char);
            
            // Vérifier si c'est une lettre latine (a-z, A-Z)
            if (ctype_alpha($char)) {
                $isUpper = ctype_upper($char);
                $base = $isUpper ? 65 : 97;
                $encrypted .= chr(($ascii - $base + $shift) % 26 + $base);
            } else {
                // Conserver les caractères spéciaux, accents, etc.
                $encrypted .= $char;
            }
        }
        
        return [
            'encrypted_content' => base64_encode($encrypted),
            'key' => (string)$shift,
            'iv' => null,
            'hash' => hash('sha256', $encrypted),
            'method' => 'reverse'
        ];
    }

    private function decryptReverse($encryptedContent, $key)
    {
        $text = base64_decode($encryptedContent, true);
        if ($text === false) {
            throw new \Exception('Erreur de décodage base64');
        }
        
        $shift = (int)$key;
        $decrypted = '';
        $length = strlen($text);
        
        for ($i = 0; $i < $length; $i++) {
            $char = $text[$i];
            $ascii = ord($char);
            
            // Vérifier si c'est une lettre latine (a-z, A-Z)
            if (ctype_alpha($char)) {
                $isUpper = ctype_upper($char);
                $base = $isUpper ? 65 : 97;
                $decrypted .= chr(($ascii - $base - $shift + 26) % 26 + $base);
            } else {
                // Conserver les caractères spéciaux, accents, etc.
                $decrypted .= $char;
            }
        }
        
        // Inverser la chaîne
        return strrev($decrypted);
    }

    /**
     * Générer une clé aléatoire
     */
    private function generateRandomKey($length = 10)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $key = '';
        for ($i = 0; $i < $length; $i++) {
            $key .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $key;
    }

    /**
     * Générer une clé aléatoire pour Vigenère (lettres seulement)
     */
    private function generateVigenereKey($length = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $key = '';
        for ($i = 0; $i < $length; $i++) {
            $key .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $key;
    }

    /**
     * Obtenir les algorithmes disponibles
     */
    public function getAvailableAlgorithms()
    {
        return $this->availableAlgorithms;
    }

    /**
     * Vérifier si un algorithme est disponible
     */
    public function isAlgorithmAvailable($method)
    {
        return array_key_exists($method, $this->availableAlgorithms) || 
               array_key_exists($method, $this->imageAlgorithms);
    }

    /**
     * Obtenir les algorithmes d'images disponibles
     */
    public function getImageAlgorithms()
    {
        return $this->imageAlgorithms;
    }

    /**
     * Vérifier si un algorithme est pour les images
     */
    public function isImageAlgorithm($method)
    {
        return array_key_exists($method, $this->imageAlgorithms);
    }
}