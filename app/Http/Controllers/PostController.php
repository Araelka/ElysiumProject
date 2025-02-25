<?php

namespace App\Http\Controllers;

use App\Events\PostCreated;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
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

        $post = Post::findOrFail($id)->delete();

        return redirect()->back();
    }

    public function showEditForm ($id){
        $post = Post::findOrFail($id);
        
        return redirect()->back();
    }
}
