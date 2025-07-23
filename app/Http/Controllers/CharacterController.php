<?php

namespace App\Http\Controllers;

use App\Http\Requests\CharacterRequest;
use App\Models\Attribute;
use App\Models\Character;
use App\Models\CharacterAttribute;
use App\Models\CharacterDescription;
use App\Models\CharacterSkill;
use App\Models\Skill;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    protected function isUser($id) {
        if (auth()->user()->id != Character::findOrFail($id)->user_id){
            return redirect()->back()->withErrors('Недостаточно прав');    
        }
    }

    public function index($id= null) {
        // $character = Character::findOrFail(1);
        // $skills = Skill::findOrFail(3);
        // $characterA = CharacterAttribute::findOrFail(2);
        // $skill = CharacterSkill::findOrFail($skills->id)->where('character_id', $character->id)->first();
        // dd($character->status->name);

        // $characters = Character::where('user_id', '=', auth()->user()->id)->get();


        return view('frontend.characters.mainInfo');
    }

    public function showMainInfo($id = null){
        if($id != null){
            $character = Character::findOrFail($id);
            
            if (auth()->user()->id != $character->user_id){
                return redirect()->back()->withErrors('Недостаточно прав');
            }

            return view('frontend.characters.mainInfo', compact('character')); 
        };

        return view('frontend.characters.mainInfo'); 
    }

    public function createMainInfo(CharacterRequest $request) {

        $character = Character::create([
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
        
        return redirect()->route('characters.showCreateSkills', $character->id);
    }

    public function updateMainInfo(CharacterRequest $request){
        $characterId = $request->input('characterId');
        $character = Character::findOrFail($characterId);

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


        return redirect()->route('characters.showCreateSkills', $characterId);
    }

    public function showCreateSkills($id){

        if (auth()->user()->id != Character::findOrFail($id)->user_id) {
            return redirect()->back()->withErrors('Недостаточно прав');
        }

        $characterId = $id;
        $attributes = Attribute::with('skills')->get();

        if(Character::findOrFail($characterId)->attributes->first()){
            $characterAttributes = CharacterAttribute::where('character_id', '=', $characterId)->get();

            return view('frontend.characters.attributes', compact('attributes','characterAttributes', 'characterId')); 
        };

        return view('frontend.characters.attributes', compact('attributes', 'characterId'));
    }

    public function createSkills($id, Request $request){

        if (auth()->user()->id != Character::findOrFail($id)->user_id){
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

        $characterId = $id;

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


        return redirect()->route('characters.showCreateDescription', $characterId);;

    }
    
    public function updateAttributes($id, Request $request){

        if (auth()->user()->id != Character::findOrFail($id)->user_id){
            return redirect()->back()->withErrors('Недостаточно прав');    
        }

        $validated = $request->validate([
            'attributes' => ['required', 'array'],
            'attributes.*' => ['required' ,'integer', 'min:0', 'max:6']
        ]);

        if (array_sum($validated['attributes']) > 6) {
            return redirect()->back()->withErrors('Превышен предел суммы количства очков');
        }
        
        $characterId = $id;

        foreach ($validated['attributes'] as $id => $attribute) {
            $characterAttribute = CharacterAttribute::where('character_id', '=', $characterId)->where('attribute_id', '=', $id)->first();
            $characterAttribute->update([
                'points' => $attribute
            ]);
        }


        return redirect()->route('characters.showCreateDescription', $characterId);
    }

    public function showCreateDescription($id){

        if (auth()->user()->id != Character::findOrFail($id)->user_id) {
            return redirect()->back()->withErrors('Недостаточно прав');
        }

        $characterId = $id;

        if (CharacterDescription::where('character_id', '=', $characterId)->first()){
            $characterDescripron = CharacterDescription::where('character_id', '=', $id)->first();
            if  (auth()->user()->id != $characterDescripron->character->user_id){
                return redirect()->back()->withErrors('Недостаточно прав');
            }

            return view('frontend.characters.description', compact( 'characterId', 'characterDescripron')); 
        }

        return view('frontend.characters.description', compact( 'characterId'));
    }

    public function createDescription(Request $request) {
        $validated = $request->validate([
            'biography' => ['required', 'string'],
            'description' => ['required', 'string'],
            'headcounts' => ['nullable' ,'string']
        ]);


        CharacterDescription::create([
            'character_id' => $request->input('characterId'),
            'biography' => $validated['biography'],
            'description' => $validated['description'],
            'headcounts' => $validated['headcounts']
        ]);

        $character = Character::findOrFail($request->input('characterId'));
        $character->update([
            'status_id' => 2
        ]);

        return view('frontend.characters.index');
    }

}
