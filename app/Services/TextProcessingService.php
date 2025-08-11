<?php

namespace App\Services;

class TextProcessingService
{
    
    public function textProcessing ($text) {

        $text = preg_replace('/<[^>]*>/', '', $text);

        $text = preg_replace('/&nbsp;/', ' ', $text);

        $text = trim($text);

        $text = preg_replace('/(\r?\n){3,}/m', "\n\n", $text);

        $text = preg_replace('/^[ \t]+/m', '', $text);

        $text = preg_replace('/[ \t]{2,}/', ' ', $text);

        $text = rtrim($text, " \t\n\r\0\x0B");

        $text = preg_replace('/<\s*(br|p)\s*\/?>\s*<\s*\/?\s*\1\s*>/i', '', $text);

        
        return $text;
    }

}