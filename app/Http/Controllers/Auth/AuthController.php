<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    public function showLoginForm() {
        return view('frontend/auth/login');
    }

    public function login(Request $request){
        $credentials = $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string']
        ]);

        if (filter_var($request->input('identifier'), FILTER_VALIDATE_EMAIL)) {
            $field = 'email'; 
        } else {
            $field = 'login'; 
            $credentials['identifier'] = mb_strtolower($credentials['identifier']);
        }

        $user = User::where($field, $credentials['identifier'])->first();

        if ($user && $user->is_banned) {
            return back()->withErrors([
                'identifier' => 'Ваш аккаунт заблокирован.<br> Причина: ' . $user->ban_reason
            ])->onlyInput('identifier');
        }

        if (Auth::attempt([$field => $credentials['identifier'], 'password' => $credentials['password']])){
            $request->session()->regenerate();
            return redirect('/');
        };

        return back()->withErrors([
            'identifier' => 'Неверный логин/email или пароль'
        ])->onlyInput('identifier');
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate(); 
        $request->session()->regenerateToken(); 
        return redirect()->route('login');
    }
}
