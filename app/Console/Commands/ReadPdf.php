<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Smalot\PdfParser\Parser;

class ReadPdf extends Command
{
    protected $signature = 'pdf:read {path : Ruta absoluta o relativa al PDF}';
    protected $description = 'Lee un PDF y muestra su contenido como texto plano';

    public function handle(): int
    {
        $path = $this->argument('path');

        if (!is_string($path) || !file_exists($path)) {
            $this->error("Archivo no encontrado: {$path}");
            return self::FAILURE;
        }

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($path);
            $text = $pdf->getText();

            // Normalizar saltos y codificación
            $text = str_replace("\r\n", "\n", $text);

            $this->info("=== Contenido extraído de: {$path} ===");
            $this->line($text);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Error leyendo el PDF: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}