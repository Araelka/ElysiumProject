<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Character;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    public function index() {
        return view('frontend.characters.mainInfo');
    }

    public function createMainInfo(Request $request) {
        $character = Character::create([
            'user_id' => auth()->user()->id,
            'firstName' => $request->input('firstName'),
            'secondName' => $request->input('secondName'),
            'age' => $request->input('age'),
            'gender' => $request->input('gender'),
            'nationality' => $request->input('nationality'),
            'residentialAddress' => $request->input('residentialAddress'),
            'activity' => $request->input('activity'),
            'personality' => $request->input('personality')
        ]);
        
        $attributes = Attribute::with('skills')->get();
        $characterId = $character->id;

        return view('frontend.characters.attributes', compact('attributes', 'characterId'));
    }

    public function createSkills(Request $request){
        dd($request);
    }

}
