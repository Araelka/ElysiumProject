<?php

namespace App\Http\Controllers\GameRoom;

use App\Http\Controllers\Controller;
use App\Events\PostCreated;
use App\Models\Character;
use App\Models\Location;
use App\Models\Post;
use App\Models\Theme;
use App\Services\TextProcessingService;
use Illuminate\Http\Request;
use App\Http\Controllers\ThemeController;
use Str;


class PostController extends Controller
{

    protected $textProcessingService;

    public function __construct(TextProcessingService $textProcessingService) {
        $this->textProcessingService = $textProcessingService;
    }
    
    public function index(Request $request){

        if (!auth()->user()->isPlayer()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $locations = Location::all();

        $selectedLocationId = $request->query('location_id');

        $selectedLocation = Location::find($selectedLocationId);

        $posts = $this->selectPostsByLocation($selectedLocationId);

        $characters = Character::where('user_id', auth()->user()->id)->where('status_id', 3)->get();

        $parentPost = Post::find($request->get('parent_post_id'));
        
        return view('frontend/gameroom/index', compact('locations', 'selectedLocation', 'posts', 'characters', 'parentPost'));
    }

    private function selectPostsByLocation ($selectedLocationId) {
        $posts = Post::with('parent')->where('location_id', $selectedLocationId)->get();

        return $posts;
    }



    public function store (Request $request){
        
        if (!auth()->user()->isPlayer()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $validated = $request->validate([
            'post_text' => ['required', 'string'],
            'parent_post_id' => 'nullable|exists:posts,id',
            'location_id' => 'required|exists:locations,id',
            'character_uuid' => ['required']
        ]);


        if (auth()->user()->id != Character::where('uuid', $request->input('character_uuid'))->first()->user_id){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }


        $characterId = Character::where('uuid', $validated['character_uuid'])->first()->id;

        $post = Post::create([
            'content' => $this->textProcessingService->textProcessing($validated['post_text']),
            'character_id' => $characterId,
            'location_id' => $validated['location_id']
        ]);

        if ($validated['parent_post_id']) {
            $post->update([
                'parent_post_id' => $validated['parent_post_id']
            ]); 
        }

        $location_id = $validated['location_id'];

        return redirect()->route('gameroom.index', compact('location_id'));
    }

    public function destroy ($id){

        if (auth()->user()->id != Post::findOrFail($id)->character()->first()->user_id && !auth()->user()->isEditor()) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $post = Post::findOrFail($id);

        $post->delete();

        return redirect()->back();
    }

    public function showEditForm ($id){

        if (auth()->user()->id != Post::findOrFail($id)->character()->first()->user_id) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $postContent = Post::findOrFail($id);

        $locations = Location::all();

        $selectedLocationId = $postContent->location_id;

        $selectedLocation = Location::find($selectedLocationId);

        $posts = $this->selectPostsByLocation($selectedLocationId);

        $parentPost = $postContent;

        return view('frontend/gameroom/index', compact('locations', 'posts', 'selectedLocation', 'postContent', 'parentPost'));
    }

    public function edit(Request $request) {

        if (auth()->user()->id != Character::where('uuid', $request->input('character_uuid'))->first()->user_id 
        || $request->input('character_uuid') != Post::findOrFail($request->input('post_id'))->character()->first()->uuid){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $validated = $request->validate(
            ['post_text' =>  ['required', 'string']
        ]);

        $post = Post::findOrFail($request->input('post_id'))->update([
            'content' => $this->textProcessingService->textProcessing($validated['post_text'])
        ]);

        $location_id = $request->input('location_id');

        return redirect()->route('gameroom.index', compact('location_id'));
    }

    public function getPostContent($id){
        $post = Post::find($id);

        if ($post) {
            return response()->json([
                'character_name' => $post->character->firstName . ' ' . $post->character->secondName,
                'content' => Str::limit($post->content, 100), 
            ]);
        }

        return response()->json(['error' => 'Сообщение не найдено'], 404);
    }

}
