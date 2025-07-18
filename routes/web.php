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

    Route::get('characters', [CharacterController::class, 'index'])->name('characters.index');
    Route::post('character/create', [CharacterController::class, 'createMainInfo'])->name('characters.create');
    Route::post('character/create/skills', [CharacterController::class, 'createSkills'])->name('characters.createSkills');
});

Route::middleware(['auth', 'editor'])->group(function(){
    Route::get('wiki/theme/create', [ThemeController::class, 'showCreateThemeForm'])->name('wiki.showCreateThemeForm');
    Route::post('wiki/theme/create', [ThemeController::class, 'createTheme'])->name('wiki.createTheme');
    Route::delete('wiki/theme/{id}/destroy', [ThemeController::class, 'destroy'])->name('wiki.destroyTheme');
    Route::post('wiki/theme/upload-image/{id}', [ThemeController::class,'uploadImage'])->name('wiki.uploadImage');
    Route::put('wiki/theme/toggle-visibility/{id}', [ThemeController::class, 'toggleVisibility'])->name('wiki.toggleVisibility');

    Route::get('wiki/article/edit/title/{id}', [ArticleController::class, 'showEditTitleForm'])->name('wiki.showEditArticleTitle');
    Route::put('wiki/article/edit/title/{id}', [ThemeController::class, 'editTheme'])->name('wiki.editArticleTitle');
    Route::get('wiki/article/edit/content/{id}', [ArticleController::class, 'showEditArticleContent'])->name('wiki.showEditArticleContent');
    Route::put('wiki/article/edit/content/{id}', [ArticleController::class, 'editArticleContent'])->name('wiki.editArticleContent');
    Route::post('wiki/article/edit/content/{id}', [ArticleController::class, 'uploadImage'])->name('wiki.uploadArticleImage');
});

Route::middleware(['auth', 'player'])->group(function(){
    Route::post('publish', [PostController::class, 'store'])->name('post.publish');
    Route::delete('destroy/{id}', [PostController::class, 'destroy'])->name('post.destroy');

    Route::get('edit/{id}', [PostController::class, 'showEditForm'])->name('post.editShow');
    Route::put('edit', [PostController::class, 'edit'])->name('post.edit');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function() {
    Route::get('/', [AdminController::class, 'index'])->name('admin');

    Route::get('users', [AdminController::class, 'showTableUser'])->name('admin.showUsers');
    Route::delete('users/{id}/destroy', [AdminController::class, 'destroyUser'])->name('admin.destroyUser');
    Route::get('users/{id}/edit', [AdminController::class, 'showEditUserForm'])->name('admin.showUserEditForm');
    Route::put('users/{id}/edit', [AdminController::class, 'editUser'])->name('admin.editUser');
    Route::put('user/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('admin.resetPassword'); 
    Route::put('users/{id}/ban', [AdminController::class, 'userBan'])->name('admin.userBan');
    Route::put('users/{id}/unban', [AdminController::class, 'userUnban'])->name('admin.userUnban');
    Route::post('users/bulk-destroy', [AdminController::class, 'bulkUserDestroy'])->name('admin.bulkUserDestroy');
    Route::put('users/bulk-ban', [AdminController::class, 'bulkUserBan'])->name('admin.bulkUserBan');
    Route::get('users/create', [AdminController::class, 'showCreateUserForm'])->name('admin.showCreateUserForm');
    Route::post('users/create', [AdminController::class, 'createUser'])->name('admin.createUser');

    Route::get('locations', [AdminController::class, 'showTableLocations'])->name('admin.showLocations');
    Route::delete('locations/{id}/destroy', [AdminController::class, 'destroyLocation'])->name('admin.destroyLocation');
    Route::post('locations/bulk-destroy', [AdminController::class, 'bulkLocationDestroy'])->name('admin.bulkDestroyLocation');
    Route::get('locations{id}/edit', [AdminController::class, 'showEditLocationForm'])->name('admin.showLocationEditForm');
    Route::put('locations/{id}/edit', [AdminController::class, 'editLocation'])->name('admin.editLocation');
    Route::get('locations/create', [AdminController::class, 'showCreateLocationForm'])->name('admin.showLocationCreateForm');
    Route::post('locations/create', [AdminController::class, 'createLocation'])->name('admin.createLocation');
});

