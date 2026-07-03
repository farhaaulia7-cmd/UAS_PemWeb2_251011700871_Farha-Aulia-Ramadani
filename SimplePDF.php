<?php
/**
 * SimplePDF
 * -----------------------------------------------------------------
 * Generator PDF sederhana murni PHP (tanpa library eksternal / composer)
 * dibuat khusus untuk kebutuhan fitur "Report PDF" pada aplikasi ini.
 * Mendukung: teks, garis, dan halaman otomatis (multi-page) untuk tabel.
 * -----------------------------------------------------------------
 */
class SimplePDF
{
    private array $pages = [];
    private string $currentContent = '';
    private float $pageWidth;
    private float $pageHeight;
    private float $y;
    private float $marginBottom = 50;

    public function __construct(string $orientation = 'L')
    {
        if ($orientation === 'L') {
            $this->pageWidth  = 841.89; // A4 landscape (pt)
            $this->pageHeight = 595.28;
        } else {
            $this->pageWidth  = 595.28; // A4 portrait (pt)
            $this->pageHeight = 841.89;
        }
        $this->y = $this->pageHeight - 40;
    }

    private function escape(string $s): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $s);
    }

    /** Tulis teks pada posisi x,y (dari kiri-bawah halaman) */
    public function text(float $x, float $y, string $str, int $size = 10, bool $bold = false): void
    {
        $font = $bold ? 'F2' : 'F1';
        $str = $this->escape($str);
        $this->currentContent .= "BT /$font $size Tf $x $y Td ($str) Tj ET\n";
    }

    /** Gambar garis horizontal/vertikal */
    public function line(float $x1, float $y1, float $x2, float $y2): void
    {
        $this->currentContent .= "0.6 w $x1 $y1 m $x2 $y2 l S\n";
    }

    /** Pindah ke halaman baru jika sudah mendekati batas bawah */
    private function ensureSpace(): void
    {
        if ($this->y < $this->marginBottom) {
            $this->pages[] = $this->currentContent;
            $this->currentContent = '';
            $this->y = $this->pageHeight - 40;
        }
    }

    /** Judul laporan */
    public function title(string $text): void
    {
        $this->text(30, $this->y, $text, 16, true);
        $this->y -= 10;
        $this->line(30, $this->y, $this->pageWidth - 30, $this->y);
        $this->y -= 20;
    }

    public function subtitle(string $text): void
    {
        $this->text(30, $this->y, $text, 10);
        $this->y -= 20;
    }

    /** Cetak satu baris tabel (array kolom + array lebar kolom) */
    public function row(array $cells, array $colWidths, bool $bold = false, int $size = 9): void
    {
        $this->ensureSpace();
        $x = 30;
        foreach ($cells as $i => $cell) {
            $this->text($x, $this->y, (string)$cell, $size, $bold);
            $x += $colWidths[$i] ?? 80;
        }
        $this->y -= 18;
    }

    public function tableHeaderLine(): void
    {
        $this->line(30, $this->y + 5, $this->pageWidth - 30, $this->y + 5);
    }

    /** Render & keluarkan file PDF ke browser */
    public function output(string $filename = 'report.pdf'): void
    {
        $this->pages[] = $this->currentContent;

        // Object 1: Catalog, Object 2: Pages, Object 3/4: Fonts
        $objects = [];
        $objects[] = "<< /Type /Catalog /Pages 2 0 R >>";                                  // obj 1
        $objects[] = null;                                                                 // obj 2 - Pages (isi belakangan)
        $objects[] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";              // obj 3 - font normal
        $objects[] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>";         // obj 4 - font bold

        $pageRefs = [];
        foreach ($this->pages as $content) {
            $pageObjNum = count($objects) + 1;
            $contentObjNum = $pageObjNum + 1;

            $objects[] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 {$this->pageWidth} {$this->pageHeight}] "
                . "/Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents $contentObjNum 0 R >>";
            $len = strlen($content);
            $objects[] = "<< /Length $len >>\nstream\n$content\nendstream";

            $pageRefs[] = $pageObjNum;
        }

        $kids = implode(' ', array_map(fn($n) => "$n 0 R", $pageRefs));
        $objects[1] = "<< /Type /Pages /Kids [$kids] /Count " . count($pageRefs) . " >>";

        // Susun file PDF
        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $i => $obj) {
            $offsets[$i + 1] = strlen($pdf);
            $pdf .= ($i + 1) . " 0 obj\n$obj\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $count = count($objects) + 1;
        $pdf .= "xref\n0 $count\n0000000000 65535 f \n";
        for ($i = 1; $i < $count; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer\n<< /Size $count /Root 1 0 R >>\nstartxref\n$xrefOffset\n%%EOF";

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }
}
