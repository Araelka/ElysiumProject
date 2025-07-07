<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\MarkdownService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    protected $markdownService;

    public function __construct(MarkdownService $markdownService) {
        $this->markdownService = $markdownService;
    }

    public function index($id)  {
        $article = Article::with('images')->findOrFail($id);

        $article->content_html = $this->markdownService->convertToHtml($article->content);

        return view('frontend.wiki.showArticle', compact('article'));
    }

    public function showEditTitleForm($id){
        if (!auth()->user()->isEditor()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }
        $article = Article::with('images')->findOrFail($id);

        return view('frontend.wiki.editArticleTitle', compact('article'));
    }

    public function showEditArticleContent($id) {
        if (!auth()->user()->isEditor()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $article = Article::findOrFail($id);
        return view('frontend.wiki.editArticle', compact('article'));
    }

    public function editArticleContent(Request $request, $id){
        if (!auth()->user()->isEditor()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $text = $request->input('content');

        $text = preg_replace('/(\r?\n){3,}/m', "\n\n", $text);

        $text = preg_replace('/^[ \t]+(?=\S)/m', '', $text);
        
        $text = preg_replace('/(?:\s*<(br|p)\s*\/?>\s*|\s+|)+$/i', '', $text);

        $text = rtrim($text, " \t\n\r\0\x0B");
        
        // $text = preg_replace('/^\s+|\s+\r\n$/um', '', $text);

        $text = rtrim($text, " \t\n\r\0\x0B"); 

        $article = Article::findOrFail($id);

        $article->update([
            'content' => $text
        ]);

        return redirect()->route('wiki.article.index', $article->id);
    }
}
