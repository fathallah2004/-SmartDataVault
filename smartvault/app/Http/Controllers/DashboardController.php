<?php

namespace App\Http\Controllers;

use App\Models\EncryptedFile;
use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private $phpwordAvailable;

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

        $mimeTypes = ['txt' => 'text/plain; charset=utf-8', 'md' => 'text/markdown; charset=utf-8', 'rtf' => 'application/rtf; charset=utf-8', 'doc' => 'text/plain; charset=utf-8'];
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

    private function downloadAsDocx($content, $originalName)
    {
        try {
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $section = $phpWord->addSection();
            foreach (explode("\n", $content) as $line) {
                trim($line) ? $section->addText($line) : $section->addTextBreak();
            }
            $tempFile = tempnam(sys_get_temp_dir(), 'docx_');
            \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007')->save($tempFile);
            $fileContent = file_get_contents($tempFile);
            @unlink($tempFile);
            return response($fileContent, 200)
                ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
                ->header('Content-Disposition', 'attachment; filename="' . $originalName . '"')
                ->header('Content-Transfer-Encoding', 'binary');
        } catch (\Exception $e) {
            $fileName = pathinfo($originalName, PATHINFO_FILENAME) . '.txt';
            if (substr($content, 0, 3) !== "\xEF\xBB\xBF") $content = "\xEF\xBB\xBF" . $content;
            return response($content, 200)
                ->header('Content-Type', 'text/plain; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        }
    }

    private function downloadAsPdf($content, $originalName)
    {
        try {
            $content = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', mb_convert_encoding($content, 'UTF-8', 'UTF-8'));
            $fpdfClass = class_exists('FPDF') ? 'FPDF' : (class_exists('\FPDF') ? '\FPDF' : null);
            
            if ($fpdfClass) {
                try {
                    $pdf = new $fpdfClass();
                    $pdf->SetCreator('SmartDataVault');
                    $pdf->SetAuthor('SmartDataVault');
                    $pdf->SetTitle('Document déchiffré');
                    $pdf->AddPage();
                    $pdf->SetFont('Arial', '', 12);
                    $pdf->SetMargins(20, 20, 20);
                    foreach (explode("\n", $content) as $line) {
                        trim($line) ? $pdf->MultiCell(0, 8, @mb_convert_encoding($line, 'ISO-8859-1', 'UTF-8') ?: $line, 0, 'L') : $pdf->Ln(5);
                    }
                    if ($pdfOutput = $pdf->Output('', 'S')) {
                        return response($pdfOutput, 200)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="' . $originalName . '"')->header('Content-Transfer-Encoding', 'binary');
                    }
                } catch (\Exception $e) {
                    \Log::error('Erreur FPDF: ' . $e->getMessage());
                }
            }
            
            if (class_exists('TCPDF')) {
                $pdf = new \TCPDF();
                $pdf->SetCreator('SmartDataVault');
                $pdf->SetAuthor('SmartDataVault');
                $pdf->SetTitle('Document déchiffré');
                $pdf->AddPage();
                foreach (explode("\n", $content) as $line) {
                    trim($line) ? $pdf->Write(0, $line, '', 0, 'L', true, 0, false, false, 0) : $pdf->Ln(5);
                }
                return response($pdf->Output('', 'S'), 200)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="' . $originalName . '"')->header('Content-Transfer-Encoding', 'binary');
            }
            
            return response($this->createSimplePdf($content), 200)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="' . $originalName . '"')->header('Content-Transfer-Encoding', 'binary');
        } catch (\Exception $e) {
            $fileName = pathinfo($originalName, PATHINFO_FILENAME) . '.txt';
            if (substr($content, 0, 3) !== "\xEF\xBB\xBF") $content = "\xEF\xBB\xBF" . $content;
            return response($content, 200)->header('Content-Type', 'text/plain; charset=utf-8')->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        }
    }

    private function createSimplePdf($text)
    {
        $lines = array_filter(array_map('trim', explode("\n", preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', mb_convert_encoding($text, 'UTF-8', 'UTF-8')))), fn($l) => $l !== '') ?: ['Document vide'];
        $pdfParts = [];
        $xref = [];
        $pdfParts[] = "%PDF-1.4\n";
        $currentOffset = strlen($pdfParts[0]);
        $pdfParts[] = ($catalog = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n");
        $xref[1] = $currentOffset;
        $currentOffset += strlen($catalog);
        $pdfParts[] = ($pages = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n");
        $xref[2] = $currentOffset;
        $currentOffset += strlen($pages);
        $pdfParts[] = ($pageObj = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> >> >> >>\nendobj\n");
        $xref[3] = $currentOffset;
        $currentOffset += strlen($pageObj);
        
        $pageContent = "";
        $y = 750;
        foreach ($lines as $line) {
            if ($y < 50) break;
            foreach (str_split($line, 70) as $wrappedLine) {
                if ($y < 50) break;
                $wrappedLine = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], @mb_convert_encoding($wrappedLine, 'ISO-8859-1', 'UTF-8') ?: $line);
                $pageContent .= "BT\n/F1 12 Tf\n50 {$y} Td\n({$wrappedLine}) Tj\nET\n";
                $y -= 14;
            }
        }
        if (empty($pageContent)) $pageContent = "BT\n/F1 12 Tf\n50 750 Td\n(Document vide) Tj\nET\n";
        
        $pdfParts[] = ($contentsObj = "4 0 obj\n<< /Length " . strlen($pageContent) . " >>\nstream\n" . $pageContent . "\nendstream\nendobj\n");
        $xref[4] = $currentOffset;
        $pdfContent = implode('', $pdfParts);
        $xrefOffset = strlen($pdfContent);
        $pdfContent .= "xref\n0 5\n0000000000 65535 f \n";
        foreach ($xref as $objOffset) $pdfContent .= sprintf("%010d 00000 n \n", $objOffset);
        $pdfContent .= "trailer\n<< /Size 5 /Root 1 0 R >>\nstartxref\n" . $xrefOffset . "\n%%EOF\n";
        return $pdfContent;
    }

    public function destroy(EncryptedFile $file)
    {
        if ($file->user_id !== Auth::id()) abort(403);
        $fileName = $file->original_name;
        Auth::user()->updateStatsAfterDelete($file->file_size);
        $file->delete();
        return redirect()->route('dashboard')->with('delete', 'Fichier "' . $fileName . '" supprimé définitivement !');
    }
}