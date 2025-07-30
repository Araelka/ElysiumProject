<?php

namespace App\Http\Controllers;

use App\Http\Requests\CharacterDescriptionRequest;
use App\Http\Requests\CharacterRequest;
use App\Models\Attribute;
use App\Models\Character;
use App\Models\CharacterAttribute;
use App\Models\CharacterDescription;
use App\Models\CharacterImage;
use App\Models\CharacterSkill;
use App\Models\Skill;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CharacterController extends Controller
{

    protected function textProcessing ($text) {

        $text = preg_replace('/(\r?\n){3,}/m', "\n\n", $text);

        $text = preg_replace('/^[ \t]+/m', '', $text);

        $text = preg_replace('/[ \t]{2,}/', ' ', $text);

        $text = preg_replace('/\s*<(br|p)\s*\/?>\s*/i', '<$1>', $text);

        $text = rtrim($text, " \t\n\r\0\x0B");

        $text = preg_replace('/<\s*(br|p)\s*\/?>\s*<\s*\/?\s*\1\s*>/i', '', $text);
        
        return $text;

    }

    protected function diffInDays ($character) {

        if (!$character || !$character->updated_at) {
            return null;
        }

        $currentDate = Carbon::now();

        $characterUpdatedDate = Carbon::parse($character->updated_at);

        $diffInDays = $characterUpdatedDate->diffInDays($currentDate);

        return $diffInDays;
    }

    public function index(Request $request) {

        $selectedCharacterId = $request->query('character');

        $selectedCharacter = Character::where('uuid', $selectedCharacterId)->first();
        
        if ($selectedCharacter && $selectedCharacter->user_id != auth()->user()->id) {
            return redirect()->back()->withErrors('У вас нет прав на совершение данного действия');
        }

        $diffInDays = $this->diffInDays($selectedCharacter);

        $characters = Character::where('user_id', auth()->user()->id)->orderByRaw("FIELD(status_id, 3, 2, 1, 4, 5)")->get();

        return view('frontend.characters.index', compact('characters', 'selectedCharacter', 'diffInDays'));
    }

    public function showMainInfo($uuid = null){
        if($uuid != null){
            if (!Character::where('uuid', $uuid)->first()->isPreparing() && !Character::where('uuid', $uuid)->first()->isRejected()) {
                return redirect()->back()->withError('У вас нет прав на совершение данного действия');
            }
            
            $character = Character::where('uuid', $uuid)->first();
            
            if (auth()->user()->id != $character->user_id){
                return redirect()->back()->withErrors('У вас нет прав на совершение данного действия');
            }

            return view('frontend.characters.mainInfo', compact('character')); 
        };

        if (Character::where('user_id', auth()->user()->id)->where('status_id', '!=', '5')->count() == 5){
            return redirect()->back()->withErrors('Превышен предел персонажий');
        }

        return view('frontend.characters.mainInfo'); 
    }

    public function createMainInfo(CharacterRequest $request) {

        if (Character::where('user_id', auth()->user()->id)->where('status_id', '!=', '5')->count() == 5){
            return redirect()->back()->withErrors('Превышен предел персонажий');
        }

        $text = $this->textProcessing($request->input('personality'));

        $character = Character::create([
            'uuid' => Str::uuid(),
            'user_id' => auth()->user()->id,
            'firstName' => $request->input('firstName'),
            'secondName' => $request->input('secondName'),
            'age' => $request->input('age'),
            'gender' => $request->input('gender'),
            'height' => $request->input('height'),
            'weight' => $request->input('weight'),
            'nationality' => $request->input('nationality'),
            'residentialAddress' => $request->input('residentialAddress'),
            'activity' => $request->input('activity'),
            'personality' => $text,
            'comment' => null,
            'status_id' => 1
        ]);

        if ($request->hasFile('image')) {
            $folderPath = "images/characters/{$character->uuid}";
            $imagePath = $request->file('image')->store($folderPath, 'public');

            $file = $request->file('image');

            $fileContent = file_get_contents($file->getPathname());

            $hash = md5($fileContent);

            $image = CharacterImage::create([
                'character_id' => $character->id,
                'path' => $imagePath,
                'file_hash' => $hash
            ]);
        }
        
        return redirect()->route('characters.showCreateSkills', $character->uuid);
    }

    public function updateMainInfo($uuid, CharacterRequest $request){
        if (!Character::where('uuid', $uuid)->first()->isPreparing() && !Character::where('uuid', $uuid)->first()->isRejected()) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $character = Character::where('uuid', $uuid)->first();

        if (auth()->user()->id != $character->user_id){
            return redirect()->back()->withErrors('У вас нет прав на совершение данного действия');    
        }

        $text = $this->textProcessing($request->input('personality'));

        $character->update([
            'firstName' => $request->input('firstName'),
            'secondName' => $request->input('secondName'),
            'age' => $request->input('age'),
            'gender' => $request->input('gender'),
            'height' => $request->input('height'),
            'weight' => $request->input('weight'),
            'nationality' => $request->input('nationality'),
            'residentialAddress' => $request->input('residentialAddress'),
            'activity' => $request->input('activity'),
            'personality' => $text,
            'comment' => null,
            'status_id' => 1
        ]);



        if ($request->hasFile('image')) {
             
            if ($character->images->first()) {
                if (Storage::disk('public')->exists($character->images->first()->path)) {
                    Storage::disk('public')->delete($character->images->first()->path);
                }
            }

            $file = $request->file('image');

            $folderPath = "images/characters/{$character->uuid}";
            $imagePath = $request->file('image')->store($folderPath, 'public');

            $fileContent = file_get_contents($file->getPathname());

            $hash = md5($fileContent);

            if ($character->images->first()) {
                    $character->images->first()->update([
                    'path' => $imagePath,
                    'file_hash' => $hash
                ]);
            }
            else {
                CharacterImage::create([
                'character_id' => $character->id,
                'path' => $imagePath,
                'file_hash' => $hash
            ]);
            }
            
        }

        $characterId = $uuid;
        

        return redirect()->route('characters.showCreateSkills', $characterId);
    }

    public function showCreateSkills($uuid){   
        
        if (auth()->user()->id != Character::where('uuid', $uuid)->first()->user_id) {
            return redirect()->back()->withErrors('У вас нет прав на совершение данного действия');
        }

        if (!Character::where('uuid', $uuid)->first()->isPreparing() && !Character::where('uuid', $uuid)->first()->isRejected()) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $attributes = Attribute::with('skills')->get();
        $characterId = $uuid;

        if(Character::where('uuid', $uuid)->first()->attributes->first()){
            $characterId = Character::where('uuid', $uuid)->first()->attributes->first()->character_id;
            $characterAttributes = CharacterAttribute::where('character_id',  $characterId)->get();
            $characterId = $uuid;

            return view('frontend.characters.attributes', compact('attributes','characterAttributes', 'characterId')); 
        };

        return view('frontend.characters.attributes', compact('attributes', 'characterId'));
    }

    public function showUpdateSkills ($uuid) {

        if (auth()->user()->id != Character::where('uuid', $uuid)->first()->user_id) {
            return redirect()->back()->withErrors('У вас нет прав на совершение данного действия');
        }

        if (Character::where('uuid', $uuid)->first()->getAvailablePoints() <= 0 || !Character::where('uuid', $uuid)->first()->isApproved()) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $attributes = Attribute::with('skills')->get();

        $character = Character::where('uuid', $uuid)->first();
        $characterId = $character->id;
        $characterAttributes = $character->attributes;
        $characterId = $uuid;

        return view('frontend.characters.skills', compact('attributes', 'characterAttributes', 'character', 'characterId')); 


    }

    public function createSkills($uuid, Request $request){

        if (auth()->user()->id != Character::where('uuid', $uuid)->first()->user_id){
            return redirect()->back()->withErrors('У вас нет прав на совершение данного действия');    
        }

        $validated = $request->validate([
            'attributes' => ['required', 'array'],
            'attributes.*' => ['required' ,'integer', 'min:0', 'max:6'],
            'skills' => ['required', 'array'],
            'skills.*' => ['required', 'integer']
        ]);


        if (array_sum($validated['attributes']) > 6) {
            return redirect()->back()->withErrors('Превышен предел суммы количства очков');
        }

        if (array_sum($validated['skills']) != 0) {
            return redirect()->back()->withErrors('Превышен предел суммы количства очков');
        }

        $characterId = Character::where('uuid', $uuid)->first()->id;

        foreach ($validated['attributes'] as $id => $attribute) {
            $characterAttribute = CharacterAttribute::create([
                'character_id' => $characterId,
                'attribute_id' => $id,
                'points' => $attribute
            ]);
        }        

        foreach ($validated['skills'] as $id => $skill) {
            $characterSkill = CharacterSkill::create([
                'character_id' => $characterId,
                'skill_id' => $id
            ]);
        }


        return redirect()->route('characters.showCreateDescription', $uuid);;

    }
    
    public function updateAttributes($uuid, Request $request){

        if (auth()->user()->id != Character::where('uuid', $uuid)->first()->user_id){
            return redirect()->back()->withErrors('У вас нет прав на совершение данного действия');    
        }

        if (!Character::where('uuid', $uuid)->first()->isPreparing() && !Character::where('uuid', $uuid)->first()->isRejected()) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $validated = $request->validate([
            'attributes' => ['required', 'array'],
            'attributes.*' => ['required' ,'integer', 'min:0', 'max:6']
        ]);

        if (array_sum($validated['attributes']) > 6) {
            return redirect()->back()->withErrors('Превышен предел суммы количства очков');
        }
        
        $characterId = Character::where('uuid', $uuid)->first()->id;

        foreach ($validated['attributes'] as $id => $attribute) {
            $characterAttribute = CharacterAttribute::where('character_id',  $characterId)->where('attribute_id',  $id)->first();
            $characterAttribute->update([
                'points' => $attribute
            ]);
        } 

        $characterId = $uuid;

        return redirect()->route('characters.showCreateDescription', $characterId);
    }

    public function updateSkills ($uuid, Request $request){

        if (auth()->user()->id != Character::where('uuid', $uuid)->first()->user_id) {
            return redirect()->back()->withErrors('У вас нет прав на совершение данного действия');
        }

        if (Character::where('uuid', $uuid)->first()->getAvailablePoints() <= 0 || !Character::where('uuid', $uuid)->first()->isApproved()) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $validated = $request->validate([
            'skills' => ['required', 'array', 'max:1'],
            'skills.*' => ['required', 'integer', 'min:1', 'max:6']
        ]);

        $character = Character::where('uuid', $uuid)->first();

        foreach ($validated['skills'] as $id => $skill) {
            $selectedSkill = CharacterSkill::findOrFail($id);
            $skillPoints = intval($skill);
        }

        if ($character->id != $selectedSkill->character_id){
            return redirect()->back()->withErrors('У вас нет прав на совершение данного действия');
        }

        if ($skillPoints > $selectedSkill->getMaxPoints() || ($skillPoints - 1) != $selectedSkill->points) {
            return redirect()->back()->withErrors('Превышен максимум очком для данного навыка');
        }

        $selectedSkill->update([
            'points' => $skillPoints
        ]);

        $character->decreaseAvailablePoints();

        if ($character->getAvailablePoints() > 0) {
            return redirect()->back();
        }
        else {
            return redirect()->route('characters.index');
        }

    }

    public function showCreateDescription($uuid){

        if (auth()->user()->id != Character::where('uuid', $uuid)->first()->user_id) {
            return redirect()->back()->withErrors('У вас нет прав на совершение данного действия');
        }

        if (!Character::where('uuid', $uuid)->first()->isPreparing() && !Character::where('uuid', $uuid)->first()->isRejected()) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $characterId = Character::where('uuid', $uuid)->first()->id;

        if (CharacterDescription::where('character_id',  $characterId)->first()){
            $characterDescripron = CharacterDescription::where('character_id',  $characterId)->first();
            if  (auth()->user()->id != $characterDescripron->character->user_id){
                return redirect()->back()->withErrors('У вас нет прав на совершение данного действия');
            }

            $characterId = $uuid;

            return view('frontend.characters.description', compact( 'characterId', 'characterDescripron')); 
        }

        $characterId = $uuid;

        return view('frontend.characters.description', compact( 'characterId'));
    }

    public function createDescription($uuid, CharacterDescriptionRequest $request) {

        if (auth()->user()->id != Character::where('uuid', $uuid)->first()->user_id){
            return redirect()->back()->withErrors('У вас нет прав на совершение данного действия');    
        }

        if (!Character::where('uuid', $uuid)->first()->isPreparing() && !Character::where('uuid', $uuid)->first()->isRejected()) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $biography = $this->textProcessing($request->input(['biography']));
        $description = $this->textProcessing($request->input(['description']));
        $headcounts = $this->textProcessing($request->input(['headcounts']));

        $characterId = Character::where('uuid', $uuid)->first()->id;

        CharacterDescription::create([
            'character_id' => $characterId,
            'biography' => $biography,
            'description' => $description,
            'headcounts' => $headcounts
        ]);

        $character = Character::findOrFail($characterId);
        $character->update([
            'status_id' => 2
        ]);

        return redirect()->route('characters.index');
    }

    public function updateDescription($uuid, CharacterDescriptionRequest $request){

        if (auth()->user()->id != Character::where('uuid', $uuid)->first()->user_id){
            return redirect()->back()->withErrors('У вас нет прав на совершение данного действия');    
        }

        if (!Character::where('uuid', $uuid)->first()->isPreparing() && !Character::where('uuid', $uuid)->first()->isRejected()) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $biography = $this->textProcessing($request->input(['biography']));
        $description = $this->textProcessing($request->input(['description']));
        $headcounts = $this->textProcessing($request->input(['headcounts']));

        $character = Character::where('uuid', $uuid)->first();
        $characterId = $character->id;

        $characterDescription = CharacterDescription::where('character_id', $characterId)->first();
        
        $characterDescription->update([
            'biography' => $biography,
            'description' => $description,
            'headcounts' => $headcounts
        ]);


        $character->update([
            'status_id' => 2
        ]);


        return redirect()->route('characters.index');
    }

    public function characterDestoy ($uuid) {

        if (Character::where('uuid', $uuid)->first()->user_id != auth()->user()->id && !auth()->user()->isAdmin()) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        if (!auth()->user()->isAdmin() && !Character::where('uuid', $uuid)->first()->isPreparing() && !Character::where('uuid', $uuid)->first()->isRejected()) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $character = Character::where('uuid', $uuid)->first();

        if ($character->images->first()) {
            if (Storage::disk('public')->exists('images/characters/' . $character->uuid)) {
                Storage::disk('public')->deleteDirectory('images/characters/' . $character->uuid);
            }
        }

        $character->delete();

        return redirect()->back();
    }

    public function changeArchiveStatus ($uuid, Request $request){

        if (auth()->user()->id != Character::where('uuid', $uuid)->first()->user_id){
            return redirect()->back()->withErrors('У вас нет прав на совершение данного действия');    
        }

        if (!Character::where('uuid', $uuid)->first()->isApproved() && !Character::where('uuid', $uuid)->first()->isArchive() ) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $character = Character::where('uuid', $uuid)->first();

        $diffInDays = $this->diffInDays($character);

        if ($diffInDays < 14) {
            return redirect()->back()->withErrors('Смена статуса доступана раз в 2 недели');
        }

        if ($character->isApproved()){
            $character->update([
                'status_id' => 5
            ]);
        }
        else {
            $character->update([
                'status_id' => 3
            ]);
        }

        return redirect()->back()->with('Статус персонажа успешно изменён');

    }

}
