<?php

namespace App\Http\Controllers;

use App\Http\Requests\CharacterRequest;
use App\Models\Attribute;
use App\Models\Character;
use App\Models\CharacterAttribute;
use App\Models\CharacterDescription;
use App\Models\CharacterImage;
use App\Models\CharacterSkill;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CharacterController extends Controller
{

    public function index(Request $request) {
        // $character = Character::findOrFail(1);
        // $skills = Skill::findOrFail(3);
        // $characterA = CharacterAttribute::findOrFail(2);
        // $skill = CharacterSkill::findOrFail($skills->id)->where('character_id', $character->id)->first();
        // dd($character->status->name);

        // $characters = Character::where('user_id', '=', auth()->user()->id)->get();

        $selectedCharacterId = $request->query('character_id');

        $selectedCharacter = Character::find($selectedCharacterId);


        $characters = Character::where('user_id', auth()->user()->id)->get();

        return view('frontend.characters.index', compact('characters', 'selectedCharacter'));
    }

    public function showMainInfo($uuid = null){
        if($uuid != null){
            $character = Character::where('uuid', $uuid)->first();
            
            if (auth()->user()->id != $character->user_id){
                return redirect()->back()->withErrors('Недостаточно прав');
            }

            return view('frontend.characters.mainInfo', compact('character')); 
        };

        return view('frontend.characters.mainInfo'); 
    }

    public function createMainInfo(CharacterRequest $request) {

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
            'personality' => $request->input('personality'),
            'status_id' => 1
        ]);

        if ($request->hasFile('image')) {
            $folderPath = "images/character/{$character->uuid}";
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
        $character = Character::where('uuid', $uuid)->first();

        if (auth()->user()->id != $character->user_id){
            return redirect()->back()->withErrors('Недостаточно прав');    
        }

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
            'personality' => $request->input('personality'),
            'status_id' => 1
        ]);



        if ($request->hasFile('image')) {
             
            if ($character->images->first()) {
                if (Storage::disk('public')->exists($character->images->first()->path)) {
                    Storage::disk('public')->delete($character->images->first()->path);
                }
            }

            $file = $request->file('image');

            $folderPath = "images/character/{$character->uuid}";
            $imagePath = $request->file('image')->store($folderPath, 'public');

            $fileContent = file_get_contents($file->getPathname());

            $hash = md5($fileContent);

            $character->images->first()->update([
                'path' => $imagePath,
                'file_hash' => $hash
            ]);
        }

        $characterId = $uuid;
        

        return redirect()->route('characters.showCreateSkills', $characterId);
    }

    public function showCreateSkills($uuid){   

        if (auth()->user()->id != Character::where('uuid', $uuid)->first()->user_id) {
            return redirect()->back()->withErrors('Недостаточно прав');
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

    public function createSkills($uuid, Request $request){

        if (auth()->user()->id != Character::where('uuid', $uuid)->first()->user_id){
            return redirect()->back()->withErrors('Недостаточно прав');    
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
            return redirect()->back()->withErrors('Недостаточно прав');    
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

    public function showCreateDescription($uuid){

        if (auth()->user()->id != Character::where('uuid', $uuid)->first()->user_id) {
            return redirect()->back()->withErrors('Недостаточно прав');
        }

        $characterId = Character::where('uuid', $uuid)->first()->id;

        if (CharacterDescription::where('character_id',  $characterId)->first()){
            $characterDescripron = CharacterDescription::where('character_id',  $characterId)->first();
            if  (auth()->user()->id != $characterDescripron->character->user_id){
                return redirect()->back()->withErrors('Недостаточно прав');
            }

            $characterId = $uuid;

            return view('frontend.characters.description', compact( 'characterId', 'characterDescripron')); 
        }

        $characterId = $uuid;

        return view('frontend.characters.description', compact( 'characterId'));
    }

    public function createDescription($uuid ,Request $request) {

        if (auth()->user()->id != Character::where('uuid', $uuid)->first()->user_id){
            return redirect()->back()->withErrors('Недостаточно прав');    
        }

        $validated = $request->validate([
            'biography' => ['required', 'string'],
            'description' => ['required', 'string'],
            'headcounts' => ['nullable' ,'string']
        ]);

        $characterId = Character::where('uuid', $uuid)->first()->id;
        CharacterDescription::create([
            'character_id' => $characterId,
            'biography' => $validated['biography'],
            'description' => $validated['description'],
            'headcounts' => $validated['headcounts']
        ]);

        $character = Character::findOrFail($characterId);
        $character->update([
            'status_id' => 2
        ]);

        return redirect()->route('characters.index');
    }

    public function updateDescription($uuid, Request $request){

        if (auth()->user()->id != Character::where('uuid', $uuid)->first()->user_id){
            return redirect()->back()->withErrors('Недостаточно прав');    
        }

        $validated = $request->validate([
            'biography' => ['required', 'string'],
            'description' => ['required', 'string'],
            'headcounts' => ['nullable' ,'string']
        ]);

        $character = Character::where('uuid', $uuid)->first();
        $characterId = $character->id;

        $characterDescription = CharacterDescription::findOrFail($characterId);
        $characterDescription->update([
            'biography' => $validated['biography'],
            'description' => $validated['description'],
            'headcounts' => $validated['headcounts']
        ]);

        $character->update([
            'status_id' => 2
        ]);

        return redirect()->route('characters.index');
    }

}
