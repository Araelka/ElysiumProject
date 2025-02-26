<?php

namespace App\Http\Controllers;

use App\Events\PostCreated;
use App\Models\Post;
use App\Models\Theme;
use Illuminate\Http\Request;


class PostController extends Controller
{
    
    public function index(Request $request){

        $themes = Theme::all();

        $selectedThemeId = $request->query('theme_id');

        $posts = $this->selectPostsByTheme($selectedThemeId);

        return view('frontend/index', ['themes' => $themes, 'posts' => $posts, 'selectedThemeId' => $selectedThemeId]);
    }

    private function selectPostsByTheme ($selectedThemeId) {
        $posts = Post::where('theme_id', $selectedThemeId)->with(relations:['theme', 'user'])->get();

        return $posts;
    }



    public function store (Request $request){
        $validated = $request->validate([
            'post_text' => ['required', 'string'],
        ]);


        $post = new Post();
        $post->content = $validated['post_text'];
        $post->user_id = auth()->id();
        $post->theme_id = $request->input('theme_id');
        $post->save();

        return redirect()->back();
    }

    public function destroy ($id){

        $post = Post::findOrFail($id);

        if (auth()->id() == $post->user_id || auth()->user()->isAdmin()) {
            $post->delete();
            return redirect()->back();
        }
    }

    public function showEditForm ($id){
        $post = Post::findOrFail($id);

        if (auth()->id() != $post->user_id) {
            return redirect()->route('homePage', ['theme_id' => $post->theme_id]);
        }

        $themes = Theme::all();

        $selectedThemeId = $post->theme->id;

        $posts = $this->selectPostsByTheme($selectedThemeId);

        return view('frontend/index', ['themes' => $themes, 'posts' => $posts, 'selectedThemeId' => $selectedThemeId, 'postContent' => $post]);
    }

    public function edit(Request $request) {
        $validated = $request->validate(
            ['post_text' =>  ['required', 'string']
        ]);

        if (auth()->id() == $request->input('user_id')){
            $post = Post::findOrFail($request->input('post_id'));
            $post->content = $validated['post_text'];
            $post->update();
        }

        return redirect()->route('homePage', ['theme_id' => $request->input('theme_id')]);
    }
}
