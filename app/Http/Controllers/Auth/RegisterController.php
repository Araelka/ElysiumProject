<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\RegisterRequest;



class RegisterController extends Controller
{   //
    public function showRegisterForm() {
        return view('frontend/auth/register');
    }

    public function register(RegisterRequest $request) {
        
        $user = User::create([
            'login' => $request->input('login'),
            'email' => $request->input('email'),
            'password' => $request->input('password')
        ]);

        $user->roles()->attach(7);

        auth()->login($user);

        return redirect('/')->with('success', 'Вы успешно зарегистрировались!');
    }
}
