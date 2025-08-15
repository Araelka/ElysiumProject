<?php

namespace App\Http\Controllers\GameRoom;

use App\Events\PostEvent;
use App\Events\PostReadEvent;
use App\Http\Controllers\Controller;
use App\Events\PostCreated;
use App\Models\Character;
use App\Models\Location;
use App\Models\Post;
use App\Models\PostRead;
use App\Models\Theme;
use App\Services\TextProcessingService;
use Illuminate\Container\Attributes\Auth;
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

        $locationIds = $locations->pluck('id')->toArray();
        $unreadCounts = $this->getUnreadCountsForLocations($locationIds, auth()->user()->id);

        $selectedLocationId = $request->query('location_id');

        $selectedLocation = Location::find($selectedLocationId);
        
        $characters = Character::where('user_id', auth()->user()->id)->where('status_id', 3)->get();
        
        return view('frontend/gameroom/index', compact('locations', 'selectedLocation', 'characters', 'unreadCounts'));
    }

    public function loadPosts(Request $request){
        if (!auth()->user()->isPlayer()) {
            return response()->json(['error' => 'У вас нет прав на совершение данного действия'], 403);
        }

        $selectedLocationId = $request->query('location_id');
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 20);
         $searchQuery = $request->query('search');

        if (!$selectedLocationId) {
            return response()->json(['error' => 'Локация не выбрана'], 400);
        }

        $postsQuery = Post::where('location_id', $selectedLocationId);

        if (!empty($searchQuery)) {
            $postsQuery->where(function($searchQueryClosure) use ($searchQuery) {
                $searchQueryClosure->where('content', 'like', '%' . $searchQuery . '%')
                    ->orWhereHas('character', function($characterQuery) use ($searchQuery) {
                        $characterQuery->whereRaw('CONCAT(LOWER(firstName), \' \', LOWER(secondName)) LIKE ?', ['%' . mb_strtolower($searchQuery) . '%']);
                    });
            });
        }

        $postIdsForPage = (clone $postsQuery)->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->pluck('id'); 


        $readPostIds = PostRead::where('user_id', auth()->user()->id)
            ->whereIn('post_id', $postIdsForPage)
            ->pluck('post_id')
            ->toArray();

        $readByOthersMap = [];
        if ($postIdsForPage->isNotEmpty()) {
            $readByOthersPostIds = PostRead::whereIn('post_id', $postIdsForPage)
                ->where('user_id', '!=', auth()->user()->id) 
                ->select('post_id')
                ->distinct()
                ->pluck('post_id');
            
            foreach ($readByOthersPostIds as $postId) {
                $readByOthersMap[$postId] = true;
            }
        }

        $posts = $postsQuery->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        $firstUnreadPostId = null;
        if ($page == 1 && !$searchQuery) {
            $postsCollection = $posts->getCollection();
            for ($i = $postsCollection->count() - 1; $i >= 0; $i--) {
                $post = $postsCollection[$i];
                if (!in_array($post->id, $readPostIds)) {
                    $firstUnreadPostId = $post->id;
                    break;
                }
            }
        }

        $postData = $posts->map(function ($post) use($readPostIds, $readByOthersMap) {
            return [
                'id' => $post->id,
                'location_id' => $post->location_id,
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
                'created_at' => $post->created_at->isoFormat('HH:mm DD.MM.YYYY'),
                'updated_at' => $post->updated_at->isoFormat('HH:mm DD.MM.YYYY'),
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
                'isDeletable' => auth()->check() && auth()->user()->id === $post->character->user_id,
                'isModerator' => auth()->user()->isModerator(),
                'diffInHours' => $this->diffInHours($post),
                'isRead' => in_array($post->id, $readPostIds),
                'isReadByOthers' => isset($readByOthersMap[$post->id]),
            ];
        });

        return response()->json([
            'posts' => $postData,
            'hasMore' => $posts->hasMorePages(),
            'currentPage' => $posts->currentPage(),
            'searchQuery' => $searchQuery ?? null,
            'firstUnreadPostId' => $firstUnreadPostId,
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

        $validated = $request->validate([
            'post_text' => ['required', 'string'],
            'parent_post_id' => ['nullable', 'exists:posts,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'character_uuid' => ['required', 'exists:characters,uuid']
        ]);

        if (auth()->user()->id != Character::where('uuid', $request->input('character_uuid'))->first()->user_id){
            return response()->json(['error' => 'У вас нет прав на совершение данного действия'], 403);
        }

        
        if (!Character::where('uuid', $validated['character_uuid'])->first()->isApproved()){
            return response()->json(['error' => 'У вас нет прав на совершение данного действия'], 403);
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

        PostRead::firstOrCreate([
            'user_id' => auth()->user()->id, 
            'post_id' => $post->id
        ]);

        event(new PostReadEvent($post->id, auth()->user()->id));

        $postData = [
            'id' => $post->id,
            'location_id' => $post->location_id,
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
            'created_at' => $post->created_at->isoFormat('HH:mm DD.MM.YYYY'),
            'updated_at' => $post->updated_at->isoFormat('HH:mm DD.MM.YYYY'),
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
            return response()->json(['error' => 'У вас нет прав на совершение данного действия'], 403);
        }

        $post = Post::findOrFail($id);

        $locationId = $post->location_id;

        $replay = $post->replies()->get();

        $post->delete();

        $postData = [
            'id' => $post->id,
            'location_id' => $locationId,
            'replay' => $replay
        ];

        event(new PostEvent('delete', $postData));

        return response()->json(['success' => 200]);
    }


    public function edit($id, Request $request) {
        
        if (auth()->user()->id != Character::where('uuid', $request->input('character_uuid'))->first()->user_id 
        || $request->input('character_uuid') != Post::findOrFail($request->input('post_id'))->character()->first()->uuid){
            return response()->json(['error' => 'У вас нет прав на совершение данного действия'], 403);
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
            'updated_at' => $post->updated_at->isoFormat('HH:mm DD.MM.YYYY')
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

    public function markAsRead($id){
        if (!auth()->user()->isPlayer()) {
            return response()->json(['error' => 'У вас нет прав на совершение данного действия'], 403);
        }

        $userId = auth()->user()->id;
        $postId = $id;

        $post = Post::find($postId);
        if (!$post) {
            return response()->json(['error' => 'Пост не найден'], 404);
        }

        $postRead = PostRead::firstOrCreate(
            ['user_id' => $userId, 'post_id' => $postId]
        );

        if ($postRead->wasRecentlyCreated) {

            event(new PostReadEvent($postId, $userId));
        }

        return response()->json(['success' => true]);
    }

    private function getUnreadCountsForLocations(array $locationIds, $userId): array{
        

        if (empty($locationIds) || !$userId) {
            return [];
        }

        $unreadCounts = Post::whereIn('location_id', $locationIds)
            ->leftJoin('post_reads as pr', function ($join) use ($userId) {
                $join->on('posts.id', '=', 'pr.post_id')
                    ->where('pr.user_id', $userId);
            })
            ->whereNull('pr.post_id') 
            ->selectRaw('location_id, count(*) as unread_count')
            ->groupBy('location_id')
            ->pluck('unread_count', 'location_id')
            ->toArray();
        $result = [];
        foreach ($locationIds as $locId) {
            $result[$locId] = $unreadCounts[$locId] ?? 0;
        }

        return $result;
    }

    public function getUnreadCounts(Request $request){
        if (!auth()->user()->isPlayer()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $userId = auth()->user()->id;

        $locationIds = $request->input('location_ids', []);
        
        if (empty($locationIds)) {
            return response()->json(['counts' => []]);
        }

        $counts = $this->getUnreadCountsForLocations($locationIds, $userId);

        return response()->json(['counts' => $counts]);
    }

}
