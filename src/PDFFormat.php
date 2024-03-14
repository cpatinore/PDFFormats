<?php

namespace ExcelFormats;

use PDFFormat\Interfaces\FilePDF;

class PDFFormat
{
    public FilePDF $filePDF;

    function __construct(FilePDF $filePDF)
    {
        $this->filePDF = $filePDF;
    }

    public function save(string $path)
    {
        return $this->filePDF->Output($path, 'I');
    }
}