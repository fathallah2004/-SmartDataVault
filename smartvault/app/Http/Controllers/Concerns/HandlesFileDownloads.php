<?php

namespace App\Http\Controllers\Concerns;

trait HandlesFileDownloads
{
    protected function downloadAsDocx($content, $originalName)
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
            if (substr($content, 0, 3) !== "\xEF\xBB\xBF") {
                $content = "\xEF\xBB\xBF" . $content;
            }
            return response($content, 200)
                ->header('Content-Type', 'text/plain; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        }
    }

    protected function downloadAsPdf($content, $originalName)
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
                        return response($pdfOutput, 200)
                            ->header('Content-Type', 'application/pdf')
                            ->header('Content-Disposition', 'attachment; filename="' . $originalName . '"')
                            ->header('Content-Transfer-Encoding', 'binary');
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
                    if (trim($line)) {
                        $pdf->Write(0, $line, '', 0, 'L', true, 0, false, false, 0);
                    } else {
                        $pdf->Ln(5);
                    }
                }
                
                return response($pdf->Output('', 'S'), 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'attachment; filename="' . $originalName . '"')
                    ->header('Content-Transfer-Encoding', 'binary');
            }
            
            return response($this->createSimplePdf($content), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $originalName . '"')
                ->header('Content-Transfer-Encoding', 'binary');
        } catch (\Exception $e) {
            $fileName = pathinfo($originalName, PATHINFO_FILENAME) . '.txt';
            if (substr($content, 0, 3) !== "\xEF\xBB\xBF") {
                $content = "\xEF\xBB\xBF" . $content;
            }
            
            return response($content, 200)
                ->header('Content-Type', 'text/plain; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        }
    }

    protected function createSimplePdf($text)
    {
        $cleanedText = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', mb_convert_encoding($text, 'UTF-8', 'UTF-8'));
        $lines = array_filter(array_map('trim', explode("\n", $cleanedText)), fn($l) => $l !== '') ?: ['Document vide'];
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
        if (empty($pageContent)) {
            $pageContent = "BT\n/F1 12 Tf\n50 750 Td\n(Document vide) Tj\nET\n";
        }
        
        $pdfParts[] = ($contentsObj = "4 0 obj\n<< /Length " . strlen($pageContent) . " >>\nstream\n" . $pageContent . "\nendstream\nendobj\n");
        $xref[4] = $currentOffset;
        $pdfContent = implode('', $pdfParts);
        $xrefOffset = strlen($pdfContent);
        $pdfContent .= "xref\n0 5\n0000000000 65535 f \n";
        foreach ($xref as $objOffset) {
            $pdfContent .= sprintf("%010d 00000 n \n", $objOffset);
        }
        $pdfContent .= "trailer\n<< /Size 5 /Root 1 0 R >>\nstartxref\n" . $xrefOffset . "\n%%EOF\n";
        return $pdfContent;
    }
}

