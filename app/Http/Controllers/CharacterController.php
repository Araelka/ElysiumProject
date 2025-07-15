<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    public function index() {

        $attributes = Attribute::with('skills')->get();

        return view('frontend.characters.index', compact('attributes'));
    }
}
