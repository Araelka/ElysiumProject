<?php

namespace App\Http\Controllers\Wiki;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleImage;
use App\Services\MarkdownService;
use Illuminate\Http\Request;
use Log;

class ArticleController extends Controller
{
    protected $markdownService;

    public function __construct(MarkdownService $markdownService) {
        $this->markdownService = $markdownService;
    }

    public function index($id)  {
        $article = Article::findOrFail($id);

        $article->content_html = $this->markdownService->convertToHtml($article->content);

        return view('frontend.wiki.showArticle', compact('article'));
    }

    public function showEditTitleForm($id){
        if (!auth()->user()->isEditor()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }
        $article = Article::findOrFail($id);

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
        
        $text = rtrim($text, " \t\n\r\0\x0B"); 

        $article = Article::findOrFail($id);

        $article->update([
            'content' => $text
        ]);

        return redirect()->route('wiki.article.index', $article->id);
    }
    
    public function uploadImage (Request $request, $id) {

        if (!auth()->user()->isEditor()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $validated = $request->validate([
            'image' => ['nullable', 'image', 'mimes:png,jpeg,jpg,webp', 'max:2048']
        ]);

        

        if ($request->hasFile('image')) {

            $articleName = Article::findOrFail($id)->theme->name;

            $file = $request->file('image');

            $fileHash = md5_file($file->getPathname());

            $existingFile = ArticleImage::where('article_id', $id)->where('file_hash', $fileHash )->first();


            if ($existingFile){
                $url = asset('storage/' . $existingFile->path);
                return response()->json(['url' => $url]);
            }

            $fileContent = file_get_contents($file->getPathname());

            $hash = md5($fileContent);

            $fileName = $hash . '.' . $file->getClientOriginalExtension();

            $folderPath = "images/wiki/{$articleName}";
            $imagePath = $file->store($folderPath, 'public');

            $image = ArticleImage::create([
                'article_id' => $id,
                'path' => $imagePath,
                'file_hash' => $hash
            ]);

            $url = asset('storage/' . $imagePath);

            return response()->json(['url' => $url]);
        }
    }
}
