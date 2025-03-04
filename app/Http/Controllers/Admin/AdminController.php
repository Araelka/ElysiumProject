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

    public function showTableUser (Request $request) {
        // $users = User::with('role')->get();

        // return view('frontend.admin.adminShowUsers', ['users' => $users]);

        $searchTerm = $request->query('search');
        $searchFields = ['login', 'email'];

        $users = User::when($searchTerm, function ($query) use ($searchTerm, $searchFields) {
            foreach ($searchFields as $field) {
                $query->orWhereRaw('LOWER(' . $field . ') LIKE ?', ['%' . mb_strtolower($searchTerm) . '%']);
            }
        })->with('role')->get();

        return view('frontend.admin.adminShowUsers', compact('users'));
    }

    public function destroyUser ($id) {

        if (!auth()->user()->isAdmin()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $user = User::findOrFail($id);
        if ($user->role_id != 1) {
            $user->ban();
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

    public function bulkUserDestroy (Request $request) {

        if (!auth()->user()->isAdmin()) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $validated = $request->validate([
            'selected_ids' => ['required', 'string'], 
        ]);

        $selectedIds = explode(',', $validated['selected_ids']);

        $idsArray = array_filter($selectedIds, 'is_numeric');

        if (empty($idsArray)) {
            return redirect()->back()->withErrors('Не выбраны элементы для удаления');
        }

        User::whereIn('id', $idsArray)->delete();

        return redirect()->back();
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

    public function showTableLocations (Request $request) {

        // $locations = Location::withCount('posts')->get();

        // return view('frontend.admin.adminShowLocations', ['locations' => $locations]);

        $searchTerm = $request->query('search');
        
        $locations = Location::when($searchTerm, function ($query) use ($searchTerm) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . mb_strtolower($searchTerm) . '%']);
        })->withCount('posts')->get();

        return view('frontend.admin.adminShowLocations', compact('locations'));
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

    public function bulkLocationDestroy (Request $request) {

        if (!auth()->user()->isAdmin()) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }
        
        $validated = $request->validate([
            'selected_ids' => ['required', 'string'], 
        ]);

        $selectedIds = explode(',', $validated['selected_ids']);

        $idsArray = array_filter($selectedIds, 'is_numeric');

        if (empty($idsArray)) {
            return redirect()->back()->withErrors('Не выбраны элементы для удаления');
        }

        Location::whereIn('id', $idsArray)->delete();

        return redirect()->back();
    }

    public function showCreateLocationForm () {
        return view('frontend.admin.adminCreateLocation');
    }

    public function createLocation (Request $request) {

        if (!auth()->user()->isAdmin()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $validated = $request->validate([
            'name' => ['required', 'string']
        ]);

        Location::create([
            'name' => $validated['name']
        ]);

        return redirect()->route('admin.showLocations');
    }

    

}
