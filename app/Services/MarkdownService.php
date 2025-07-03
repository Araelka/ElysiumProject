<?php

namespace App\Services;

use Parsedown;

class MarkdownService
{
    protected $parsedown;

    public function __construct()
    {
        $this->parsedown = new Parsedown();
        $this->parsedown->setSafeMode(true); 
    }

    public function convertToHtml($markdown)
    {
        $content = $markdown;

        $content = preg_replace('/^\r\n/m', "  \n", $content);

        // dd($content);
        return $this->parsedown->text($content);
    }
}