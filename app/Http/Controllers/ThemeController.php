<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleImage;
use App\Models\Theme;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use function PHPUnit\Framework\returnArgument;

class ThemeController extends Controller
{
    public function index(Request $request) {

        $searchTerm = $request->query('search');
        
        $query = Theme::when($searchTerm, function ($query) use ($searchTerm) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . mb_strtolower($searchTerm) . '%']);
        })->whereHas('article')->with(['article.images' => function ($query) {
           $query->orderBy('id', 'asc')->take(1);
       }]);


        $themes = $query->paginate(20);

        return view('frontend.wiki.showThemes', compact('themes'));
    }

    public function CreateTheme (Request $request){

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            \Log::info('File MIME type:', ['mime' => $image->getMimeType()]);
        }
        $validated = $request->validate([
            'name' => ['required', 'unique:themes', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:png,jpeg,jpg,webp', 'max:2048']
        ]);

        $theme = new Theme();
        $theme->name = $validated['name'];
        $theme->save();

        $article = new Article();
        $article->theme_id = $theme->id;
        $article->title = $validated['name'];
        $article->content = "";
        $article->save();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');

            $image = new ArticleImage();
            $image->article_id = $article->id;
            $image->path = $imagePath;
            $image->description = 'Изображение для темы "' . $theme->name . '"';
            $image->save();
        }

        return redirect()->route('wiki.index');
    }

    public function destroy ($id) {
        
         if (!auth()->user()->isEditor()) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $theme = Theme::findOrFail($id);

        if ($theme->article && $theme->article->images) {
            foreach ($theme->article->images as $image) {
                if (Storage::disk('public')->exists($image->path)) {
                    Storage::disk('public')->delete($image->path);
                }
                $image->delete();
            }
        }

        if ($theme->article) {
            $theme->article->delete();
        }

        $theme->delete();

        return redirect()->route('wiki.index');
    } 



    public function showCreateThemeForm() {
        return view('frontend.wiki.showTheme');
    }


    
}
