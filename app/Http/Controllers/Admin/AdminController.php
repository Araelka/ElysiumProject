<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(){
        return view('frontend.admin.admin');
    }

    public function showTableUser () {
        $users = User::with('role')->get();

        return view('frontend.admin.adminShowUsers', ['users' => $users]);
    }

    public function destroy ($id) {
        if (auth()->user()->isAdmin()){
            $user = User::findOrFail($id);
            if ($user->role_id != 1) {
                $user->delete();
            }
        }

        return redirect()->back();
    }
}
