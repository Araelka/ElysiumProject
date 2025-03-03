<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Models\Location;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Str;

class AdminController extends Controller
{
    public function index(){
        return view('frontend.admin.admin');
    }

    public function showTableUser () {
        $users = User::with('role')->get();

        return view('frontend.admin.adminShowUsers', ['users' => $users]);
    }

    public function destroyUser ($id) {

        if (!auth()->user()->isAdmin()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $user = User::findOrFail($id);
        if ($user->role_id != 1) {
            $user->delete();
        }

        return redirect()->route('admin.showUsers');
    }

    public function showEditUserForm ($id){
        $user = User::with('role')->findOrFail($id);

        $roles = Role::all();

        return view('frontend.admin.adminShowUser', ['user' => $user, 'roles' => $roles]);
    }

    public function editUser (AdminRequest $request, $id) {

        if (!auth()->user()->isAdmin()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $user = User::findOrFail($id);
        $validated = $request->validated();
        $user->update([
            'email' => $validated['email'],
            'role_id' => $validated['role_id']
        ]);

        return redirect()->route('admin.showUsers');

    }

    public function resetPassword (Request $request, $id) {

        if (!auth()->user()->isAdmin()) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }
        
        $user = User::findOrFail($id);
        $newPassword = Str::random(10);
        $user->password = Hash::make($newPassword);
        $user->save();

        return redirect()->back()->with('newPassword', $newPassword);
    }

    public function showTableLocations () {

        $locations = Location::withCount('posts')->get();

        return view('frontend.admin.adminShowLocations', ['locations' => $locations]);
    }

    public function destroyLocation($id) {

        if (!auth()->user()->isAdmin()) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        Location::findOrFail($id)->delete();

        return redirect()->route('admin.showLocations');
    }

    public function showEditLocationForm($id) {
        
        $location = Location::findOrFail($id);

        return view('frontend.admin.adminShowLocation', ['location' => $location]);
    }

    public function editLocation (Request $request, $id) {


        if (!auth()->user()->isAdmin()) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $validated = $request->validate([
            'name' => ['required', 'string']
        ]);

        $location = Location::findOrFail($id);
        $location->update([
            'name' => $validated['name']
        ]);

        return redirect()->route('admin.showLocations');
    }

}
