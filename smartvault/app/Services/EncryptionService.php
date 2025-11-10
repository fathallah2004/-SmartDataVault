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
        if (!$this->isTextContent($content)) {
            throw new \Exception("Le fichier contient des données binaires. Seuls les fichiers texte sont supportés.");
        }
        return match($method) {
            'cesar' => $this->encryptCesar($content),
            'vigenere' => $this->encryptVigenere($content),
            'xor-text' => $this->encryptXORText($content),
            'substitution' => $this->encryptSubstitution($content),
            'reverse' => $this->encryptReverse($content),
            default => $this->encryptCesar($content)
        };
    }

    public function decryptText($encryptedContent, $key, $method = 'cesar')
    {
        return match($method) {
            'cesar' => $this->decryptCesar($encryptedContent, $key),
            'vigenere' => $this->decryptVigenere($encryptedContent, $key),
            'xor-text' => $this->decryptXORText($encryptedContent, $key),
            'substitution' => $this->decryptSubstitution($encryptedContent, $key),
            'reverse' => $this->decryptReverse($encryptedContent, $key),
            default => $this->decryptCesar($encryptedContent, $key)
        };
    }

    private function isTextContent($content)
    {
        return !preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $content) && strlen($content) <= 1000000;
    }

    private function buildEncryptionResult($encrypted, $key, $method)
    {
        return ['encrypted_content' => base64_encode($encrypted), 'key' => $key, 'iv' => null, 'hash' => hash('sha256', $encrypted), 'method' => $method];
    }

    private function encryptCesar($text)
    {
        $shift = rand(1, 25);
        $encrypted = '';
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            if (ctype_alpha($char)) {
                $base = ctype_upper($char) ? 65 : 97;
                $encrypted .= chr((ord($char) - $base + $shift) % 26 + $base);
            } else {
                $encrypted .= $char;
            }
        }
        return $this->buildEncryptionResult($encrypted, (string)$shift, 'cesar');
    }

    private function decryptCesar($encryptedContent, $key)
    {
        if (($text = base64_decode($encryptedContent, true)) === false) {
            throw new \Exception('Erreur de décodage base64');
        }
        $shift = (int)$key;
        $decrypted = '';
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            if (ctype_alpha($char)) {
                $base = ctype_upper($char) ? 65 : 97;
                $decrypted .= chr((ord($char) - $base - $shift + 26) % 26 + $base);
            } else {
                $decrypted .= $char;
            }
        }
        return $decrypted;
    }

    private function encryptVigenere($text)
    {
        $key = strtoupper($this->generateVigenereKey(8));
        $encrypted = '';
        $keyIndex = 0;
        $keyLen = strlen($key);
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            if (ctype_alpha($char)) {
                $base = ctype_upper($char) ? 65 : 97;
                $encrypted .= chr((ord($char) - $base + ord($key[$keyIndex++ % $keyLen]) - 65) % 26 + $base);
            } else {
                $encrypted .= $char;
            }
        }
        return $this->buildEncryptionResult($encrypted, $key, 'vigenere');
    }

    private function decryptVigenere($encryptedContent, $key)
    {
        if (($text = base64_decode($encryptedContent, true)) === false) {
            throw new \Exception('Erreur de décodage base64');
        }
        $normalizedKey = '';
        for ($i = 0; $i < strlen($key); $i++) {
            $char = $key[$i];
            $normalizedKey .= ctype_alpha($char) ? strtoupper($char) : (ctype_digit($char) ? chr(65 + (int)$char) : '');
        }
        if (empty($normalizedKey)) {
            throw new \Exception('Clé Vigenère invalide');
        }
        $key = $normalizedKey;
        $decrypted = '';
        $keyIndex = 0;
        $keyLen = strlen($key);
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            if (ctype_alpha($char)) {
                $base = ctype_upper($char) ? 65 : 97;
                $decrypted .= chr((ord($char) - $base - (ord($key[$keyIndex++ % $keyLen]) - 65) + 26) % 26 + $base);
            } else {
                $decrypted .= $char;
            }
        }
        return $decrypted;
    }

    private function encryptXORText($text)
    {
        $key = $this->generateRandomKey(12);
        $textBytes = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        $keyBytes = mb_convert_encoding($key, 'UTF-8', 'UTF-8');
        $encrypted = '';
        $keyLen = strlen($keyBytes);
        for ($i = 0; $i < strlen($textBytes); $i++) {
            $encrypted .= chr((ord($textBytes[$i]) ^ ord($keyBytes[$i % $keyLen])) & 0xFF);
        }
        return $this->buildEncryptionResult($encrypted, base64_encode($keyBytes), 'xor-text');
    }

    private function decryptXORText($encryptedContent, $key)
    {
        if (($encrypted = base64_decode($encryptedContent, true)) === false) {
            throw new \Exception('Erreur de décodage base64');
        }
        $keyBytes = base64_decode($key, true) ?: $key;
        if (!mb_check_encoding($keyBytes, 'UTF-8')) {
            $keyBytes = mb_convert_encoding($keyBytes, 'UTF-8', 'auto');
        }
        if (($keyLen = strlen($keyBytes)) === 0) {
            throw new \Exception('Clé invalide');
        }
        $decrypted = '';
        for ($i = 0; $i < strlen($encrypted); $i++) {
            $decrypted .= chr((ord($encrypted[$i]) ^ ord($keyBytes[$i % $keyLen])) & 0xFF);
        }
        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', mb_convert_encoding($decrypted, 'UTF-8', 'UTF-8'));
    }

    private function encryptSubstitution($text)
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyz';
        $substitution = str_shuffle($alphabet);
        $encrypted = '';
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            if (ctype_alpha($char) && ($pos = strpos($alphabet, strtolower($char))) !== false) {
                $newChar = $substitution[$pos];
                $encrypted .= ctype_upper($char) ? strtoupper($newChar) : $newChar;
            } else {
                $encrypted .= $char;
            }
        }
        return $this->buildEncryptionResult($encrypted, $substitution, 'substitution');
    }

    private function decryptSubstitution($encryptedContent, $key)
    {
        if (($text = base64_decode($encryptedContent, true)) === false) {
            throw new \Exception('Erreur de décodage base64');
        }
        $alphabet = 'abcdefghijklmnopqrstuvwxyz';
        $decrypted = '';
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            if (ctype_alpha($char) && ($pos = strpos($key, strtolower($char))) !== false) {
                $newChar = $alphabet[$pos];
                $decrypted .= ctype_upper($char) ? strtoupper($newChar) : $newChar;
            } else {
                $decrypted .= $char;
            }
        }
        return $decrypted;
    }

    private function encryptReverse($text)
    {
        $shift = rand(1, 10);
        $reversed = strrev($text);
        $encrypted = '';
        for ($i = 0; $i < strlen($reversed); $i++) {
            $char = $reversed[$i];
            if (ctype_alpha($char)) {
                $base = ctype_upper($char) ? 65 : 97;
                $encrypted .= chr((ord($char) - $base + $shift) % 26 + $base);
            } else {
                $encrypted .= $char;
            }
        }
        return $this->buildEncryptionResult($encrypted, (string)$shift, 'reverse');
    }

    private function decryptReverse($encryptedContent, $key)
    {
        if (($text = base64_decode($encryptedContent, true)) === false) {
            throw new \Exception('Erreur de décodage base64');
        }
        $shift = (int)$key;
        $decrypted = '';
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            if (ctype_alpha($char)) {
                $base = ctype_upper($char) ? 65 : 97;
                $decrypted .= chr((ord($char) - $base - $shift + 26) % 26 + $base);
            } else {
                $decrypted .= $char;
            }
        }
        return strrev($decrypted);
    }

    private function generateRandomKey($length = 10, $lettersOnly = false)
    {
        $chars = $lettersOnly ? 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' : 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $key = '';
        for ($i = 0; $i < $length; $i++) {
            $key .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $key;
    }

    private function generateVigenereKey($length = 8)
    {
        return $this->generateRandomKey($length, true);
    }

    public function getAvailableAlgorithms()
    {
        return $this->availableAlgorithms;
    }
}