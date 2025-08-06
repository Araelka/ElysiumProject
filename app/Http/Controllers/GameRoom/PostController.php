<?php

namespace App\Http\Controllers\GameRoom;

use App\Http\Controllers\Controller;
use App\Events\PostCreated;
use App\Models\Character;
use App\Models\Location;
use App\Models\Post;
use App\Models\Theme;
use Illuminate\Http\Request;
use App\Http\Controllers\ThemeController;


class PostController extends Controller
{
    
    public function index(Request $request){

        if (!auth()->user()->isPlayer()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $locations = Location::all();

        $selectedLocationId = $request->query('location_id');

        $selectedLocation = Location::find($selectedLocationId);

        $posts = $this->selectPostsByLocation($selectedLocationId);

        $characters = Character::where('user_id', auth()->user()->id)->where('status_id', 3)->get();

        return view('frontend/gameroom/index', compact('locations', 'selectedLocation', 'posts', 'characters'));
    }

    private function selectPostsByLocation ($selectedLocationId) {
        $posts = Post::where('location_id', $selectedLocationId)->get();

        return $posts;
    }



    public function store (Request $request){
        
        if (!auth()->user()->isPlayer()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        
        $validated = $request->validate([
            'post_text' => ['required', 'string'],
            'character_uuid' => ['required']
        ]);

        if (auth()->user()->id != Character::where('uuid', $request->input('character_uuid'))->first()->user_id){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }


        $characterId = Character::where('uuid', $validated['character_uuid'])->first()->id;


        $post = Post::create([
            'content' => $validated['post_text'],
            'character_id' => $characterId,
            'location_id' => $request->input('location_id')
        ]);

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
