<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function index() {
        $themes = Theme::with('article')->get();

        return view('frontend/wiki/index', ['themes' => $themes]);
    }

    public function store (Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'unique:themes', 'max:255'],
            'description' => ['nullable', 'string']
        ]);


    }

    
}
