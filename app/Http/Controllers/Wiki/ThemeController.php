<?php

namespace App\Http\Controllers\Wiki;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleImage;
use App\Models\Theme;
use App\Models\ThemeImage;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use function PHPUnit\Framework\returnArgument;

class ThemeController extends Controller
{
    public function index(Request $request) {

        $searchTerm = $request->query('search');
    

        $query = Theme::when($searchTerm, function ($query) use ($searchTerm) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . mb_strtolower($searchTerm) . '%'])
            ->orWhereHas('article', function ($query) use ($searchTerm) {
                $query->whereRaw('LOWER(content) LIKE ?', ['%' . mb_strtolower($searchTerm) . '%']);
            });
        })->with(['images' => function ($query) {
           $query->orderBy('id', 'asc')->take(1);
       }]);

        if (!auth()->check() || !auth()->user()->isEditor()) {
            $query = $query->where('visibility', 1);
        }

        $themes = $query->paginate(20);

        return view('frontend.wiki.showThemes', compact('themes'));
    }

    public function createTheme (Request $request){

        if ($request->hasFile('image')) {
            $image = $request->file('image');
        }

        $validated = $request->validate([
            'name' => ['required', 'unique:themes', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:png,jpeg,jpg,webp', 'max:2048']
        ]);

        $theme = Theme::create([
            'name' => $validated['name']
        ]);

        $article = Article::create([
            'theme_id' => $theme->id,
            'content' => ""
        ]);


        if ($request->hasFile('image')) {
            $folderPath = "images/wiki/{$theme->name}";
            $imagePath = $request->file('image')->store($folderPath, 'public');

            $image = new ThemeImage();
            $image->theme_id = $theme->id;
            $image->path = $imagePath;
            $image->description = 'Изображение для темы "' . $theme->name . '"';
            $image->save();
        }

        return redirect()->route('wiki.index');
    }

    public function editTheme(Request $request, $id){
        if (!auth()->user()->isEditor()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $validate = $request->validate([
            'name' => [
                'required', 
                'max:255', 
                Rule::unique('themes', 'name')->ignore($id)
                ]
        ]);


        $theme = Theme::findOrFail($id);
        $theme->update([
            'name' => $validate['name']
        ]);


        return redirect()->route('wiki.article.index', $theme->article->id);
    }

    public function uploadImage (Request $request, $id) {
        // dd($request);
        if (!auth()->user()->isEditor()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }


        $validated = $request->validate([
            'image' => ['nullable', 'image', 'mimes:png,jpeg,jpg,webp', 'max:2048']
        ]);

        $theme = Theme::findOrFail($id);

        if ($theme->images) {
            foreach ($theme->images as $image) {
                if (Storage::disk('public')->exists($image->path)) {
                    Storage::disk('public')->delete($image->path);
                }
                $image->delete();
            }
        }


        if ($request->hasFile('image')) {
            $folderPath = "images/wiki/{$theme->name}";
            $imagePath = $request->file('image')->store($folderPath, 'public');

            $image = new ThemeImage();
            $image->theme_id = $id;
            $image->path = $imagePath;
            $image->description = 'Изображение для темы "' . $theme->name . '"';
            $image->save();
        }

        return redirect()->back();
    }

    public function toggleVisibility($id) {
        if (!auth()->user()->isEditor()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $theme = Theme::findOrFail($id);

        $theme->visibility = !$theme->visibility;
        $theme->save();

        return redirect()->back();
    }

    public function destroy ($id) {
        
         if (!auth()->user()->isEditor()) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $theme = Theme::findOrFail($id);

        if ($theme->images) {
            foreach ($theme->images as $image) {
                if (Storage::disk('public')->exists('images/wiki/' . $theme->name)) {
                    Storage::disk('public')->deleteDirectory('images/wiki/' . $theme->name);
                }
            }
        }

        $theme->delete();

        return redirect()->route('wiki.index');
    } 



    public function showCreateThemeForm() {
        return view('frontend.wiki.showTheme');
    }



    
}
