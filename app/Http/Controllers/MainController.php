<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Theme;
use Illuminate\Http\Request;

class MainController extends Controller
{
    //
    public function index(Request $request){

        $themes = Theme::all();

        $selectedThemeId = $request->query('theme_id');


        $posts = Post::where('theme_id', $selectedThemeId)->with(relations:['theme', 'user'])->get();

        
        // $posts = Post::with(relations: ['theme', 'user'])
        // ->when($selectedThemeId, function ($query, $themeId) {
        //     $query->where('theme_id', $themeId); 
        // })
        // ->get();

        return view('frontend/index', ['themes' => $themes, 'posts' => $posts, 'selectedThemeId' => $selectedThemeId]);
    }
}
