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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;



class PostController extends Controller
{

    protected $textProcessingService;

    public function __construct(TextProcessingService $textProcessingService) {
        $this->textProcessingService = $textProcessingService;
    }

    protected function diffInHours ($post) {

        if (!$post || !$post->created_at) {
            return null;
        }

        $currentDate = Carbon::now();

        $postUpdatedDate = Carbon::parse($post->created_at);

        $diffInHours = $postUpdatedDate->diffInHours($currentDate);

        return $diffInHours;
    }
    
    public function index(Request $request){

        if (!auth()->user()->isPlayer()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $locations = Location::all();

        $selectedLocationId = $request->query('location_id');

        $selectedLocation = Location::find($selectedLocationId);

        // $posts = $this->selectPostsByLocation($selectedLocationId);

        // $diffInHours = [];

        // foreach ($posts as  $post) {
        //     $diffInHours += [$post->id => $this->diffInHours($post)];
        // }
        
        $characters = Character::where('user_id', auth()->user()->id)->where('status_id', 3)->get();

        // $parentPost = Post::find($request->get('parent_post_id'));
        
        return view('frontend/gameroom/index', compact('locations', 'selectedLocation', 'characters'));
    }

    public function loadPosts(Request $request){
        if (!auth()->user()->isPlayer()) {
            return response()->json(['error' => 'У вас нет прав на совершение данного действия'], 403);
        }

        $selectedLocationId = $request->query('location_id');
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);

        if (!$selectedLocationId) {
            return response()->json(['error' => 'Локация не выбрана'], 400);
        }

        $posts = Post::where('location_id', $selectedLocationId)
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        $postData = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'content' => $post->content,
                'character' => [
                    'firstName' => $post->character->firstName,
                    'secondName' => $post->character->secondName,
                    'gender' => $post->character->gender,
                    'avatarPath' => $post->character->images->first()
                        ? 'storage/' . $post->character->images->first()->path
                        : null,
                    'userId' => $post->character->user_id,
                    'userLogin' => $post->character->user->login,
                ],
                'created_at' => $post->created_at->toIso8601String(),
                'updated_at' => $post->updated_at->toIso8601String(),
                'parentPost' => $post->parent_post_id
                    ? [
                        'id' => $post->parent->id,
                        'content' => Str::limit($post->parent->content, 100),
                        'character' => [
                            'firstName' => $post->parent->character->firstName,
                            'secondName' => $post->parent->character->secondName,
                        ],
                    ]
                    : null,
                'isEditable' => auth()->check() && auth()->user()->id === $post->character->user_id,
                'isDeletable' => auth()->check() && (auth()->user()->id === $post->character->user_id || auth()->user()->isModerator()),

            ];
        });

        $selectedLocation = Location::find($selectedLocationId);

        return response()->json([
            'posts' => $postData,
            'hasMore' => $posts->hasMorePages(),
            'currentPage' => $posts->currentPage(),
        ]);
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

        $text = $this->textProcessingService->textProcessing($validated['post_text']);

        if ($text == ''){
            return response()->json(['success' => false]);
        }

        $post = Post::create([
            'content' => $text,
            'character_id' => $characterId,
            'location_id' => $validated['location_id']
        ]);

        $postData = [
            'id' => $post->id,
            'content' => $post->content,
            'character' => [
                'firstName' => $post->character->firstName,
                'secondName' => $post->character->secondName,
                'gender' => $post->character->gender,
                'avatarPath' =>  $post->character->images->first()
                ? 'storage/' . $post->character->images->first()->path
                : null,
                'userId' => $post->character->user_id,
                'userLogin' => $post->character->user->login
            ],
            
            'created_at' => $post->created_at->toIso8601String(),
            'updated_at' => $post->updated_at->toIso8601String()
        ];

        if ($validated['parent_post_id']) {
            $post->update([
                'parent_post_id' => $validated['parent_post_id']
            ]); 

            $parentPost = Post::find($validated['parent_post_id']);

            $postData += [
                'parentPost' => [
                    'id' => $parentPost->id,
                    'content' =>  Str::limit($parentPost->content, 100),
                    'character' => [
                        'firstName' => $parentPost->character->firstName,
                        'secondName' => $parentPost->character->secondName,
                    ]
                ]
            ];
        }
    
        event(new PostEvent('create', $postData));

        return response()->json(['success' => 200]);
        
    }

    public function renderPost($post){
        return View::make('frontend.gameroom.post', ['post' => $post])->render();
    }



    public function destroy ($id){

        if (auth()->user()->id != Post::findOrFail($id)->character()->first()->user_id && !auth()->user()->isModerator()) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $post = Post::findOrFail($id);


        $replay = $post->replies()->get();

        $post->delete();

        $postData = [
            'id' => $post->id,
            'replay' => $replay
        ];

        event(new PostEvent('delete', $postData));

        return response()->json(['success' => 200]);
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

        $postData = [
            'id' => $post->id,
            'content' => $post->content,            
            'updated_at' => $post->updated_at->toIso8601String()
        ];

        event(new PostEvent('edit', $postData));

        return response()->json(['success' => 200]);

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

    public function getPermissions($id){
        $post = Post::findOrFail($id);

        return response()->json([
            'isEditable' => auth()->check() && auth()->user()->id === $post->character->user_id,
            'isDeletable' => auth()->check() && (auth()->user()->id === $post->character->user_id || auth()->user()->isModerator()),
        ]);
    }

}
