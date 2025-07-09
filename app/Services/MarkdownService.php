<?php

namespace App\Services;

use Parsedown;

class MarkdownService
{
    protected $parsedown;
    protected $tempParams = [];
    

    public function __construct()
    {
        $this->parsedown = new Parsedown();
        $this->parsedown->setSafeMode(true); 
    }

    public function convertToHtml($markdown)
    {
        $content = $markdown;

        $content = preg_replace_callback('/!\[(.*?)\]\((.*?)\)\{(.+?)\}/', function ($matches) {
            $altText = $matches[1];
            $imageSrc = $matches[2];
            $params = $matches[3];

            $this->tempParams[$imageSrc] = $params;

            return "![{$altText}]({$imageSrc})";
        }, $markdown);

        // dd($this->tempParams);

        $content = preg_replace('/<br>/m', "\n&nbsp;\n", $content);

        $content = $this->parsedown->text($content);

        $content = preg_replace_callback('/<img\s+src="([^"]+)"\s+alt="([^"]+)"\s*\/?>/', function ($matches) {
            $imageSrc = htmlspecialchars_decode($matches[1], ENT_QUOTES);
            $altText = htmlspecialchars_decode($matches[2], ENT_QUOTES);

            $params = $this->tempParams[$imageSrc] ?? '';
            $attributes = [];
            if ($params) {
                preg_match_all('/(\w+)=([\'"]?)([^\'"\s]+)\2/', $params, $paramMatches, PREG_SET_ORDER);
                foreach ($paramMatches as $param) {
                    $attributes[$param[1]] = $param[3];
                }
            }

            $attrString = '';
            foreach ($attributes as $key => $value) {
                if ($key === 'align') {
                    $attrString .= " style=\"float: $value;\"";
                } else {
                    $attrString .= " $key=\"$value\"";
                }
            }

            return "<img src=\"$imageSrc\" alt=\"$altText\"$attrString>";
        }, $content);

        return $content;
    }
}