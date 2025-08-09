<?php

namespace App\Http\Controllers\GameRoom;

use App\Events\PostEvent;
use App\Http\Controllers\Controller;
use App\Events\PostCreated;
use App\Models\Character;
use App\Models\Location;
use App\Models\Post;
use App\Models\Theme;
use App\Services\TextProcessingService;
use Illuminate\Http\Request;
use App\Http\Controllers\ThemeController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
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

        try {

        $validated = $request->validate([
            'post_text' => ['required', 'string'],
            'parent_post_id' => ['nullable', 'exists:posts,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'character_uuid' => ['required', 'exists:characters,uuid']
        ]);
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->has('parent_post_id')) {
                $parentPost = Post::find($request->input('parent_post_id'));
                if ($parentPost) {
                    $request->session()->flash('parent_post', [
                        'id' => $parentPost->id,
                        'character_name' => $parentPost->character->firstName . ' ' . $parentPost->character->secondName,
                        'content' => Str::limit($parentPost->content, 100),
                    ]);
                }
            }
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        }


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

        event(new PostEvent('create', $this->renderPost($post)));

         return response()->json([
            'html' => $this->renderPost($post)
        ]);


        // return redirect()->route('gameroom.index', compact('location_id'));
    }

    public function renderPost($post){
        return View::make('frontend.gameroom.post', ['post' => $post])->render();
    }



    public function destroy ($id){

        Log::info('Пост для удаления : ' . $id);

        if (auth()->user()->id != Post::findOrFail($id)->character()->first()->user_id && !auth()->user()->isEditor()) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $post = Post::findOrFail($id);

         

        $post->delete();

        return response()->json(['success' => true, 'post_id' => $id]);
        // return redirect()->back();
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

    public function edit($id, Request $request) {
        
        if (auth()->user()->id != Character::where('uuid', $request->input('character_uuid'))->first()->user_id 
        || $request->input('character_uuid') != Post::findOrFail($request->input('post_id'))->character()->first()->uuid){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $validated = $request->validate(
            ['post_text' =>  ['required', 'string']
        ]);

        $post = Post::findOrFail($request->input('post_id'));
        
        $post->update([
            'content' => $this->textProcessingService->textProcessing($validated['post_text'])
        ]);

        return response()->json([
            'html' => $this->renderPost($post),
            'post_id' => $post->id,
        ]);

        // return redirect()->route('gameroom.index', compact('location_id'));
    }

    public function getPostContent($id){
        $post = Post::find($id);

        if ($post) {
            return response()->json([
                'id' => $post->id,
                'character_name' => $post->character->firstName . ' ' . $post->character->secondName,
                'character_uuid' => $post->character->uuid,
                'content' => Str::limit($post->content, 100), 
            ]);
        }

        return response()->json(['error' => 'Сообщение не найдено'], 404);
    }

}
