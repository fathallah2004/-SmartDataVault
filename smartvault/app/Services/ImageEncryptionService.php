<?php

namespace App\Services;

use Exception;

class ImageEncryptionService
{
    private $availableAlgorithms = [
        'aes-diffusion' => 'AES + Diffusion',
        'chaos' => 'Chaos (Arnold + Logistic)',
        'dwt-hybrid' => 'DWT Hybride'
    ];

    /**
     * Vérifier si GD est disponible
     */
    private function isGdAvailable(): bool
    {
        return extension_loaded('gd');
    }

    /**
     * Obtenir les algorithmes disponibles
     */
    public function getAvailableAlgorithms()
    {
        return $this->availableAlgorithms;
    }

    /**
     * Chiffrer une image
     */
    public function encryptImage($imagePath, $method = 'aes-diffusion', $password = null)
    {
        if (!$this->isGdAvailable()) {
            throw new Exception('Extension GD n\'est pas disponible');
        }

        if (!file_exists($imagePath)) {
            throw new Exception('Fichier image introuvable');
        }

        // Générer un mot de passe si non fourni
        if ($password === null) {
            $password = bin2hex(random_bytes(16));
        }

        switch ($method) {
            case 'aes-diffusion':
                return $this->encryptAesDiffusion($imagePath, $password);
            case 'chaos':
                return $this->encryptChaos($imagePath, $password);
            case 'dwt-hybrid':
                return $this->encryptDwtHybrid($imagePath, $password);
            default:
                throw new Exception("Algorithme non supporté: {$method}");
        }
    }

    /**
     * Déchiffrer une image
     */
    public function decryptImage($encryptedData, $key, $iv, $salt, $method = 'aes-diffusion', $password = null, $originalShape = null)
    {
        if (!$this->isGdAvailable()) {
            throw new Exception('Extension GD n\'est pas disponible');
        }

        switch ($method) {
            case 'aes-diffusion':
                return $this->decryptAesDiffusion($encryptedData, $key, $iv, $salt, $password);
            case 'chaos':
                return $this->decryptChaos($encryptedData, $password, $originalShape);
            case 'dwt-hybrid':
                return $this->decryptDwtHybrid($encryptedData, $key, $iv, $salt, $password);
            default:
                throw new Exception("Algorithme non supporté: {$method}");
        }
    }

