<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use PhpParser\Node\Expr\AssignOp\Pow;


Route::middleware('guest')->group(function(){
    Route::get('register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);

    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function(){
    Route::get('/', [PostController::class, 'index'])->name('homePage');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
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
    Route::delete('users/{id}/destroy', [AdminController::class, 'destroyUser'])->name('admin.destroy');
    Route::get('users/{id}/edit', [AdminController::class, 'showEditUserForm'])->name('admin.showEditForm');
    Route::put('users/{id}/edit', [AdminController::class, 'editUser'])->name('admin.edit');
    Route::put('user/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('admin.resetPassword');
});

