<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EncryptedFile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    private $phpwordAvailable;

    public function __construct()
    {
        $this->phpwordAvailable = class_exists('PhpOffice\PhpWord\IOFactory');
    }

    public function index(Request $request)
    {
        $search = (string) $request->query('search', '');
        $role = (string) $request->query('role', '');
        $sort = (string) $request->query('sort', 'created_at');
        $direction = strtolower((string) $request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $usersQuery = User::query()
            ->withCount([
                'encryptedFiles' => function ($query) {
                    $query->whereNull('encrypted_files.deleted_at');
                },
            ])
            ->withSum([
                'encryptedFiles as encrypted_files_size_sum' => function ($query) {
                    $query->whereNull('encrypted_files.deleted_at');
                },
            ], 'file_size');

        if (!empty($search)) {
            $usersQuery->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($role) && in_array($role, ['admin', 'user'], true)) {
            $usersQuery->where('role', $role);
        }

        $allowedSorts = ['name', 'email', 'created_at', 'last_login_at', 'last_upload_at', 'encrypted_files_count'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }

        if ($sort === 'encrypted_files_count') {
            $usersQuery->orderBy('encrypted_files_count', $direction);
        } else {
            $usersQuery->orderBy($sort, $direction);
        }

        $users = $usersQuery
            ->paginate(10)
            ->withQueryString();

        $stats = $this->buildStats();

        if ($request->ajax()) {
            return view('admin.users.partials.table', compact('users'))->render();
        }

        return view('admin.users.index', [
            'users' => $users,
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'role' => $role,
                'sort' => $sort,
                'direction' => $direction,
            ],
        ]);
    }

    public function show(User $user, Request $request): JsonResponse
    {
        $user->loadCount([
            'encryptedFiles' => function ($query) {
                $query->whereNull('encrypted_files.deleted_at');
            },
        ]);

        $filesQuery = EncryptedFile::where('user_id', $user->id)
            ->whereNull('deleted_at');

        // Recherche par nom de fichier
        if ($request->filled('files_search')) {
            $search = $request->query('files_search');
            $filesQuery->where('original_name', 'like', "%{$search}%");
        }

        // Filtre par algorithme
        if ($request->filled('files_algorithm')) {
            $filesQuery->where('encryption_method', $request->query('files_algorithm'));
        }

        // Filtre par date
        if ($request->filled('files_date_filter')) {
            match($request->query('files_date_filter')) {
                'today' => $filesQuery->whereDate('created_at', today()),
                'week' => $filesQuery->where('created_at', '>=', now()->subWeek()),
                'month' => $filesQuery->where('created_at', '>=', now()->subMonth()),
                default => null
            };
        }

        $filesQuery->orderByDesc('created_at');

        // Pagination pour les fichiers
        $page = (int) $request->query('files_page', 1);
        $filesPaginated = $filesQuery->paginate(10, ['*'], 'files_page', $page)->withQueryString();

        $storageSum = EncryptedFile::where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->sum('file_size');

        $files = $filesPaginated->map(function (EncryptedFile $file) {
            return [
                'id' => $file->id,
                'name' => $file->original_name,
                'size' => $this->formatBytes($file->file_size),
                'algorithm' => $file->getAlgorithmNameAttribute(),
                'method' => $file->encryption_method,
                'created_at' => optional($file->created_at)->format('d/m/Y H:i'),
                'created_at_iso' => optional($file->created_at)->toIso8601String(),
            ];
        });

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'formatted_role' => ucfirst($user->role),
                'created_at' => optional($user->created_at)->format('d/m/Y H:i'),
                'last_login_at' => optional($user->last_login_at)->format('d/m/Y H:i'),
                'last_upload_at' => optional($user->last_upload_at)->format('d/m/Y H:i'),
                'files_count' => $user->encrypted_files_count,
                'total_storage' => $this->formatBytes($storageSum),
                'status' => $user->trashed() ? 'inactive' : 'active',
            ],
            'files' => $files,
            'files_pagination' => [
                'current_page' => $filesPaginated->currentPage(),
                'last_page' => $filesPaginated->lastPage(),
                'per_page' => $filesPaginated->perPage(),
                'total' => $filesPaginated->total(),
                'from' => $filesPaginated->firstItem(),
                'to' => $filesPaginated->lastItem(),
            ],
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        DB::transaction(function () use ($user): void {
            $user->encryptedFiles()->each(function (EncryptedFile $file): void {
                $file->delete();
            });

            $user->delete();
        });

        return response()->json([
            'message' => 'Utilisateur supprimé (soft delete) avec succès.',
        ]);
    }

    private function buildStats(): array
    {
        $totalFiles = EncryptedFile::query()->whereNull('deleted_at')->count();
        $totalAdmins = User::query()->where('role', 'admin')->count();
        $totalUsers = User::query()->where('role', 'user')->count();
        $totalStorageBytes = EncryptedFile::query()->whereNull('deleted_at')->sum('file_size');

        return [
            'total_files' => $totalFiles,
            'total_admins' => $totalAdmins,
            'total_users' => $totalUsers,
            'total_storage' => $this->formatBytes($totalStorageBytes),
        ];
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = min((int) floor(log($bytes, 1024)), count($units) - 1);
        $value = $bytes / (1024 ** $pow);

        return sprintf('%.2f %s', $value, $units[$pow]);
    }

    public function downloadFile(EncryptedFile $file)
    {
        // Les admins peuvent télécharger n'importe quel fichier
        
        // Gérer les images différemment
        if ($file->file_category === 'image') {
            return $this->downloadImage($file);
        }

        try {
            $encryptionService = new \App\Services\EncryptionService();
            $decryptedContent = $encryptionService->decryptText($file->encrypted_content, $file->getDecryptionKey(), $file->encryption_method);
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'Erreur de déchiffrement: ' . $e->getMessage());
        }

        $fileType = strtolower($file->file_type);
        $fileName = $file->original_name;

        // Gérer les fichiers .docx et .pdf comme dans la partie user
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

    private function downloadImage(EncryptedFile $file)
    {
        try {
            $encryptionService = new \App\Services\EncryptionService();
            $decryptedContent = $encryptionService->decryptImage($file->encrypted_content, $file->getDecryptionKey(), $file->iv);
            
            if (empty($decryptedContent)) {
                return redirect()->route('admin.users.index')->with('error', 'Le contenu déchiffré est vide.');
            }
            
            if (strlen($decryptedContent) < 100) {
                return redirect()->route('admin.users.index')->with('error', 'Le contenu déchiffré semble invalide.');
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'Erreur de déchiffrement: ' . $e->getMessage());
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

    public function deleteFile(EncryptedFile $file): JsonResponse
    {
        // Les admins peuvent supprimer n'importe quel fichier
        try {
            $file->delete();
            
            return response()->json([
                'message' => 'Fichier supprimé avec succès.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la suppression du fichier: ' . $e->getMessage(),
            ], 500);
        }
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
}

