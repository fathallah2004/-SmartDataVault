<?php

namespace App\Http\Controllers;

use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EncryptionTestController extends Controller
{
    private $encryptionService;

    public function __construct()
    {
        $this->encryptionService = new EncryptionService();
    }

    /**
     * Afficher la page de test de cryptage
     */
    public function showTestPage()
    {
        $encryptionService = new EncryptionService();
        $algorithms = $encryptionService->getAvailableAlgorithms();
        
        return view('encryption-test', compact('algorithms'));
    }

    /**
     * Tester le chiffrement d'un texte
     */
    public function testEncryption(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string|max:5000',
            'algorithm' => 'required|in:cesar,vigenere,xor-text,substitution,reverse',
            'key' => 'nullable|string|max:100'
        ]);

        try {
            $text = $request->text;
            $algorithm = $request->algorithm;
            $customKey = $request->key;

            // Préparer la clé selon l'algorithme
            $key = $this->prepareKey($customKey, $algorithm);

            // Chiffrer le texte
            $encrypted = $this->encryptionService->encryptText($text, $algorithm);

            // Si une clé personnalisée est fournie, l'utiliser
            if ($key !== null) {
                // Recréer le chiffrement avec la clé personnalisée
                $encrypted = $this->encryptWithCustomKey($text, $algorithm, $key);
            }

            // Déchiffrer pour vérification
            $decrypted = $this->encryptionService->decryptText(
                $encrypted['encrypted_content'],
                $encrypted['key'],
                $algorithm
            );

            // Vérifier l'intégrité
            $integrity = $text === $decrypted;

            return response()->json([
                'success' => true,
                'encrypted_content' => $encrypted['encrypted_content'],
                'decrypted_text' => $decrypted,
                'used_key' => $encrypted['key'],
                'algorithm' => $algorithm,
                'integrity' => $integrity,
                'original_length' => strlen($text),
                'encrypted_length' => strlen(base64_decode($encrypted['encrypted_content']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Tester le déchiffrement d'un texte
     */
    public function testDecryption(Request $request): JsonResponse
    {
        $request->validate([
            'encrypted_content' => 'required|string',
            'algorithm' => 'required|in:cesar,vigenere,xor-text,substitution,reverse',
            'key' => 'required|string|max:100'
        ]);

        try {
            $decrypted = $this->encryptionService->decryptText(
                $request->encrypted_content,
                $request->key,
                $request->algorithm
            );

            return response()->json([
                'success' => true,
                'decrypted_text' => $decrypted,
                'algorithm' => $request->algorithm,
                'key_used' => $request->key
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Obtenir les informations sur un algorithme
     */
    public function getAlgorithmInfo(Request $request): JsonResponse
    {
        $request->validate([
            'algorithm' => 'required|in:cesar,vigenere,xor-text,substitution,reverse'
        ]);

        $algorithm = $request->algorithm;
        $info = $this->getAlgorithmDetails($algorithm);

        return response()->json([
            'success' => true,
            'algorithm' => $algorithm,
            'info' => $info
        ]);
    }

    /**
     * Générer une clé aléatoire pour un algorithme
     */
    public function generateKey(Request $request): JsonResponse
    {
        $request->validate([
            'algorithm' => 'required|in:cesar,vigenere,xor-text,substitution,reverse'
        ]);

        $key = $this->generateKeyForAlgorithm($request->algorithm);

        return response()->json([
            'success' => true,
            'algorithm' => $request->algorithm,
            'generated_key' => $key
        ]);
    }

    /**
     * Préparer la clé selon l'algorithme
     */
    private function prepareKey(?string $customKey, string $algorithm)
    {
        if (empty($customKey)) {
            return null;
        }

        switch ($algorithm) {
            case 'cesar':
                return is_numeric($customKey) ? (int)$customKey : $customKey;
            
            case 'vigenere':
                // Nettoyer la clé Vigenère (lettres seulement)
                return preg_replace('/[^A-Za-z]/', '', strtoupper($customKey));
            
            case 'substitution':
                // Vérifier que c'est une permutation de l'alphabet
                if (strlen($customKey) === 26 && ctype_alpha($customKey)) {
                    return strtolower($customKey);
                }
                return $customKey;
            
            default:
                return $customKey;
        }
    }

    /**
     * Chiffrer avec une clé personnalisée
     */
    private function encryptWithCustomKey(string $text, string $algorithm, $key): array
    {
        // Implémentation manuelle pour chaque algorithme avec clé personnalisée
        switch ($algorithm) {
            case 'cesar':
                return $this->encryptCesarCustom($text, $key);
            
            case 'vigenere':
                return $this->encryptVigenereCustom($text, $key);
            
            case 'xor-text':
                return $this->encryptXorCustom($text, $key);
            
            case 'substitution':
                return $this->encryptSubstitutionCustom($text, $key);
            
            case 'reverse':
                return $this->encryptReverseCustom($text, $key);
            
            default:
                throw new \Exception("Algorithme non supporté");
        }
    }

    /**
     * Chiffrement César avec clé personnalisée
     */
    private function encryptCesarCustom(string $text, $shift): array
    {
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

    /**
     * Chiffrement Vigenère avec clé personnalisée
     */
    private function encryptVigenereCustom(string $text, string $key): array
    {
        $encrypted = '';
        $keyIndex = 0;
        $key = strtoupper($key);
        
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            
            if (ctype_alpha($char)) {
                $ascii = ord($char);
                $isUpper = ctype_upper($char);
                $base = $isUpper ? 65 : 97;
                
                $keyChar = $key[$keyIndex % strlen($key)];
                $keyShift = ord($keyChar) - 65;
                
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

    /**
     * Chiffrement XOR avec clé personnalisée
     */
    private function encryptXorCustom(string $text, string $key): array
    {
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

    /**
     * Chiffrement Substitution avec clé personnalisée
     */
    private function encryptSubstitutionCustom(string $text, string $key): array
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyz';
        $substitution = strtolower($key);
        
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

    /**
     * Chiffrement Reverse avec clé personnalisée
     */
    private function encryptReverseCustom(string $text, $shift): array
    {
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

    /**
     * Générer une clé pour un algorithme
     */
    private function generateKeyForAlgorithm(string $algorithm): string
    {
        switch ($algorithm) {
            case 'cesar':
                return (string)rand(1, 25);
            
            case 'vigenere':
                $length = rand(5, 10);
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $key = '';
                for ($i = 0; $i < $length; $i++) {
                    $key .= $chars[rand(0, strlen($chars) - 1)];
                }
                return $key;
            
            case 'xor-text':
                $length = rand(10, 20);
                $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $key = '';
                for ($i = 0; $i < $length; $i++) {
                    $key .= $chars[rand(0, strlen($chars) - 1)];
                }
                return $key;
            
            case 'substitution':
                $alphabet = 'abcdefghijklmnopqrstuvwxyz';
                return str_shuffle($alphabet);
            
            case 'reverse':
                return (string)rand(1, 10);
            
            default:
                return '';
        }
    }

    /**
     * Obtenir les détails d'un algorithme
     */
    private function getAlgorithmDetails(string $algorithm): array
    {
        $details = [
            'cesar' => [
                'name' => 'Chiffrement César',
                'description' => 'Décalage simple des lettres de l\'alphabet',
                'key_type' => 'Nombre entier (1-25)',
                'key_example' => '3',
                'strength' => 'Faible',
                'usage' => 'Chiffrement basique'
            ],
            'vigenere' => [
                'name' => 'Chiffrement Vigenère',
                'description' => 'Chiffrement polyalphabétique utilisant une clé textuelle',
                'key_type' => 'Chaîne de caractères (lettres seulement)',
                'key_example' => 'SECRET',
                'strength' => 'Moyenne',
                'usage' => 'Chiffrement classique'
            ],
            'xor-text' => [
                'name' => 'XOR Textuel',
                'description' => 'Opération XOR bit à bit avec une clé',
                'key_type' => 'Chaîne de caractères',
                'key_example' => 'MaCleSecrete123',
                'strength' => 'Moyenne',
                'usage' => 'Chiffrement binaire simple'
            ],
            'substitution' => [
                'name' => 'Substitution Simple',
                'description' => 'Remplacement de chaque lettre par une autre selon un alphabet mélangé',
                'key_type' => 'Alphabet mélangé (26 lettres)',
                'key_example' => 'zyxwvutsrqponmlkjihgfedcba',
                'strength' => 'Faible',
                'usage' => 'Chiffrement par substitution'
            ],
            'reverse' => [
                'name' => 'Inversion + Décalage',
                'description' => 'Inversion du texte suivi d\'un décalage César',
                'key_type' => 'Nombre entier (1-10)',
                'key_example' => '5',
                'strength' => 'Très faible',
                'usage' => 'Chiffrement éducatif'
            ]
        ];

        return $details[$algorithm] ?? [];
    }
}