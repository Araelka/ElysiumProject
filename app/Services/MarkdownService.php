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
            static $idCounter = 0; 

            $altText = $matches[1];
            $imageSrc = $matches[2];
            $params = $matches[3];

            $uniqueId = 'img-' . $idCounter++;
            $this->tempParams[$uniqueId] = [
                'src' => $imageSrc,
                'params' => $params,
            ];

            $imagesWithId = $imageSrc . '?id=' . $uniqueId;

            return "![{$altText}]({$imagesWithId})";
        }, $markdown);

        $content = preg_replace('/<br>/m', "\n&nbsp;\n", $content);

        $content = $this->parsedown->text($content);

        $content = preg_replace_callback('/<img\s+src="([^"]+)"\s+alt="([^"]+)"\s*\/?>/', function ($matches) {
            $fullSrc = htmlspecialchars_decode($matches[1], ENT_QUOTES);
            $altText = htmlspecialchars_decode($matches[2], ENT_QUOTES);

            $query = parse_url($fullSrc, PHP_URL_QUERY);
            parse_str($query, $queryParams);
            $uniqueId = $queryParams['id'] ?? null;

            if (!$uniqueId || !isset($this->tempParams[$uniqueId])) {
                return "<img src=\"{$fullSrc}\" alt=\"{$altText}\">";
            }

            $params = $this->tempParams[$uniqueId]['params'] ?? '';
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
                    $padding = '';
                    if ($value === 'left') {
                        $padding = 'padding-right: 10px';
                    }
                    else if ($value === 'right') {
                        $padding = 'padding-left: 10px';
                    }
                    $attrString .= " style=\"float: $value; $padding\"";
                } else {
                    $attrString .= " $key=\"$value\"";
                }
            }

            return "<img src=\"{$fullSrc}\" alt=\"{$altText}\"$attrString>";
        }, $content);
   

        return $content;
    }
}