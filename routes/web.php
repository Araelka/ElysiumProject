<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use PhpParser\Node\Expr\AssignOp\Pow;

Route::get('/', [MainController::class, 'index'])->name('homePage');

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

Route::post('publish', [PostController::class, 'store'])->name('post.publish');
Route::delete('posts/{id}', [PostController::class, 'destroy'])->name('post.destroy');

Route::get('edit/{id}', [PostController::class, 'showEditForm'])->name('post.edit');