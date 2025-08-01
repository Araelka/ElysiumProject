<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ThemeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


Route::get('/', [PostController::class, 'index'])->name('homePage');

Route::get('wiki', [ThemeController::class, 'index'])->name('wiki.index');
Route::get('wiki/article/{id}', [ArticleController::class, 'index'])->name('wiki.article.index');



Route::middleware('guest')->group(function(){
    Route::get('register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);

    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function(){
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('characters')->name('characters.')->group(function(){
        Route::get('/', [CharacterController::class, 'index'])->name('index');

        Route::get('create/mainInfo/{id?}', [CharacterController::class, 'showMainInfo'])->name('showMainInfo');
        Route::post('create/mainInfo', [CharacterController::class, 'createMainInfo'])->name('createMainInfo');
        Route::put('update/info/{id}', [CharacterController::class, 'updateMainInfo'])->name('updateMainInfo');
        Route::delete('destoy/character/{id}', [CharacterController::class, 'characterDestoy'])->name('characterDestoy');

        Route::get('create/attributes/{id}', [CharacterController::class, 'showCreateSkills'])->name('showCreateSkills');
        Route::get('update/skills/{id}', [CharacterController::class, 'showUpdateSkills'])->name('showUpdateSkills');
        Route::post('create/attributes/{id}', [CharacterController::class, 'createSkills'])->name('createSkills');
        Route::put('update/attributes/{id}', [CharacterController::class, 'updateAttributes'])->name('updateAttributes');
        Route::put('update/skills/{id}', [CharacterController::class, 'updateSkills'])->name('updateSkills');

        Route::get('create/description/{id}', [CharacterController::class, 'showCreateDescription'])->name('showCreateDescription');
        Route::post('create/description/{id}', [CharacterController::class, 'createDescription'])->name('createDescription');
        Route::put('update/description/{id}', [CharacterController::class, 'updateDescription'])->name('updateDescription');

        Route::put('update/archive-status/{id}', [CharacterController::class, 'changeArchiveStatus'])->name('changeArchiveStatus');

        Route::put('update/available-points/{id}', [CharacterController::class, 'increaseAvailablePoints'])->name('increaseAvailablePoints');
    });

    


    Route::post('/delete-temp-file', function (\Illuminate\Http\Request $request) {
        $path = $request->input('path');

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        Session::forget('temp_photo_path');

        return response()->json(['success' => true]);
    })->name('delete.temp.file');
});

Route::middleware(['auth', 'editor'])->group(function(){
    Route::prefix('wiki')->name('wiki.')->group(function(){
        Route::get('theme/create', [ThemeController::class, 'showCreateThemeForm'])->name('showCreateThemeForm');
        Route::post('theme/create', [ThemeController::class, 'createTheme'])->name('createTheme');
        Route::delete('theme/{id}/destroy', [ThemeController::class, 'destroy'])->name('destroyTheme');
        Route::post('theme/upload-image/{id}', [ThemeController::class,'uploadImage'])->name('uploadImage');
        Route::put('theme/toggle-visibility/{id}', [ThemeController::class, 'toggleVisibility'])->name('toggleVisibility');

        Route::get('article/edit/title/{id}', [ArticleController::class, 'showEditTitleForm'])->name('showEditArticleTitle');
        Route::put('article/edit/title/{id}', [ThemeController::class, 'editTheme'])->name('editArticleTitle');
        Route::get('article/edit/content/{id}', [ArticleController::class, 'showEditArticleContent'])->name('showEditArticleContent');
        Route::put('article/edit/content/{id}', [ArticleController::class, 'editArticleContent'])->name('editArticleContent');
        Route::post('article/edit/content/{id}', [ArticleController::class, 'uploadImage'])->name('uploadArticleImage');
    });
});

Route::middleware(['auth', 'player'])->group(function(){
    Route::post('publish', [PostController::class, 'store'])->name('post.publish');
    Route::delete('destroy/{id}', [PostController::class, 'destroy'])->name('post.destroy');

    Route::get('edit/{id}', [PostController::class, 'showEditForm'])->name('post.editShow');
    Route::put('edit', [PostController::class, 'edit'])->name('post.edit');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function() {
    Route::get('/', [AdminController::class, 'index'])->name('admin');

    Route::prefix('users')->name('admin.')->group(function(){
        Route::get('/', [AdminController::class, 'showTableUser'])->name('showUsers');
        Route::delete('{id}/destroy', [AdminController::class, 'destroyUser'])->name('destroyUser');
        Route::get('edit/{id}', [AdminController::class, 'showEditUserForm'])->name('showUserEditForm');
        Route::put('edit/{id}', [AdminController::class, 'editUser'])->name('editUser');
        Route::put('reset-password/{id}', [AdminController::class, 'resetPassword'])->name('resetPassword'); 
        Route::put('ban/{id}', [AdminController::class, 'userBan'])->name('userBan');
        Route::put('unban/{id}', [AdminController::class, 'userUnban'])->name('userUnban');
        Route::post('bulk-destroy', [AdminController::class, 'bulkUserDestroy'])->name('bulkUserDestroy');
        Route::put('bulk-ban', [AdminController::class, 'bulkUserBan'])->name('bulkUserBan');
        Route::get('create', [AdminController::class, 'showCreateUserForm'])->name('showCreateUserForm');
        Route::post('create', [AdminController::class, 'createUser'])->name('createUser');
    });

    Route::prefix('locations')->name('admin.')->group(function(){
        Route::get('/', [AdminController::class, 'showTableLocations'])->name('showLocations');
        Route::delete('destroy/{id}', [AdminController::class, 'destroyLocation'])->name('destroyLocation');
        Route::post('bulk-destroy', [AdminController::class, 'bulkLocationDestroy'])->name('bulkDestroyLocation');
        Route::get('edit/{id}', [AdminController::class, 'showEditLocationForm'])->name('showLocationEditForm');
        Route::put('edit/{id}', [AdminController::class, 'editLocation'])->name('editLocation');
        Route::get('create', [AdminController::class, 'showCreateLocationForm'])->name('showLocationCreateForm');
        Route::post('create', [AdminController::class, 'createLocation'])->name('createLocation');
    });

    Route::prefix('characters')->name('admin.')->group(function(){
        Route::get('/', [AdminController::class, 'showCharactersTable'])->name('showCharactersTable');
        Route::get('edit/{id}', [AdminController::class, 'editCharacter'])->name('editCharacter');
        Route::post('bulk-destroy', [AdminController::class, 'bulkCharacterDestroy'])->name('bulkCharacterDestroy');
        Route::put('update/character-status/{id}', [AdminController::class, 'chatgeCharacterStatus'])->name('chatgeCharacterStatus');
    });

});

