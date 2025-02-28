<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
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

        return redirect()->route('admin.showUsers');
    }

    public function showEditForm ($id){
        $user = User::with('role')->findOrFail($id);

        $roles = Role::all();

        return view('frontend.admin.adminShowUser', ['user' => $user, 'roles' => $roles]);
    }

    public function edit (Request $request, $id) {
        $user = User::findOrFail($id);
        $user->role_id = $request->input('role_id');
        $user->update();

        return redirect()->route('admin.showUsers');
    }
}
