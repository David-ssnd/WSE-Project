<?php

namespace App\View;

use InvalidArgumentException;

class HtmlView{
    private $templatesDir;

    public function __construct(){
        $basePath = dirname(__DIR__); //cesta k súboru -> je to v constructe takže to je pre všetky objekty
        $this->templatesDir = $basePath . "/Templates"; 

        if(!is_dir($this->templatesDir)) throw new InvalidArgumentException("Directory not found" . $this->templatesDir);
    }

    public function render(string $templateFilename, int $responseCode = 200): void{
        $filePath = $this->templatesDir . "/" . ltrim($templateFilename, "/");

        if(!file_exists($filePath) || !is_readable($filePath)) throw new InvalidArgumentException("Template file not found or is not readable: " . $templateFilename . " (Resolved path: " . $filePath . ")");

        http_response_code($responseCode);
        header('Content-Type: text/html; charset=UTF-8');
        readfile($filePath);
    }
}