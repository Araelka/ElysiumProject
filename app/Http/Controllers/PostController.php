<?php

namespace App\Http\Controllers;

use App\Events\PostCreated;
use App\Models\Location;
use App\Models\Post;
use App\Models\Theme;
use Illuminate\Http\Request;
use App\Http\Controllers\ThemeController;


class PostController extends Controller
{
    
    public function index(Request $request){

        $locations = Location::all();

        $selectedLocationId = $request->query('location_id');

        $selectedLocation = Location::find($selectedLocationId);

        $posts = $this->selectPostsByLocation($selectedLocationId);

        return view('frontend/index', ['locations' => $locations, 'posts' => $posts, 'selectedLocation' => $selectedLocation]);
    }

    private function selectPostsByLocation ($selectedLocationId) {
        $posts = Post::where('location_id', $selectedLocationId)->with(relations:['locations', 'user'])->get();

        return $posts;
    }



    public function store (Request $request){
        $validated = $request->validate([
            'post_text' => ['required', 'string'],
        ]);


        $post = new Post();
        $post->content = $validated['post_text'];
        $post->user_id = auth()->id();
        $post->location_id = $request->input('location_id');
        $post->save();

        return redirect()->back();
    }

    public function destroy ($id){

        $post = Post::findOrFail($id);

        if (auth()->id() == $post->user_id || auth()->user()->isAdmin()) {
            $post->delete();
        }

        return redirect()->back();
    }

    public function showEditForm ($id){
        $post = Post::findOrFail($id);

        if (auth()->id() != $post->user_id) {
            return redirect()->route('homePage', ['location_id' => $post->location_id]);
        }

        $locations = Location::all();

        $selectedLocationId = $post->location_id;

        $selectedLocation = Location::find($selectedLocationId);

        $posts = $this->selectPostsByLocation($selectedLocationId);

        return view('frontend/index', ['locations' => $locations, 'posts' => $posts, 'selectedLocation' => $selectedLocation, 'postContent' => $post]);
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

        return redirect()->route('homePage', ['location_id' => $request->input('location_id')]);
    }
}
