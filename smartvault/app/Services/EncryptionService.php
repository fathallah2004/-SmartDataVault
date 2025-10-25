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
     * Chiffrement César Classique
     */
    private function encryptCesar($text)
    {
        $shift = rand(1, 25);
        $encrypted = '';
        
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            
            if (ctype_alpha($char)) {
                $ascii = ord($char);
                $isUpper = ctype_upper($char);
                $base = $isUpper ? 65 : 97;
                $encrypted .= chr(($ascii - $base + $shift) % 26 + $base);
            } else {
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
        $text = base64_decode($encryptedContent);
        $shift = (int)$key;
        $decrypted = '';
        
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            
            if (ctype_alpha($char)) {
                $ascii = ord($char);
                $isUpper = ctype_upper($char);
                $base = $isUpper ? 65 : 97;
                $decrypted .= chr(($ascii - $base - $shift + 26) % 26 + $base);
            } else {
                $decrypted .= $char;
            }
        }
        
        return $decrypted;
    }

    /**
     * Chiffrement Vigenère
     */
    private function encryptVigenere($text)
    {
        $key = $this->generateRandomKey(8);
        $encrypted = '';
        $keyIndex = 0;
        
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            
            if (ctype_alpha($char)) {
                $ascii = ord($char);
                $isUpper = ctype_upper($char);
                $base = $isUpper ? 65 : 97;
                
                $keyChar = $key[$keyIndex % strlen($key)];
                $keyShift = ord(ctype_upper($keyChar) ? $keyChar : strtoupper($keyChar)) - 65;
                
                $encrypted .= chr(($ascii - $base + $keyShift) % 26 + $base);
                $keyIndex++;
            } else {
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
        $text = base64_decode($encryptedContent);
        $decrypted = '';
        $keyIndex = 0;
        
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            
            if (ctype_alpha($char)) {
                $ascii = ord($char);
                $isUpper = ctype_upper($char);
                $base = $isUpper ? 65 : 97;
                
                $keyChar = $key[$keyIndex % strlen($key)];
                $keyShift = ord(ctype_upper($keyChar) ? $keyChar : strtoupper($keyChar)) - 65;
                
                $decrypted .= chr(($ascii - $base - $keyShift + 26) % 26 + $base);
                $keyIndex++;
            } else {
                $decrypted .= $char;
            }
        }
        
        return $decrypted;
    }

    /**
     * XOR pour texte
     */
    private function encryptXORText($text)
    {
        $key = $this->generateRandomKey(12);
        $encrypted = '';
        
        for ($i = 0; $i < strlen($text); $i++) {
            $encrypted .= $text[$i] ^ $key[$i % strlen($key)];
        }
        
        return [
            'encrypted_content' => base64_encode($encrypted),
            'key' => $key,
            'iv' => null,
            'hash' => hash('sha256', $encrypted),
            'method' => 'xor-text'
        ];
    }

    private function decryptXORText($encryptedContent, $key)
    {
        $text = base64_decode($encryptedContent);
        $decrypted = '';
        
        for ($i = 0; $i < strlen($text); $i++) {
            $decrypted .= $text[$i] ^ $key[$i % strlen($key)];
        }
        
        return $decrypted;
    }

    /**
     * Substitution alphabétique
     */
    private function encryptSubstitution($text)
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyz';
        $substitution = str_shuffle($alphabet);
        
        $encrypted = '';
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            $lowerChar = strtolower($char);
            
            if (ctype_alpha($char)) {
                $pos = strpos($alphabet, $lowerChar);
                if ($pos !== false) {
                    $newChar = $substitution[$pos];
                    $encrypted .= ctype_upper($char) ? strtoupper($newChar) : $newChar;
                } else {
                    $encrypted .= $char;
                }
            } else {
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
        $text = base64_decode($encryptedContent);
        $alphabet = 'abcdefghijklmnopqrstuvwxyz';
        $substitution = $key;
        
        $decrypted = '';
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            $lowerChar = strtolower($char);
            
            if (ctype_alpha($char)) {
                $pos = strpos($substitution, $lowerChar);
                if ($pos !== false) {
                    $newChar = $alphabet[$pos];
                    $decrypted .= ctype_upper($char) ? strtoupper($newChar) : $newChar;
                } else {
                    $decrypted .= $char;
                }
            } else {
                $decrypted .= $char;
            }
        }
        
        return $decrypted;
    }

    /**
     * Inversion + décalage
     */
    private function encryptReverse($text)
    {
        $shift = rand(1, 10);
        $reversed = strrev($text);
        $encrypted = '';
        
        for ($i = 0; $i < strlen($reversed); $i++) {
            $char = $reversed[$i];
            
            if (ctype_alpha($char)) {
                $ascii = ord($char);
                $isUpper = ctype_upper($char);
                $base = $isUpper ? 65 : 97;
                $encrypted .= chr(($ascii - $base + $shift) % 26 + $base);
            } else {
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
        $text = base64_decode($encryptedContent);
        $shift = (int)$key;
        $decrypted = '';
        
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            
            if (ctype_alpha($char)) {
                $ascii = ord($char);
                $isUpper = ctype_upper($char);
                $base = $isUpper ? 65 : 97;
                $decrypted .= chr(($ascii - $base - $shift + 26) % 26 + $base);
            } else {
                $decrypted .= $char;
            }
        }
        
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
        return array_key_exists($method, $this->availableAlgorithms);
    }
}