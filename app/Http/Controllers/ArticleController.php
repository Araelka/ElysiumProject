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
}
