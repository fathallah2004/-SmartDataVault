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

    /**
     * Chiffre une image avec AES-256-CTR - Algorithme simple qui préserve l'image exactement
     * Chiffre les bytes bruts et les convertit en image valide pour l'affichage
     */
    public function encryptImage($imageData)
    {
        if (!extension_loaded('gd')) {
            throw new \Exception('Extension GD requise pour le chiffrement d\'images');
        }

        // Augmenter temporairement la limite de mémoire pour les grandes images
        $originalMemoryLimit = ini_get('memory_limit');
        ini_set('memory_limit', '512M');

        // Détecter le type d'image
        $imageType = $this->detectImageType($imageData);
        
        // Générer une clé AES de 32 bytes (256 bits)
        $key = random_bytes(32);
        
        // Générer un nonce unique de 8 bytes
        $nonce = random_bytes(8);
        
        // Chiffrer directement les bytes bruts de l'image avec AES-CTR
        $encryptedBytes = $this->aesCtrEncrypt($imageData, $key, $nonce);
        
        // Convertir les bytes chiffrés en image valide pour l'affichage
        // Calculer les dimensions nécessaires pour stocker tous les bytes
        $totalBytes = strlen($encryptedBytes);
        // Chaque pixel peut stocker 3 bytes (RGB), donc on a besoin de totalBytes/3 pixels
        $pixelsNeeded = (int)ceil($totalBytes / 3);
        // Calculer les dimensions (carré approximatif)
        $width = (int)ceil(sqrt($pixelsNeeded));
        $height = (int)ceil($pixelsNeeded / $width);
        
        // Créer une image pour stocker les bytes chiffrés
        $encImg = imagecreatetruecolor($width, $height);
        
        // Remplir l'image avec les bytes chiffrés
        $byteIndex = 0;
        $bytesLen = strlen($encryptedBytes);
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                if ($byteIndex < $bytesLen) {
                    $r = ord($encryptedBytes[$byteIndex++]);
                    $g = ($byteIndex < $bytesLen) ? ord($encryptedBytes[$byteIndex++]) : 0;
                    $b = ($byteIndex < $bytesLen) ? ord($encryptedBytes[$byteIndex++]) : 0;
                    $color = imagecolorallocate($encImg, $r, $g, $b);
                } else {
                    // Remplir avec du noir pour les pixels restants
                    $color = imagecolorallocate($encImg, 0, 0, 0);
                }
                imagesetpixel($encImg, $x, $y, $color);
            }
        }
        
        // Sauvegarder l'image chiffrée en PNG (format lossless)
        ob_start();
        imagepng($encImg, null, 0, PNG_NO_FILTER);
        $encryptedImage = ob_get_clean();
        imagedestroy($encImg);
        
        // Préparer les métadonnées (nonce + taille de l'image originale + type d'image + dimensions)
        // Format: nonce (8 bytes) + imageSize (4 bytes big-endian) + imageType (1 byte) + width (4 bytes) + height (4 bytes)
        $imageSize = strlen($imageData);
        $imageTypeCode = match($imageType) {
            'png' => 1,
            'jpeg' => 2,
            'gif' => 3,
            'webp' => 4,
            'bmp' => 5,
            'svg' => 6,
            default => 2 // JPEG par défaut
        };
        $metadata = $nonce . 
                   pack('N', $imageSize) . 
                   chr($imageTypeCode) .
                   pack('N', $width) .
                   pack('N', $height);
        
        // Restaurer la limite de mémoire originale
        ini_set('memory_limit', $originalMemoryLimit);
        
        // Stocker la clé et les métadonnées
        return [
            'encrypted_content' => base64_encode($encryptedImage),
            'key' => base64_encode($key),
            'iv' => base64_encode($metadata), // Stocker les métadonnées dans le champ iv
            'hash' => hash('sha256', $encryptedImage),
            'method' => 'aes-ctr-image'
        ];
    }

    /**
     * Déchiffre une image avec AES-256-CTR - Algorithme simple qui préserve l'image exactement
     * Extrait les bytes chiffrés de l'image et les déchiffre pour obtenir l'image originale
     */
    public function decryptImage($encryptedContent, $key, $iv = null)
    {
        if (!extension_loaded('gd')) {
            throw new \Exception('Extension GD requise pour le déchiffrement d\'images');
        }

        // Augmenter temporairement la limite de mémoire pour les grandes images
        $originalMemoryLimit = ini_get('memory_limit');
        ini_set('memory_limit', '512M');

        if (($encrypted = base64_decode($encryptedContent, true)) === false) {
            throw new \Exception('Erreur de décodage base64');
        }
        
        $keyBytes = base64_decode($key, true);
        if ($keyBytes === false || strlen($keyBytes) !== 32) {
            throw new \Exception('Clé invalide (doit être 32 bytes)');
        }
        
        // Lire les métadonnées
        if ($iv === null) {
            throw new \Exception('Métadonnées (nonce, taille) requises pour le déchiffrement');
        }
        
        $metadata = base64_decode($iv, true);
        if ($metadata === false || strlen($metadata) < 21) {
            throw new \Exception('Métadonnées invalides');
        }
        
        // Extraire nonce, taille, type d'image et dimensions
        $nonce = substr($metadata, 0, 8);
        $imageSize = unpack('N', substr($metadata, 8, 4))[1];
        $imageTypeCode = ord($metadata[12]);
        $width = unpack('N', substr($metadata, 13, 4))[1];
        $height = unpack('N', substr($metadata, 17, 4))[1];
        
        // Convertir le code en type d'image
        $originalImageType = match($imageTypeCode) {
            1 => 'png',
            2 => 'jpeg',
            3 => 'gif',
            4 => 'webp',
            5 => 'bmp',
            6 => 'svg',
            default => 'jpeg'
        };
        
        // Charger l'image chiffrée et extraire les bytes
        $encImg = @imagecreatefromstring($encrypted);
        if ($encImg === false) {
            throw new \Exception('Impossible de charger l\'image chiffrée');
        }
        
        // Vérifier les dimensions
        $encWidth = imagesx($encImg);
        $encHeight = imagesy($encImg);
        
        if ($encWidth != $width || $encHeight != $height) {
            imagedestroy($encImg);
            throw new \Exception("Dimensions de l'image chiffrée ne correspondent pas");
        }
        
        // Extraire les bytes chiffrés depuis les pixels
        $encryptedBytes = '';
        $bytesExtracted = 0;
        for ($y = 0; $y < $height && $bytesExtracted < $imageSize; $y++) {
            for ($x = 0; $x < $width && $bytesExtracted < $imageSize; $x++) {
                $rgb = imagecolorat($encImg, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                
                // Ajouter les bytes un par un jusqu'à atteindre la taille originale
                if ($bytesExtracted < $imageSize) {
                    $encryptedBytes .= chr($r);
                    $bytesExtracted++;
                }
                if ($bytesExtracted < $imageSize) {
                    $encryptedBytes .= chr($g);
                    $bytesExtracted++;
                }
                if ($bytesExtracted < $imageSize) {
                    $encryptedBytes .= chr($b);
                    $bytesExtracted++;
                }
            }
        }
        imagedestroy($encImg);
        
        // Déchiffrer les bytes avec AES-CTR
        $decryptedBytes = $this->aesCtrDecrypt($encryptedBytes, $keyBytes, $nonce);
        
        // Vérifier que la taille correspond
        if (strlen($decryptedBytes) !== $imageSize) {
            throw new \Exception("Taille de l'image déchiffrée ne correspond pas (attendu: {$imageSize} bytes, obtenu: " . strlen($decryptedBytes) . " bytes)");
        }
        
        // Restaurer la limite de mémoire originale
        ini_set('memory_limit', $originalMemoryLimit);
        
        // Retourner directement les bytes déchiffrés (image originale exacte)
        return $decryptedBytes;
    }

    /**
     * Implémente le chiffrement AES-CTR (Counter Mode)
     * CTR mode: encrypt(counter) XOR plaintext
     */
    private function aesCtrEncrypt($plaintext, $key, $nonce)
    {
        $blockSize = 16; // AES block size
        $encrypted = '';
        $counter = 0;
        $noncePadded = $nonce . str_repeat("\x00", 8); // 16 bytes total (nonce 8 + counter 8)
        
        for ($i = 0; $i < strlen($plaintext); $i += $blockSize) {
            // Construire le compteur: nonce (8 bytes) + counter (8 bytes, big-endian)
            $counterBytes = pack('J', $counter); // J = unsigned long long (64-bit, machine byte order)
            // Pour garantir big-endian, on utilise pack('N', high) . pack('N', low)
            $high = ($counter >> 32) & 0xFFFFFFFF;
            $low = $counter & 0xFFFFFFFF;
            $counterBytes = pack('N', $high) . pack('N', $low);
            $ctr = $nonce . $counterBytes;
            
            // Chiffrer le compteur avec AES-256-ECB
            $encryptedCounter = openssl_encrypt(
                $ctr,
                'AES-256-ECB',
                $key,
                OPENSSL_RAW_DATA
            );
            
            // XOR avec le bloc de texte clair
            $block = substr($plaintext, $i, $blockSize);
            $blockLen = strlen($block);
            for ($j = 0; $j < $blockLen; $j++) {
                $encrypted .= chr(ord($block[$j]) ^ ord($encryptedCounter[$j]));
            }
            
            $counter++;
        }
        
        return $encrypted;
    }

    /**
     * Implémente le déchiffrement AES-CTR (identique au chiffrement)
     */
    private function aesCtrDecrypt($ciphertext, $key, $nonce)
    {
        // CTR mode: déchiffrement = chiffrement (XOR est symétrique)
        return $this->aesCtrEncrypt($ciphertext, $key, $nonce);
    }


    /**
     * Détecte le type d'image à partir des premiers bytes
     */
    private function detectImageType($imageData)
    {
        if (strlen($imageData) < 4) {
            return 'unknown';
        }
        
        $signature = substr($imageData, 0, 4);
        
        // JPEG: FF D8 FF
        if (substr($imageData, 0, 3) === "\xFF\xD8\xFF") {
            return 'jpeg';
        }
        
        // PNG: 89 50 4E 47
        if ($signature === "\x89\x50\x4E\x47") {
            return 'png';
        }
        
        // GIF: 47 49 46 38
        if (substr($imageData, 0, 6) === "GIF89a" || substr($imageData, 0, 6) === "GIF87a") {
            return 'gif';
        }
        
        // WEBP: RIFF...WEBP
        if (substr($imageData, 0, 4) === "RIFF" && strpos($imageData, "WEBP", 8) !== false) {
            return 'webp';
        }
        
        // BMP: BM
        if (substr($imageData, 0, 2) === "BM") {
            return 'bmp';
        }
        
        return 'unknown';
    }

    /**
     * Obtient la longueur de l'en-tête de l'image (structure complète préservée)
     */
    private function getImageHeaderLength($imageData, $imageType)
    {
        switch ($imageType) {
            case 'jpeg':
                // Pour JPEG, préserver tous les segments jusqu'aux données de scan (FF DA)
                // On doit aussi préserver les marqueurs de fin de segment (FF D9)
                $startOfScan = strpos($imageData, "\xFF\xDA");
                if ($startOfScan !== false) {
                    // Préserver jusqu'au début des données de scan + 2 bytes pour le marqueur
                    // Mais ne pas chiffrer les marqueurs FF dans les données
                    return $startOfScan + 2;
                }
                // Si pas trouvé, préserver au moins 1000 bytes (probablement tout l'en-tête)
                return min(1000, strlen($imageData));
                
            case 'png':
                // PNG: préserver la signature (8) + tous les chunks jusqu'aux données IDAT
                // Les chunks PNG ont une structure: length (4) + type (4) + data + CRC (4)
                $pos = strpos($imageData, "IDAT");
                if ($pos !== false) {
                    // Préserver jusqu'au début du chunk IDAT + 8 bytes (length + type)
                    return $pos + 8;
                }
                // Si pas trouvé, préserver au moins 100 bytes
                return min(100, strlen($imageData));
                
            case 'gif':
                // GIF: préserver signature (6) + Logical Screen Descriptor (7) + Global Color Table (si présente)
                // Pour simplifier, préserver les 100 premiers bytes
                return min(100, strlen($imageData));
                
            case 'webp':
                // WEBP: préserver le header RIFF (12) + le header VP8/VP8L
                // Pour simplifier, préserver les 100 premiers bytes
                return min(100, strlen($imageData));
                
            case 'bmp':
                // BMP: header fixe de 54 bytes
                return 54;
                
            default:
                // Pour les formats inconnus, préserver les 200 premiers bytes (plus sûr)
                return min(200, strlen($imageData));
        }
    }

    /**
     * Chiffrement simple pour les formats non reconnus
     */
    private function encryptImageSimple($imageData)
    {
        $key = $this->generateRandomKey(32);
        $keyBytes = $key;
        
        // Préserver les 100 premiers bytes (probablement l'en-tête)
        $headerLength = min(100, strlen($imageData));
        $header = substr($imageData, 0, $headerLength);
        $data = substr($imageData, $headerLength);
        
        $encrypted = '';
        $keyLen = strlen($keyBytes);
        $dataLen = strlen($data);
        
        for ($i = 0; $i < $dataLen; $i++) {
            $encrypted .= chr((ord($data[$i]) ^ ord($keyBytes[$i % $keyLen])) & 0xFF);
        }
        
        return $this->buildEncryptionResult($header . $encrypted, base64_encode($keyBytes), 'xor-image');
    }

    /**
     * Déchiffrement simple pour les formats non reconnus
     */
    private function decryptImageSimple($encrypted, $keyBytes)
    {
        // Préserver les 100 premiers bytes
        $headerLength = min(100, strlen($encrypted));
        $header = substr($encrypted, 0, $headerLength);
        $encryptedData = substr($encrypted, $headerLength);
        
        $decrypted = '';
        $keyLen = strlen($keyBytes);
        $dataLen = strlen($encryptedData);
        
        for ($i = 0; $i < $dataLen; $i++) {
            $decrypted .= chr((ord($encryptedData[$i]) ^ ord($keyBytes[$i % $keyLen])) & 0xFF);
        }
        
        return $header . $decrypted;
    }
}