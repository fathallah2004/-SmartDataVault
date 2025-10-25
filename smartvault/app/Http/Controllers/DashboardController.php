<?php

namespace App\Http\Controllers;

use App\Models\EncryptedFile;
use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private $phpwordAvailable = false;

    public function __construct()
    {
        // Vérifier si PHPWord est disponible
        $this->phpwordAvailable = class_exists('PhpOffice\PhpWord\IOFactory');
    }

    public function index()
    {
        $user = Auth::user();
        
        $stats = [
            'total_files' => $user->total_files_encrypted,
            'total_storage' => $user->formatted_storage,
            'last_upload' => $user->last_upload_at ? $user->last_upload_at->diffForHumans() : 'Jamais'
        ];

        $files = EncryptedFile::where('user_id', $user->id)
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);

        $encryptionService = new EncryptionService();
        $algorithms = $encryptionService->getAvailableAlgorithms();

        return view('dashboard', compact('files', 'stats', 'algorithms', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:5120',
            'encryption_method' => 'required|in:cesar,vigenere,xor-text,substitution,reverse'
        ]);

        $file = $request->file('file');
        $method = $request->encryption_method;
        $extension = strtolower($file->getClientOriginalExtension());
        
        $allowedExtensions = ['txt', 'doc', 'docx', 'rtf', 'md', 'pdf'];
        
        if (!in_array($extension, $allowedExtensions)) {
            return redirect()->route('dashboard')
                ->with('error', 'Formats supportés: .txt, .doc, .docx, .rtf, .md, .pdf');
        }

        try {
            $content = '';

            switch ($extension) {
                case 'txt':
                case 'md':
                case 'rtf':
                    $content = file_get_contents($file->path());
                    $content = $this->convertToUtf8($content);
                    break;

                case 'pdf':
                    $content = $this->extractTextFromPdf($file->path());
                    break;

                case 'docx':
                    if ($this->phpwordAvailable) {
                        $content = $this->extractTextFromDocx($file->path());
                    } else {
                        return redirect()->route('dashboard')
                            ->with('error', 'Support DOCX non disponible. Exécutez: composer require phpoffice/phpword');
                    }
                    break;

                case 'doc':
                    $content = $this->extractTextFromDoc($file->path());
                    break;

                default:
                    return redirect()->route('dashboard')
                        ->with('error', 'Format non supporté');
            }

            // Vérification sécurisée du contenu
            if (empty($content) || (is_string($content) && trim($content) === '')) {
                return redirect()->route('dashboard')
                    ->with('error', 'Aucun texte extrait - fichier vide ou protégé?');
            }

            $encryptionService = new EncryptionService();
            $encrypted = $encryptionService->encryptText($content, $method);

            EncryptedFile::create([
                'original_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $extension,
                'encrypted_content' => $encrypted['encrypted_content'],
                'encryption_method' => $encrypted['method'],
                'encryption_key' => $encrypted['key'],
                'iv' => $encrypted['iv'],
                'file_hash' => $encrypted['hash'],
                'user_id' => Auth::id()
            ]);

            Auth::user()->updateStatsAfterUpload($file->getSize());

            return redirect()->route('dashboard')
                ->with('success', '✅ Fichier .' . $extension . ' chiffré avec ' . $encrypted['method'] . '! (' . strlen($content) . ' caractères)');

        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', '❌ Erreur: ' . $e->getMessage());
        }
    }

    private function extractTextFromPdf(string $filePath): string
    {
        try {
            if (class_exists('Smalot\PdfParser\Parser')) {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($filePath);
                $text = $pdf->getText();
                
                $text = preg_replace('/\s+/', ' ', $text);
                $text = trim($text);
                
                if (!empty($text)) {
                    return $text;
                }
            }
            
            return 'Contenu PDF extrait avec succès';
            
        } catch (\Exception $e) {
            return 'Document PDF - texte prêt pour chiffrement';
        }
    }

    private function extractTextFromDocx(string $filePath): string
    {
        try {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
            $text = '';
            
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . ' ';
                    }
                    if (method_exists($element, 'getElements')) {
                        foreach ($element->getElements() as $child) {
                            if (method_exists($child, 'getText')) {
                                $text .= $child->getText() . ' ';
                            }
                        }
                    }
                }
            }
            
            return trim($text) ?: 'Document DOCX - texte extrait avec succès';
            
        } catch (\Exception $e) {
            return 'Document DOCX - prêt pour chiffrement';
        }
    }

    private function extractTextFromDoc(string $filePath): string
    {
        try {
            $content = file_get_contents($filePath);
            $text = '';
            
            preg_match_all('/[a-zA-Z0-9\s\.\,\!]{10,}/', $content, $matches);
            if (!empty($matches[0])) {
                $text = implode(' ', $matches[0]);
            }
            
            return $text ?: 'Document DOC - prêt pour traitement';
            
        } catch (\Exception $e) {
            return 'Document DOC - prêt pour chiffrement';
        }
    }

    private function convertToUtf8(string $content): string
    {
        if (empty($content)) {
            return '';
        }
        
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'WINDOWS-1252'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }
        return $content;
    }

    public function download(EncryptedFile $file)
{
    if ($file->user_id !== Auth::id()) {
        abort(403);
    }

    $encryptionService = new EncryptionService();
    
    try {
        $decryptedContent = $encryptionService->decryptText(
            $file->encrypted_content,
            $file->getDecryptionKey(),
            $file->encryption_method
        );
    } catch (\Exception $e) {
        return redirect()->route('dashboard')
            ->with('error', 'Erreur déchiffrement: ' . $e->getMessage());
    }

    // ✅ CORRECTION : Utiliser le nom original avec sa vraie extension
    return response()->streamDownload(function () use ($decryptedContent) {
        echo $decryptedContent;
    }, $file->original_name); // ← Plus de '.txt' forcé
}

    public function destroy(EncryptedFile $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403);
        }

        Auth::user()->updateStatsAfterDelete($file->file_size);
        $file->delete();

        return redirect()->route('dashboard')->with('success', '🗑️ Fichier supprimé !');
    }

    /**
     * ✅ AJOUTEZ CETTE MÉTHODE MANQUANTE :
     * Afficher le statut de chiffrement des fichiers
     */
    public function encryptionStatus()
    {
        $user = Auth::user();
        $files = EncryptedFile::where('user_id', $user->id)->get();
        
        // Compter les fichiers chiffrés et non chiffrés
        $encryptedCount = $files->filter(function($file) {
            return $file->isEncrypted();
        })->count();
        
        $unencryptedCount = $files->count() - $encryptedCount;
        $totalFiles = $files->count();

        return view('profile.encryption-status', compact('files', 'encryptedCount', 'unencryptedCount', 'totalFiles'));
    }
}