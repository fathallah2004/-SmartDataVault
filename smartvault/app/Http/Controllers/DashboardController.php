<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesFileDownloads;
use App\Models\EncryptedFile;
use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use HandlesFileDownloads;
    private $phpwordAvailable;

    public function __construct()
    {
        $this->phpwordAvailable = class_exists('PhpOffice\PhpWord\IOFactory');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $stats = [
            'total_files' => $user->total_files_encrypted,
            'total_storage' => $user->formatted_storage,
            'last_upload' => $user->last_upload_at?->diffForHumans() ?? 'Jamais'
        ];

        $query = EncryptedFile::where('user_id', $user->id);
        
        if ($request->filled('search')) {
            $query->where('original_name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('algorithm')) {
            $query->where('encryption_method', $request->algorithm);
        }
        if ($request->filled('date_filter')) {
            match($request->date_filter) {
                'today' => $query->whereDate('created_at', today()),
                'week' => $query->where('created_at', '>=', now()->subWeek()),
                'month' => $query->where('created_at', '>=', now()->subMonth()),
                default => null
            };
        }

        $files = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->except('page'));
        $algorithms = (new EncryptionService())->getAvailableAlgorithms();

        return view('dashboard', compact('files', 'stats', 'algorithms', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'encryption_method' => 'required|in:cesar,vigenere,xor-text,substitution,reverse'
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, ['txt', 'doc', 'docx', 'rtf', 'md', 'pdf'])) {
            return redirect()->route('dashboard')->with('error', 'Formats supportés: .txt, .doc, .docx, .rtf, .md, .pdf');
        }
        
        return $this->storeText($file, $request->encryption_method, $extension);
    }

    public function storeImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp,bmp,svg|max:20480'
        ]);

        $file = $request->file('image');
        return $this->storeImageFile($file);
    }

    private function storeText($file, $method, $extension)
    {
        try {
            $content = match($extension) {
                'txt', 'md', 'rtf' => $this->convertToUtf8(file_get_contents($file->path())),
                'pdf' => $this->extractTextFromPdf($file->path()),
                'docx' => $this->phpwordAvailable ? $this->extractTextFromDocx($file->path()) : throw new \Exception('Support DOCX non disponible'),
                'doc' => $this->extractTextFromDoc($file->path()),
                default => throw new \Exception('Format non supporté')
            };

            if (empty(trim($content))) {
                return redirect()->route('dashboard')->with('error', 'Aucun texte extrait');
            }

            $encrypted = (new EncryptionService())->encryptText($content, $method);
            EncryptedFile::create([
                'original_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $extension,
                'file_category' => 'text',
                'encrypted_content' => $encrypted['encrypted_content'],
                'encryption_method' => $encrypted['method'],
                'encryption_key' => $encrypted['key'],
                'iv' => $encrypted['iv'],
                'file_hash' => $encrypted['hash'],
                'user_id' => Auth::id()
            ]);

            Auth::user()->updateStatsAfterUpload($file->getSize());
            return redirect()->route('dashboard')->with('success', 'Fichier "' . $file->getClientOriginalName() . '" chiffré avec succès !');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', $e->getMessage());
        }
    }

    private function storeImageFile($file)
    {
        try {
            $extension = strtolower($file->getClientOriginalExtension());
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
            
            if (!in_array($extension, $allowedExtensions)) {
                return redirect()->route('dashboard')->with('error', 'Formats d\'image supportés: JPG, PNG, GIF, WEBP, BMP, SVG');
            }

            $imageData = file_get_contents($file->path());
            $encrypted = (new EncryptionService())->encryptImage($imageData);
            
            $fileSize = strlen($imageData);
            
            EncryptedFile::create([
                'original_name' => $file->getClientOriginalName(),
                'file_size' => $fileSize,
                'file_type' => $extension,
                'file_category' => 'image',
                'encrypted_content' => $encrypted['encrypted_content'],
                'encryption_method' => $encrypted['method'],
                'encryption_key' => $encrypted['key'],
                'iv' => $encrypted['iv'],
                'file_hash' => $encrypted['hash'],
                'user_id' => Auth::id()
            ]);

            Auth::user()->updateStatsAfterUpload($fileSize);
            return redirect()->route('dashboard')->with('success', 'Image "' . $file->getClientOriginalName() . '" chiffrée avec succès !');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', $e->getMessage());
        }
    }

    private function extractTextFromPdf(string $filePath): string
    {
        try {
            if (class_exists('Smalot\PdfParser\Parser')) {
                $text = trim(preg_replace('/\s+/', ' ', (new \Smalot\PdfParser\Parser())->parseFile($filePath)->getText()));
                if ($text) return $text;
            }
        } catch (\Exception $e) {}
        return 'Document PDF - texte prêt pour chiffrement';
    }

    private function extractTextFromDocx(string $filePath): string
    {
        try {
            $text = '';
            foreach (\PhpOffice\PhpWord\IOFactory::load($filePath)->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText') && ($t = $element->getText())) {
                        $text .= $t . "\n";
                    }
                }
            }
            return trim(preg_replace('/\n{3,}/', "\n\n", $text)) ?: 'Document DOCX - texte extrait';
        } catch (\Exception $e) {
            return 'Document DOCX - prêt pour chiffrement';
        }
    }

    private function extractTextFromDoc(string $filePath): string
    {
        try {
            preg_match_all('/[a-zA-Z0-9\s\.\,\!]{10,}/', file_get_contents($filePath), $matches);
            return !empty($matches[0]) ? implode(' ', $matches[0]) : 'Document DOC - prêt pour traitement';
        } catch (\Exception $e) {
            return 'Document DOC - prêt pour chiffrement';
        }
    }

    private function convertToUtf8(string $content): string
    {
        if (empty($content)) return '';
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'WINDOWS-1252'], true);
        return $encoding && $encoding !== 'UTF-8' ? mb_convert_encoding($content, 'UTF-8', $encoding) : $content;
    }

    public function download(EncryptedFile $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403);
        }

        if ($file->file_category === 'image') {
            return $this->downloadImage($file);
        }

        try {
            $decryptedContent = (new EncryptionService())->decryptText($file->encrypted_content, $file->getDecryptionKey(), $file->encryption_method);
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Erreur de déchiffrement: ' . $e->getMessage());
        }

        $fileType = strtolower($file->file_type);
        $fileName = $file->original_name;

        if ($fileType === 'docx' && $this->phpwordAvailable) {
            return $this->downloadAsDocx($decryptedContent, $fileName);
        }
        if ($fileType === 'pdf') {
            return $this->downloadAsPdf($decryptedContent, $fileName);
        }
        if ($fileType === 'doc') {
            $fileName = pathinfo($fileName, PATHINFO_FILENAME) . '.txt';
        }

        $mimeTypes = [
            'txt' => 'text/plain; charset=utf-8',
            'md' => 'text/markdown; charset=utf-8',
            'rtf' => 'application/rtf; charset=utf-8',
            'doc' => 'text/plain; charset=utf-8'
        ];
        if (!mb_check_encoding($decryptedContent, 'UTF-8')) {
            $decryptedContent = mb_convert_encoding($decryptedContent, 'UTF-8', 'auto');
        }
        if (in_array($fileType, ['txt', 'md', 'rtf']) && substr($decryptedContent, 0, 3) !== "\xEF\xBB\xBF") {
            $decryptedContent = "\xEF\xBB\xBF" . $decryptedContent;
        }

        return response($decryptedContent, 200)
            ->header('Content-Type', $mimeTypes[$fileType] ?? 'text/plain; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->header('Content-Transfer-Encoding', 'binary');
    }

    private function downloadImage(EncryptedFile $file)
    {
        try {
            $decryptedContent = (new EncryptionService())->decryptImage($file->encrypted_content, $file->getDecryptionKey(), $file->iv);
            
            if (empty($decryptedContent) || strlen($decryptedContent) < 100) {
                return redirect()->route('dashboard')->with('error', 'Le contenu déchiffré semble invalide.');
            }
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Erreur de déchiffrement: ' . $e->getMessage());
        }

        $extension = strtolower($file->file_type);
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'bmp' => 'image/bmp',
            'svg' => 'image/svg+xml'
        ];

        return response($decryptedContent, 200, [
            'Content-Type' => $mimeTypes[$extension] ?? 'image/jpeg',
            'Content-Disposition' => 'attachment; filename="' . $file->original_name . '"',
            'Content-Length' => strlen($decryptedContent),
            'Content-Transfer-Encoding' => 'binary',
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache'
        ]);
    }

    public function downloadEncrypted(EncryptedFile $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403);
        }

        if ($file->file_category !== 'image') {
            return redirect()->route('dashboard')->with('error', 'Le téléchargement chiffré est réservé aux images.');
        }

        $encryptedBinary = base64_decode($file->encrypted_content, true);
        if ($encryptedBinary === false) {
            return redirect()->route('dashboard')->with('error', 'Impossible de récupérer le contenu chiffré.');
        }

        $extension = strtolower($file->file_type);
        $fileName = pathinfo($file->original_name, PATHINFO_FILENAME) . '_encrypted.' . $extension;
        
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'bmp' => 'image/bmp',
            'svg' => 'image/svg+xml'
        ];

        return response($encryptedBinary, 200, [
            'Content-Type' => $mimeTypes[$extension] ?? 'image/jpeg',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Content-Length' => strlen($encryptedBinary),
            'Content-Transfer-Encoding' => 'binary',
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache'
        ]);
    }

    public function destroy(EncryptedFile $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403);
        }
        
        $fileName = $file->original_name;
        Auth::user()->updateStatsAfterDelete($file->file_size);
        $file->delete();
        
        return redirect()->route('dashboard')->with('delete', 'Fichier "' . $fileName . '" supprimé définitivement !');
    }
}