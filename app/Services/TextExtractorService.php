<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * TextExtractorService
 *
 * Extracts raw text from uploaded resume files.
 * Supports: PDF, DOCX, TXT
 *
 * For PDF: Uses pdftotext (poppler-utils) CLI or smalot/pdfparser
 * For DOCX: Uses phpoffice/phpword
 * For TXT: Direct file read
 */
class TextExtractorService
{
    /**
     * Extract text from file at given path.
     *
     * @param string $filePath Absolute path to file
     * @param string $fileType pdf|docx|txt
     * @return string Extracted plain text
     */
    public function extract(string $filePath, string $fileType): string
    {
        return match (strtolower($fileType)) {
            'pdf'  => $this->extractFromPdf($filePath),
            'docx' => $this->extractFromDocx($filePath),
            'txt'  => $this->extractFromTxt($filePath),
            default => throw new \InvalidArgumentException("Unsupported file type: {$fileType}"),
        };
    }

  
    private function extractFromPdf(string $filePath): string
    {
        // Strategy 1: CLI pdftotext (preferred — handles complex layouts)
        $pdftotextPath = '/usr/bin/pdftotext';
        if (file_exists($pdftotextPath)) {
            $escaped = escapeshellarg($filePath);
            $output  = shell_exec("{$pdftotextPath} -layout {$escaped} - 2>/dev/null");
            Log::info('pdftotext execution', ['path' => $pdftotextPath, 'length' => strlen($output ?? ''), 'file' => $filePath]);
            if (!empty(trim($output ?? ''))) {
                return $this->cleanText($output);
            }
        }

        // Strategy 2: PHP library fallback
        if (class_exists(\Smalot\PdfParser\Parser::class)) {
            try {
                $parser   = new \Smalot\PdfParser\Parser();
                $pdf      = $parser->parseFile($filePath);
                $text     = $pdf->getText();
                if (!empty(trim($text))) {
                    return $this->cleanText($text);
                }
            } catch (\Exception $e) {
                Log::warning('PDF parser library failed', ['error' => $e->getMessage()]);
            }
        }

        throw new \RuntimeException('Cannot extract text from PDF — ensure poppler-utils or smalot/pdfparser is installed.');
    }

    private function extractFromDocx(string $filePath): string
    {
        if (!class_exists(\PhpOffice\PhpWord\IOFactory::class)) {
            return $this->extractDocxManually($filePath);
        }

        $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
        $text    = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                    foreach ($element->getElements() as $textEl) {
                        if (method_exists($textEl, 'getText')) {
                            $text .= $textEl->getText() . ' ';
                        }
                    }
                    $text .= "\n";
                }
            }
        }

        return $this->cleanText($text);
    }

  
    private function extractDocxManually(string $filePath): string
    {
        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new \RuntimeException('Cannot open DOCX file as ZIP archive.');
        }

        $xml  = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($xml === false) {
            throw new \RuntimeException('word/document.xml not found in DOCX archive.');
        }

        $text = strip_tags(str_replace(['</w:p>', '</w:tr>'], "\n", $xml));
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        return $this->cleanText($text);
    }

    private function extractFromTxt(string $filePath): string
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("File not found: {$filePath}");
        }

        return $this->cleanText(file_get_contents($filePath));
    }

   
    private function cleanText(string $text): string
    {
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text);

        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        $lines = array_map('trim', explode("\n", $text));
        $text  = implode("\n", $lines);

        return trim($text);
    }

    private function commandExists(string $command): bool
    {
        return !empty(shell_exec("which {$command} 2>/dev/null"));
    }
}
