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

    /**
     * Extract text from PDF using multiple fallback strategies:
     * 1. pdftotext (best quality, requires poppler-utils on server)
     * 2. smalot/pdfparser PHP library (no system dependency)
     */
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

    /**
     * Extract text from DOCX using phpoffice/phpword.
     * DOCX is a ZIP archive containing XML — we parse the XML directly.
     */
    private function extractFromDocx(string $filePath): string
    {
        if (!class_exists(\PhpOffice\PhpWord\IOFactory::class)) {
            // Fallback: parse XML manually from ZIP
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

    /**
     * Manual DOCX extraction without PhpWord library.
     * DOCX files are ZIP archives — we extract word/document.xml directly.
     */
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

        // Strip XML tags and decode entities
        $text = strip_tags(str_replace(['</w:p>', '</w:tr>'], "\n", $xml));
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        return $this->cleanText($text);
    }

    /** Read plain text file directly */
    private function extractFromTxt(string $filePath): string
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("File not found: {$filePath}");
        }

        return $this->cleanText(file_get_contents($filePath));
    }

    /**
     * Normalize extracted text:
     * - Remove non-printable characters
     * - Collapse multiple blank lines
     * - Normalize whitespace
     */
    private function cleanText(string $text): string
    {
        // Remove null bytes and control characters (except newlines/tabs)
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text);

        // Collapse 3+ blank lines to 2
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        // Trim each line
        $lines = array_map('trim', explode("\n", $text));
        $text  = implode("\n", $lines);

        return trim($text);
    }

    /** Check if a CLI command exists on the system */
    private function commandExists(string $command): bool
    {
        return !empty(shell_exec("which {$command} 2>/dev/null"));
    }
}
