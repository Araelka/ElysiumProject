<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CharacterController extends Controller
{
    public function index() {
        return view('frontend.characters.index');
    }
}
