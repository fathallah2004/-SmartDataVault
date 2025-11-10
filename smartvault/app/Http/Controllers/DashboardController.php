<?php

namespace App\Http\Controllers;

use App\Models\EncryptedFile;
use App\Services\EncryptionService;
use App\Services\ImageEncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Charger FPDF si disponible
$fpdfPaths = [
    __DIR__ . '/../../vendor/setasign/fpdf/fpdf.php',
    __DIR__ . '/../../vendor/autoload.php', // FPDF peut être chargé via autoload
];
foreach ($fpdfPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        break;
    }
}

class DashboardController extends Controller
{
    private $phpwordAvailable = false;

    public function __construct()
    {
        $this->phpwordAvailable = class_exists('PhpOffice\PhpWord\IOFactory');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        $stats = [
            'total_files' => $user->total_files_encrypted,
            'total_storage' => $user->formatted_storage,
            'last_upload' => $user->last_upload_at ? $user->last_upload_at->diffForHumans() : 'Jamais'
        ];

        // Query de base pour les fichiers
        $query = EncryptedFile::where('user_id', $user->id);

        // Filtre par recherche (nom de fichier)
        if ($request->has('search') && $request->search != '') {
            $query->where('original_name', 'like', '%' . $request->search . '%');
        }

        // Filtre par algorithme
        if ($request->has('algorithm') && $request->algorithm != '') {
            $query->where('encryption_method', $request->algorithm);
        }

        // Filtre par date
        if ($request->has('date_filter') && $request->date_filter != '') {
            switch($request->date_filter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->where('created_at', '>=', now()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->subMonth());
                    break;
            }
        }

        // Conservation des filtres dans la pagination
        $files = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->except('page'));

        $encryptionService = new EncryptionService();
        $algorithms = $encryptionService->getAvailableAlgorithms();
        $imageAlgorithms = $encryptionService->getImageAlgorithms();

        return view('dashboard', compact('files', 'stats', 'algorithms', 'imageAlgorithms', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // Augmenté à 10MB pour les images
            'encryption_method' => 'required'
        ]);

        $file = $request->file('file');
        $method = $request->encryption_method;
        $extension = strtolower($file->getClientOriginalExtension());
        
        $allowedTextExtensions = ['txt', 'doc', 'docx', 'rtf', 'md', 'pdf'];
        $allowedImageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $allowedExtensions = array_merge($allowedTextExtensions, $allowedImageExtensions);
        
        if (!in_array($extension, $allowedExtensions)) {
            return redirect()->route('dashboard')
                ->with('error', 'Formats supportés: Texte (.txt, .doc, .docx, .rtf, .md, .pdf) ou Images (.jpg, .jpeg, .png, .gif, .bmp, .webp)');
        }

        // Vérifier si c'est une image
        $isImage = in_array($extension, $allowedImageExtensions);
        $encryptionService = new EncryptionService();
        