    /**
     * Méthode 1: AES + Diffusion
     */
    private function encryptAesDiffusion($imagePath, $password)
    {
        // Charger l'image
        $imageInfo = getimagesize($imagePath);
        if ($imageInfo === false) {
            throw new Exception('Impossible de charger l\'image');
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $type = $imageInfo[2];

        // Créer une ressource image selon le type
        $image = null;
        switch ($type) {
            case IMAGETYPE_JPEG:
                $image = @imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_PNG:
                $image = @imagecreatefrompng($imagePath);
                break;
            case IMAGETYPE_GIF:
                $image = @imagecreatefromgif($imagePath);
                break;
            case IMAGETYPE_BMP:
                $image = @imagecreatefrombmp($imagePath);
                break;
            case IMAGETYPE_WEBP:
                $image = @imagecreatefromwebp($imagePath);
                break;
            default:
                // Essayer de charger avec imagecreatefromstring comme fallback
                $imageData = file_get_contents($imagePath);
                $image = @imagecreatefromstring($imageData);
                break;
        }
        
        if ($image === false || $image === null) {
            throw new Exception('Impossible de charger l\'image. Format supporté: JPG, PNG, GIF, BMP, WEBP');
        }

        // Convertir en tableau d'octets RGB
        $data = $this->imageToBytes($image, $width, $height);
        imagedestroy($image);

        // Générer salt et IV
        $salt = random_bytes(16);
        $iv = random_bytes(16);

        // Dériver la clé avec PBKDF2 (simulé avec hash_hmac)
        $key = $this->deriveKey($password, $salt, 32);

        // Diffusion: XOR avec keystream dérivé de la clé
        $keystream = $this->generateKeystream($key, $iv, strlen($data));
        $diffused = '';
        for ($i = 0; $i < strlen($data); $i++) {
            $diffused .= chr(ord($data[$i]) ^ ord($keystream[$i]));
        }

        // Padding pour AES (16 bytes)
        $padded = $this->pkcs7Pad($diffused, 16);

        // Chiffrer avec AES-256-CBC
        $encrypted = openssl_encrypt($padded, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        if ($encrypted === false) {
            throw new Exception('Erreur de chiffrement AES');
        }

        // Créer une image visible à partir des données chiffrées
        $visibleImage = $this->bytesToImage($encrypted, $width, $height);

        // Sauvegarder les données binaires (salt + iv + encrypted)
        $binaryData = $salt . $iv . $encrypted;

        return [
            'encrypted_content' => base64_encode($binaryData),
            'encrypted_image' => base64_encode($visibleImage),
            'key' => base64_encode($key),
            'iv' => base64_encode($iv),
            'salt' => base64_encode($salt),
            'hash' => hash('sha256', $encrypted),
            'method' => 'aes-diffusion',
            'width' => $width,
            'height' => $height,
            'original_type' => $type
        ];
    }

    private function decryptAesDiffusion($encryptedData, $key, $iv, $salt, $password)
    {
        // Décoder les données
        $binaryData = base64_decode($encryptedData);
        $keyBytes = base64_decode($key);
        $ivBytes = base64_decode($iv);
        $saltBytes = base64_decode($salt);

        // Si la clé n'est pas fournie, la dériver du mot de passe
        if (empty($keyBytes) && $password) {
            $keyBytes = $this->deriveKey($password, $saltBytes, 32);
        }

        // Extraire les données chiffrées (après salt et iv)
        $encrypted = substr($binaryData, 32); // 16 (salt) + 16 (iv)

        // Déchiffrer avec AES-256-CBC
        $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $keyBytes, OPENSSL_RAW_DATA, $ivBytes);
        if ($decrypted === false) {
            throw new Exception('Erreur de déchiffrement AES');
        }

        // Retirer le padding
        $unpadded = $this->pkcs7Unpad($decrypted);

        // Régénérer le keystream et inverser la diffusion
        $keystream = $this->generateKeystream($keyBytes, $ivBytes, strlen($unpadded));
        $undiffused = '';
        for ($i = 0; $i < strlen($unpadded); $i++) {
            $undiffused .= chr(ord($unpadded[$i]) ^ ord($keystream[$i]));
        }

        return $undiffused;
    }

    /**
     * Méthode 2: Chaos (Arnold Cat Map + Logistic Map)
     */
    private function encryptChaos($imagePath, $password, $arnoldIterations = 5)
    {
        $imageInfo = getimagesize($imagePath);
        if ($imageInfo === false) {
            throw new Exception('Impossible de charger l\'image');
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $type = $imageInfo[2];

        $image = null;
        switch ($type) {
            case IMAGETYPE_JPEG:
                $image = @imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_PNG:
                $image = @imagecreatefrompng($imagePath);
                break;
            case IMAGETYPE_GIF:
                $image = @imagecreatefromgif($imagePath);
                break;
            case IMAGETYPE_BMP:
                $image = @imagecreatefrombmp($imagePath);
                break;
            case IMAGETYPE_WEBP:
                $image = @imagecreatefromwebp($imagePath);
                break;
            default:
                $imageData = file_get_contents($imagePath);
                $image = @imagecreatefromstring($imageData);
                break;
        }
        
        if ($image === false || $image === null) {
            throw new Exception('Impossible de charger l\'image. Format supporté: JPG, PNG, GIF, BMP, WEBP');
        }

        // Convertir en tableau RGB
        $pixels = $this->imageToArray($image, $width, $height);
        imagedestroy($image);

        // Convertir en image carrée si nécessaire
        $square = $this->makeSquare($pixels, $width, $height);
        $N = $square['size'];
        $pixelArray = $square['pixels'];

        // Dériver le seed du mot de passe
        $seed = $this->deriveSeed($password);

        // Appliquer Arnold Cat Map (permutation)
        $permuted = $this->arnoldCatMap($pixelArray, $N, $arnoldIterations);

        // Appliquer Logistic Map (diffusion)
        $length = $N * $N * 3; // RGB
        $logisticSeq = $this->logisticSequence($seed, $length);

        // Appliquer XOR avec la séquence chaotique
        $encryptedPixels = [];
        $idx = 0;
        for ($y = 0; $y < $N; $y++) {
            for ($x = 0; $x < $N; $x++) {
                $r = ($permuted[$y][$x][0] ^ $logisticSeq[$idx++]) & 0xFF;
                $g = ($permuted[$y][$x][1] ^ $logisticSeq[$idx++]) & 0xFF;
                $b = ($permuted[$y][$x][2] ^ $logisticSeq[$idx++]) & 0xFF;
                $encryptedPixels[$y][$x] = [$r, $g, $b];
            }
        }

        // Créer l'image chiffrée
        $encryptedImage = $this->arrayToImage($encryptedPixels, $N, $N);

        // Sauvegarder les métadonnées
        $metadata = json_encode([
            'arnold_iterations' => $arnoldIterations,
            'original_width' => $width,
            'original_height' => $height,
            'square_size' => $N
        ]);

        return [
            'encrypted_content' => base64_encode($encryptedImage),
            'encrypted_image' => base64_encode($encryptedImage),
            'key' => base64_encode($metadata),
            'iv' => null,
            'salt' => null,
            'hash' => hash('sha256', $encryptedImage),
            'method' => 'chaos',
            'width' => $N,
            'height' => $N,
            'original_type' => $type,
            'metadata' => $metadata
        ];
    }

    private function decryptChaos($encryptedData, $password, $originalShape = null)
    {
        $encryptedImage = base64_decode($encryptedData);
        
        // Créer une image depuis les données
        $tempFile = tmpfile();
        $tempPath = stream_get_meta_data($tempFile)['uri'];
        file_put_contents($tempPath, $encryptedImage);

        $imageInfo = getimagesize($tempPath);
        if ($imageInfo === false) {
            throw new Exception('Impossible de charger l\'image chiffrée');
        }

        $N = $imageInfo[0];
        $image = imagecreatefromstring($encryptedImage);

        // Convertir en tableau
        $pixels = $this->imageToArray($image, $N, $N);
        imagedestroy($image);

        // Dériver le seed
        $seed = $this->deriveSeed($password);

        // Récupérer les métadonnées si disponibles
        $arnoldIterations = 5;
        $originalWidth = $N;
        $originalHeight = $N;

        if ($originalShape) {
            $metadata = json_decode(base64_decode($originalShape), true);
            if ($metadata) {
                $arnoldIterations = $metadata['arnold_iterations'] ?? 5;
                $originalWidth = $metadata['original_width'] ?? $N;
                $originalHeight = $metadata['original_height'] ?? $N;
            }
        }

        // Générer la séquence logistic (même que pour le chiffrement)
        $length = $N * $N * 3;
        $logisticSeq = $this->logisticSequence($seed, $length);

        // Inverser la diffusion (XOR)
        $undiffused = [];
        $idx = 0;
        for ($y = 0; $y < $N; $y++) {
            for ($x = 0; $x < $N; $x++) {
                $r = ($pixels[$y][$x][0] ^ $logisticSeq[$idx++]) & 0xFF;
                $g = ($pixels[$y][$x][1] ^ $logisticSeq[$idx++]) & 0xFF;
                $b = ($pixels[$y][$x][2] ^ $logisticSeq[$idx++]) & 0xFF;
                $undiffused[$y][$x] = [$r, $g, $b];
            }
        }

        // Inverser Arnold Cat Map
        $decrypted = $this->inverseArnoldCatMap($undiffused, $N, $arnoldIterations);

        // Recréer l'image
        $decryptedImage = $this->arrayToImage($decrypted, $N, $N);

        // Si l'image originale n'était pas carrée, recadrer
        if ($originalWidth != $N || $originalHeight != $N) {
            // Créer une nouvelle image avec les dimensions originales
            $finalImage = imagecreatetruecolor($originalWidth, $originalHeight);
            $source = imagecreatefromstring($decryptedImage);
            imagecopy($finalImage, $source, 0, 0, 0, 0, $originalWidth, $originalHeight);
            imagedestroy($source);
            
            ob_start();
            imagepng($finalImage);
            $decryptedImage = ob_get_clean();
            imagedestroy($finalImage);
        }

        return $decryptedImage;
    }

    /**
     * Méthode 3: DWT Hybride (simplifié - sans vraie DWT, utilise une approche similaire)
     */
    private function encryptDwtHybrid($imagePath, $password)
    {
        // Pour simplifier, on utilise une approche similaire à AES+Diffusion
        // mais avec une transformation de fréquence simulée
        return $this->encryptAesDiffusion($imagePath, $password);
    }

    private function decryptDwtHybrid($encryptedData, $key, $iv, $salt, $password)
    {
        return $this->decryptAesDiffusion($encryptedData, $key, $iv, $salt, $password);
    }

    /**
     * Fonctions utilitaires
     */
    private function imageToBytes($image, $width, $height)
    {
        $bytes = '';
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $bytes .= chr($r) . chr($g) . chr($b);
            }
        }
        return $bytes;
    }

