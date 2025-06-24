<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use Illuminate\Http\Request;
use function PHPUnit\Framework\returnArgument;

class ThemeController extends Controller
{
    public function index() {
        $themes = Theme::with('article');

        $themes = $themes->paginate(20);


        // return view('frontend.admin.adminShowUsers', compact('users'));

        return view('frontend.wiki.showThemes', compact('themes'));
    }

    public function CreateTheme (Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'unique:themes', 'max:255']
        ]);

        $theme = new Theme();
        $theme->name = $validated['name'];
        $theme->save();

        return redirect()->route('wiki.index');
    }

    public function showCreateThemeForm() {
        return view('frontend.wiki.showTheme');
    }


    
}