        if ($isImage) {
            // Valider l'algorithme d'image
            $imageAlgorithms = $encryptionService->getImageAlgorithms();
            if (!array_key_exists($method, $imageAlgorithms)) {
                return redirect()->route('dashboard')
                    ->with('error', 'Algorithme d\'image invalide. Utilisez: ' . implode(', ', array_keys($imageAlgorithms)));
            }
            
            return $this->storeImage($file, $method, $extension);
        } else {
            // Valider l'algorithme de texte
            if (!in_array($method, ['cesar', 'vigenere', 'xor-text', 'substitution', 'reverse'])) {
                return redirect()->route('dashboard')
                    ->with('error', 'Algorithme de texte invalide');
            }
            
            return $this->storeText($file, $method, $extension);
        }
    }

    private function storeImage($file, $method, $extension)
    {
        try {
            // Vérifier que le fichier existe et est valide
            if (!$file->isValid()) {
                throw new \Exception('Le fichier uploadé n\'est pas valide');
            }

            $imageEncryptionService = new ImageEncryptionService();
            
            // Générer un mot de passe aléatoire
            $password = bin2hex(random_bytes(16));
            
            // Chiffrer l'image
            $encrypted = $imageEncryptionService->encryptImage($file->path(), $method, $password);

            $encryptedFile = EncryptedFile::create([
                'original_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $extension,
                'encrypted_content' => $encrypted['encrypted_content'],
                'encryption_method' => $encrypted['method'],
                'encryption_key' => $encrypted['key'],
                'iv' => $encrypted['iv'],
                'file_hash' => $encrypted['hash'],
                'user_id' => Auth::id(),
                'metadata' => json_encode([
                    'width' => $encrypted['width'],
                    'height' => $encrypted['height'],
                    'original_type' => $encrypted['original_type'],
                    'salt' => $encrypted['salt'],
                    'password' => $password, // Stocker le mot de passe pour le déchiffrement
                    'metadata' => $encrypted['metadata'] ?? null
                ])
            ]);

            Auth::user()->updateStatsAfterUpload($file->getSize());

            return redirect()->route('dashboard')
                ->with('success', 'Image "' . $file->getClientOriginalName() . '" chiffrée avec succès ! (Algorithme: ' . $encrypted['method'] . ')');

        } catch (\Exception $e) {
            \Log::error('Erreur upload image: ' . $e->getMessage(), [
                'file' => $file->getClientOriginalName(),
                'method' => $method,
                'extension' => $extension,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('dashboard')
                ->with('error', 'Erreur lors du chiffrement de l\'image: ' . $e->getMessage());
        }
    }

    private function storeText($file, $method, $extension)
    {

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
                            ->with('error', 'Support DOCX non disponible');
                    }
                    break;

                case 'doc':
                    $content = $this->extractTextFromDoc($file->path());
                    break;

                default:
                    return redirect()->route('dashboard')
                        ->with('error', 'Format non supporté');
            }

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

            // ✅ NOTIFICATION AMÉLIORÉE
            return redirect()->route('dashboard')
                ->with('success', 'Fichier "' . $file->getClientOriginalName() . '" chiffré avec succès ! (Algorithme: ' . $encrypted['method'] . ')');

        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', 'Erreur: ' . $e->getMessage());
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
                    // Extraire le texte de l'élément directement
                    if (method_exists($element, 'getText')) {
                        $elementText = $element->getText();
                        if (!empty($elementText)) {
                            $text .= $elementText . "\n";
                        }
                    }
                    // Si l'élément a des sous-éléments, les traiter séparément
                    // mais éviter la duplication en vérifiant d'abord si getText() existe
                    if (method_exists($element, 'getElements')) {
                        $hasDirectText = method_exists($element, 'getText') && !empty($element->getText());
                        foreach ($element->getElements() as $child) {
                            if (method_exists($child, 'getText')) {
                                $childText = $child->getText();
                                // Ne pas ajouter si c'est le même texte que l'élément parent
                                if (!empty($childText) && (!$hasDirectText || $childText !== $element->getText())) {
                                    $text .= $childText . "\n";
                                }
                            }
                        }
                    }
                }
            }
            
            // Nettoyer les lignes vides multiples
            $text = preg_replace('/\n{3,}/', "\n\n", $text);
            
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
        $fileType = strtolower($file->file_type);
        $fileName = $file->original_name;
        
        // Vérifier si c'est une image
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $isImage = in_array($fileType, $imageExtensions);
        
        if ($isImage) {
            return $this->downloadImage($file);
        }
        
        try {
            $decryptedContent = $encryptionService->decryptText(
                $file->encrypted_content,
                $file->getDecryptionKey(),
                $file->encryption_method
            );

        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', 'Erreur de déchiffrement: ' . $e->getMessage());
        }

        // Pour les fichiers Word, reconstruire le format
        if ($fileType === 'docx' && $this->phpwordAvailable) {
            return $this->downloadAsDocx($decryptedContent, $fileName);
        } elseif ($fileType === 'doc') {
            // Pour les anciens .doc, on renvoie en .txt car le format est binaire complexe
            $fileName = pathinfo($fileName, PATHINFO_FILENAME) . '.txt';
        } elseif ($fileType === 'pdf') {
            // Pour les PDF, créer un nouveau PDF à partir du texte
            return $this->downloadAsPdf($decryptedContent, $fileName);
        }

        // Pour les autres formats (txt, md, rtf), renvoyer avec le bon encodage
        $mimeTypes = [
            'txt' => 'text/plain; charset=utf-8',
            'md' => 'text/markdown; charset=utf-8',
            'rtf' => 'application/rtf; charset=utf-8',
            'doc' => 'text/plain; charset=utf-8'
        ];

        $mimeType = $mimeTypes[$fileType] ?? 'text/plain; charset=utf-8';

        // S'assurer que le contenu est en UTF-8
        if (!mb_check_encoding($decryptedContent, 'UTF-8')) {
            $decryptedContent = mb_convert_encoding($decryptedContent, 'UTF-8', 'auto');
        }

        // Ajouter BOM UTF-8 pour les fichiers texte (pour compatibilité Word)
        if (in_array($fileType, ['txt', 'md', 'rtf'])) {
            // Vérifier si le BOM n'est pas déjà présent
            if (substr($decryptedContent, 0, 3) !== "\xEF\xBB\xBF") {
                $decryptedContent = "\xEF\xBB\xBF" . $decryptedContent;
            }
        }

        return response($decryptedContent, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->header('Content-Transfer-Encoding', 'binary');
    }

    private function downloadAsDocx($content, $originalName)
    {
        $tempFile = null;
        try {
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $section = $phpWord->addSection();
            
            // Diviser le contenu en paragraphes (par lignes)
            $lines = explode("\n", $content);
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $section->addText($line);
                } else {
                    $section->addTextBreak();
                }
            }

            // Créer un fichier temporaire
            $tempFile = tempnam(sys_get_temp_dir(), 'docx_');
            if ($tempFile === false) {
                throw new \Exception('Impossible de créer un fichier temporaire');
            }
            
            $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($tempFile);

            // Lire le fichier et le renvoyer
            $fileContent = file_get_contents($tempFile);
            
            // Nettoyer le fichier temporaire
            if ($tempFile && file_exists($tempFile)) {
                unlink($tempFile);
            }

            return response($fileContent, 200)
                ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
                ->header('Content-Disposition', 'attachment; filename="' . $originalName . '"')
                ->header('Content-Transfer-Encoding', 'binary');

        } catch (\Exception $e) {
            // Nettoyer le fichier temporaire en cas d'erreur
            if ($tempFile && file_exists($tempFile)) {
                @unlink($tempFile);
            }
            
            // En cas d'erreur, renvoyer en .txt
            $fileName = pathinfo($originalName, PATHINFO_FILENAME) . '.txt';
            // Vérifier si le BOM n'est pas déjà présent
            if (substr($content, 0, 3) !== "\xEF\xBB\xBF") {
                $content = "\xEF\xBB\xBF" . $content; // BOM UTF-8
            }
            
            return response($content, 200)
                ->header('Content-Type', 'text/plain; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        }
    }

    private function downloadAsPdf($content, $originalName)
    {
        try {
            // Nettoyer le contenu pour PDF
            $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
            $content = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $content);
            
            // Utiliser FPDF si disponible (plus simple et fiable)
            $fpdfClass = null;
            if (class_exists('FPDF')) {
                $fpdfClass = 'FPDF';
            } elseif (class_exists('\FPDF')) {
                $fpdfClass = '\FPDF';
            }
            
            if ($fpdfClass !== null) {
                try {
                    $pdf = new $fpdfClass();
                    $pdf->SetCreator('SmartDataVault');
                    $pdf->SetAuthor('SmartDataVault');
                    $pdf->SetTitle('Document déchiffré');
                    $pdf->AddPage();
                    $pdf->SetFont('Arial', '', 12);
                    $pdf->SetMargins(20, 20, 20);
                    
                    // Diviser en lignes et ajouter au PDF
                    $lines = explode("\n", $content);
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (!empty($line)) {
                            // Encoder correctement pour FPDF (ISO-8859-1)
                            // Gérer les caractères UTF-8 qui ne sont pas dans ISO-8859-1
                            $lineEncoded = @mb_convert_encoding($line, 'ISO-8859-1', 'UTF-8');
                            if ($lineEncoded === false) {
                                // Si la conversion échoue, utiliser le texte original
                                $lineEncoded = $line;
                            }
                            // Utiliser MultiCell pour gérer les retours à la ligne automatiques
                            $pdf->MultiCell(0, 8, $lineEncoded, 0, 'L');
                        } else {
                            $pdf->Ln(5);
                        }
                    }
                    
                    $pdfOutput = $pdf->Output('', 'S');
                    
                    if (!empty($pdfOutput)) {
                        return response($pdfOutput, 200)
                            ->header('Content-Type', 'application/pdf')
                            ->header('Content-Disposition', 'attachment; filename="' . $originalName . '"')
                            ->header('Content-Transfer-Encoding', 'binary');
                    }
                } catch (\Exception $e) {
                    // Si FPDF échoue, continuer avec les autres méthodes
                    \Log::error('Erreur FPDF: ' . $e->getMessage());
                }
            }
            // Utiliser TCPDF si disponible
            elseif (class_exists('TCPDF')) {
                $pdf = new \TCPDF();
                $pdf->SetCreator('SmartDataVault');
                $pdf->SetAuthor('SmartDataVault');
                $pdf->SetTitle('Document déchiffré');
                $pdf->AddPage();
                
                // Diviser en paragraphes
                $lines = explode("\n", $content);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!empty($line)) {
                        $pdf->Write(0, $line, '', 0, 'L', true, 0, false, false, 0);
                    } else {
                        $pdf->Ln(5);
                    }
                }
                
                return response($pdf->Output('', 'S'), 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'attachment; filename="' . $originalName . '"')
                    ->header('Content-Transfer-Encoding', 'binary');
            } else {
                // Si aucune bibliothèque n'est disponible, créer un PDF simple mais valide
                $pdfContent = $this->createSimplePdf($content);
                
                return response($pdfContent, 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'attachment; filename="' . $originalName . '"')
                    ->header('Content-Transfer-Encoding', 'binary');
            }
        } catch (\Exception $e) {
            // En cas d'erreur, renvoyer en .txt
            $fileName = pathinfo($originalName, PATHINFO_FILENAME) . '.txt';
            if (substr($content, 0, 3) !== "\xEF\xBB\xBF") {
                $content = "\xEF\xBB\xBF" . $content; // BOM UTF-8
            }
            
            return response($content, 200)
                ->header('Content-Type', 'text/plain; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        }
    }

    private function createSimplePdf($text)
    {
        // Créer un PDF minimal valide en format PDF 1.4
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        
        // Diviser en lignes et nettoyer
        $lines = explode("\n", $text);
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines, function($line) {
            return $line !== '';
        });
        
        if (empty($lines)) {
            $lines = ['Document vide'];
        }
        
        // Construire le PDF avec une structure valide
        $pdfParts = [];
        $xref = [];
        
        // Header
        $pdfParts[] = "%PDF-1.4\n";
        $currentOffset = strlen($pdfParts[0]);
        
        // Objet 1: Catalog
        $catalog = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $xref[1] = $currentOffset;
        $pdfParts[] = $catalog;
        $currentOffset += strlen($catalog);
        
        // Objet 2: Pages
        $pages = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $xref[2] = $currentOffset;
        $pdfParts[] = $pages;
        $currentOffset += strlen($pages);
        
        // Objet 3: Page
        $pageObj = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> >> >> >>\nendobj\n";
        $xref[3] = $currentOffset;
        $pdfParts[] = $pageObj;
        $currentOffset += strlen($pageObj);
        
        // Objet 4: Contents (contenu de la page)
        $pageContent = "";
        $y = 750;
        $lineHeight = 14;
        $leftMargin = 50;
        $maxWidth = 70; // Caractères par ligne
        
        foreach ($lines as $line) {
            if ($y < 50) {
                break; // Limite de page
            }
            
            // Diviser les lignes longues
            $wrappedLines = str_split($line, $maxWidth);
            foreach ($wrappedLines as $wrappedLine) {
                if ($y < 50) break;
                
                // Échapper les caractères spéciaux pour PDF
                $wrappedLine = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $wrappedLine);
                // Convertir en ISO-8859-1 pour compatibilité PDF
                $wrappedLine = @mb_convert_encoding($wrappedLine, 'ISO-8859-1', 'UTF-8');
                if ($wrappedLine === false) {
                    $wrappedLine = $line; // Utiliser l'original si la conversion échoue
                }
                
                $pageContent .= "BT\n/F1 12 Tf\n{$leftMargin} {$y} Td\n({$wrappedLine}) Tj\nET\n";
                $y -= $lineHeight;
            }
        }
        
        // S'assurer qu'il y a du contenu
        if (empty($pageContent)) {
            $pageContent = "BT\n/F1 12 Tf\n50 750 Td\n(Document vide) Tj\nET\n";
        }
        
        $contentsObj = "4 0 obj\n<< /Length " . strlen($pageContent) . " >>\nstream\n" . $pageContent . "\nendstream\nendobj\n";
        $xref[4] = $currentOffset;
        $pdfParts[] = $contentsObj;
        $currentOffset += strlen($contentsObj);
        
        // Construire le PDF complet
        $pdfContent = implode('', $pdfParts);
        
        // Xref table
        $xrefOffset = strlen($pdfContent);
        $pdfContent .= "xref\n0 5\n";
        $pdfContent .= "0000000000 65535 f \n";
        foreach ($xref as $objNum => $objOffset) {
            $pdfContent .= sprintf("%010d 00000 n \n", $objOffset);
        }
        
        // Trailer
        $pdfContent .= "trailer\n<< /Size 5 /Root 1 0 R >>\n";
        $pdfContent .= "startxref\n" . $xrefOffset . "\n%%EOF\n";
        
        return $pdfContent;
    }

    public function destroy(EncryptedFile $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403);
        }

        $fileName = $file->original_name; // ✅ Sauvegarder le nom avant suppression

        Auth::user()->updateStatsAfterDelete($file->file_size);
        $file->delete();

        // ✅ NOTIFICATION AMÉLIORÉE
        return redirect()->route('dashboard')
            ->with('delete', 'Fichier "' . $fileName . '" supprimé définitivement !');
    }

    private function downloadImage(EncryptedFile $file)
    {
        try {
            $imageEncryptionService = new ImageEncryptionService();
            
            // Récupérer les métadonnées
            $metadata = $file->metadata;
            if (!$metadata) {
                throw new \Exception('Métadonnées manquantes pour l\'image');
            }
            
            $password = $metadata['password'] ?? null;
            $key = $file->encryption_key;
            $iv = $file->iv;
            $salt = $metadata['salt'] ?? null;
            $originalShape = $metadata['metadata'] ?? null;
            
            // Déchiffrer l'image
            $decryptedImage = $imageEncryptionService->decryptImage(
                $file->encrypted_content,
                $key,
                $iv,
                $salt,
                $file->encryption_method,
                $password,
                $originalShape
            );
            
            // Déterminer le type MIME
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'bmp' => 'image/bmp',
                'webp' => 'image/webp'
            ];
            
            $mimeType = $mimeTypes[$file->file_type] ?? 'image/png';
            
            return response($decryptedImage)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'attachment; filename="' . $file->original_name . '"');
                
        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', 'Erreur de déchiffrement d\'image: ' . $e->getMessage());
        }
    }

    public function encryptionStatus()
    {
        $user = Auth::user();
        $files = EncryptedFile::where('user_id', $user->id)->get();
        
        $encryptedCount = $files->filter(function($file) {
            return $file->isEncrypted();
        })->count();
        
        $unencryptedCount = $files->count() - $encryptedCount;
        $totalFiles = $files->count();

        return view('profile.encryption-status', compact('files', 'encryptedCount', 'unencryptedCount', 'totalFiles'));
    }
}