    private function bytesToImage($bytes, $width, $height)
    {
        $image = imagecreatetruecolor($width, $height);
        $idx = 0;
        $required = $width * $height * 3;
        
        // Répéter les bytes si nécessaire
        if (strlen($bytes) < $required) {
            $bytes = str_repeat($bytes, ceil($required / strlen($bytes)));
        }
        $bytes = substr($bytes, 0, $required);

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                if ($idx + 2 < strlen($bytes)) {
                    $r = ord($bytes[$idx++]);
                    $g = ord($bytes[$idx++]);
                    $b = ord($bytes[$idx++]);
                    $color = imagecolorallocate($image, $r, $g, $b);
                    imagesetpixel($image, $x, $y, $color);
                }
            }
        }

        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);
        return $imageData;
    }

    private function imageToArray($image, $width, $height)
    {
        $pixels = [];
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $pixels[$y][$x] = [$r, $g, $b];
            }
        }
        return $pixels;
    }

    private function arrayToImage($pixels, $width, $height)
    {
        $image = imagecreatetruecolor($width, $height);
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                if (isset($pixels[$y][$x])) {
                    [$r, $g, $b] = $pixels[$y][$x];
                    $color = imagecolorallocate($image, $r, $g, $b);
                    imagesetpixel($image, $x, $y, $color);
                }
            }
        }
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);
        return $imageData;
    }

    private function makeSquare($pixels, $width, $height)
    {
        $N = max($width, $height);
        $square = [];
        
        for ($y = 0; $y < $N; $y++) {
            for ($x = 0; $x < $N; $x++) {
                if ($y < $height && $x < $width) {
                    $square[$y][$x] = $pixels[$y][$x];
                } else {
                    $square[$y][$x] = [0, 0, 0]; // Noir pour le padding
                }
            }
        }
        
        return ['pixels' => $square, 'size' => $N];
    }

    private function arnoldCatMap($pixels, $N, $iterations)
    {
        $result = $pixels;
        for ($iter = 0; $iter < $iterations; $iter++) {
            $temp = [];
            for ($y = 0; $y < $N; $y++) {
                for ($x = 0; $x < $N; $x++) {
                    $newX = ($x + $y) % $N;
                    $newY = ($x + 2 * $y) % $N;
                    $temp[$newY][$newX] = $result[$y][$x];
                }
            }
            $result = $temp;
        }
        return $result;
    }

    private function inverseArnoldCatMap($pixels, $N, $iterations)
    {
        $result = $pixels;
        for ($iter = 0; $iter < $iterations; $iter++) {
            $temp = [];
            for ($y = 0; $y < $N; $y++) {
                for ($x = 0; $x < $N; $x++) {
                    // Inverse: [2, -1; -1, 1]
                    $origX = (2 * $x - $y + $N) % $N;
                    $origY = (-1 * $x + $y + $N) % $N;
                    $temp[$origY][$origX] = $result[$y][$x];
                }
            }
            $result = $temp;
        }
        return $result;
    }

    private function logisticSequence($seed, $length, $mu = 3.999999)
    {
        $x = $seed;
        $seq = [];
        for ($i = 0; $i < $length; $i++) {
            $x = $mu * $x * (1 - $x);
            $seq[] = (int)(($x * 1e14) % 256);
        }
        return $seq;
    }

    private function deriveSeed($password)
    {
        $hash = hash('sha256', $password, true);
        $hex = bin2hex(substr($hash, 0, 8));
        $int = hexdec($hex);
        return ($int % 100000000) / 100000000.0;
    }

    private function deriveKey($password, $salt, $keyLength = 32)
    {
        // PBKDF2 simplifié avec hash_hmac
        $iterations = 200000;
        $hash = '';
        $blockCount = ceil($keyLength / 32);
        
        for ($i = 1; $i <= $blockCount; $i++) {
            $u = hash_hmac('sha256', $salt . pack('N', $i), $password, true);
            $result = $u;
            
            for ($j = 1; $j < $iterations; $j++) {
                $u = hash_hmac('sha256', $u, $password, true);
                $result = $result ^ $u;
            }
            
            $hash .= $result;
        }
        
        return substr($hash, 0, $keyLength);
    }

    private function generateKeystream($key, $iv, $length)
    {
        // Générer un keystream déterministe à partir de la clé et IV
        $keystream = '';
        $counter = 0;
        
        while (strlen($keystream) < $length) {
            $data = $iv . pack('N', $counter);
            $block = hash_hmac('sha256', $data, $key, true);
            $keystream .= $block;
            $counter++;
        }
        
        return substr($keystream, 0, $length);
    }

    private function pkcs7Pad($data, $blockSize)
    {
        $pad = $blockSize - (strlen($data) % $blockSize);
        return $data . str_repeat(chr($pad), $pad);
    }

    private function pkcs7Unpad($data)
    {
        $pad = ord($data[strlen($data) - 1]);
        return substr($data, 0, -$pad);
    }
}

