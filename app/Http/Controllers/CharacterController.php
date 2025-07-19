<?php

namespace App\Http\Controllers;

use App\Http\Requests\CharacterRequest;
use App\Models\Attribute;
use App\Models\Character;
use App\Models\CharacterAttribute;
use App\Models\CharacterSkill;
use App\Models\Skill;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    public function index() {
        // $character = Character::findOrFail(1);
        // $skills = Skill::findOrFail(3);
        // $characterA = CharacterAttribute::findOrFail(2);
        // $skill = CharacterSkill::findOrFail($skills->id)->where('character_id', $character->id)->first();
        // dd($character->status->name);

        return view('frontend.characters.mainInfo');
    }

    public function createMainInfo(CharacterRequest $request) {

        $character = Character::create([
            'user_id' => auth()->user()->id,
            'firstName' => $request->input('firstName'),
            'secondName' => $request->input('secondName'),
            'age' => $request->input('age'),
            'gender' => $request->input('gender'),
            'nationality' => $request->input('nationality'),
            'residentialAddress' => $request->input('residentialAddress'),
            'activity' => $request->input('activity'),
            'personality' => $request->input('personality'),
            'status_id' => 1
        ]);
        
        $attributes = Attribute::with('skills')->get();
        $characterId = $character->id;

        return view('frontend.characters.attributes', compact('attributes', 'characterId'));
    }

    public function createSkills(Request $request){
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

        foreach ($validated['attributes'] as $id =>$attribute) {
            $characterAttribute = CharacterAttribute::create([
                'character_id' => $request->input('characterId'),
                'attribute_id' => $id,
                'points' => $attribute
            ]);
        }        

        foreach ($validated['skills'] as $id => $skill) {
            $characterSkill = CharacterSkill::create([
                'character_id' => $request->input('characterId'),
                'skill_id' => $id
            ]);
        }

        return view('frontend.index');

    }

}
