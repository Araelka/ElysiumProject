<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index($id)  {
        $article = Article::with('images')->findOrFail($id);

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

        $article = Article::findOrFail($id);

        $article->update([
            'content' => $request->input('content')
        ]);

        return redirect()->route('wiki.article.index', $article->id);
    }
}
