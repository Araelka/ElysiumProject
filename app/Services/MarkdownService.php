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


        $content = preg_replace('/<br>/m', "\n&nbsp;\n", $content);

        // dd($this->parsedown->text($content));

        return $this->parsedown->text($content);
    }
}