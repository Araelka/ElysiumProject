<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Character;
use App\Models\Location;
use App\Models\User;
use App\Models\Role;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Str;

class AdminController extends Controller
{
    public function index(){
        return view('frontend.admin.admin');
    }

    public function showTableUser (Request $request) {

        $filter = $request->query('filter', 'all');
        $searchTerm = $request->query('search');
        $searchFields = ['login', 'email'];

        $query = User::query()->with('role')
        ->when($filter === 'active', function ($query) {   
            $query->where('is_banned', false);      
        })->when($filter === 'banned', function ($query) {
            $query->where('is_banned', true);
        });

        if ($searchTerm) {
            $query = $query->where(function ($query) use ($searchTerm, $searchFields) {
                foreach ($searchFields as $field) {
                    $query->orWhereRaw('LOWER(' . $field . ') LIKE ?', ['%' . mb_strtolower($searchTerm) . '%']);
                }
                });
        }

        $users = $query->paginate(20);

        $users->appends([
            'filter' => $filter,
            'search' => $searchTerm
        ]);

        return view('frontend.admin.adminShowUsers', compact('users'));
    }

    public function destroyUser ($id) {

        if (!auth()->user()->isAdmin()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $user = User::findOrFail($id);

        if ($user->id != 1) {
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

        User::whereIn('id', $idsArray)->each(function($user){
            if ($user->id != 1) {
                $user->delete();
            }
        });

        return redirect()->back();
    }

    public function bulkUserBan (Request $request) {

        if (!auth()->user()->isAdmin()) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $validated = $request->validate([
            'selected_ids' => ['required', 'string'],
            'ban_reason' => ['nullable', 'string'], 
        ]);

        $selectedIds = explode(',', $validated['selected_ids']);

        $idsArray = array_filter($selectedIds, 'is_numeric');
        $banReason = $validated['ban_reason'] ?? 'Нарушение правил сообщества';

        if (empty($idsArray)) {
            return redirect()->back()->withErrors('Не выбраны элементы для удаления');
        }

        User::whereIn('id', $idsArray)->get()->each(function($user) use ($banReason) {
            if ($user->id != 1 && !$user->is_banned){
                $user->terminateSessions();
                $user->ban($banReason);
            }
        });

        return redirect()->back();
    }

    public function userBan(Request $request, $id) {
        if (!auth()->user()->isAdmin()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $validated = $request->validate([
            'ban_reason' => ['nullable', 'string']
        ]);

        $user = User::findOrFail($id);

        $banReason = $validated['ban_reason'] ?? 'Нарушение правил сообщества';

        if ($user->id != 1 && !$user->is_banned) {
            $user->terminateSessions();
            $user->ban($banReason);
        }

        return redirect()->back();
    }

    public function userUnban($id) {
        if (!auth()->user()->isAdmin()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $user = User::findOrFail($id);

        if ($user->is_banned){
            $user->unban();
        }

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

    public function showCreateUserForm (){

        $roles = Role::all();

        return view('frontend.admin.adminCreateUser', compact('roles'));
    }

    public function createUser (RegisterRequest $request) {

        if (!auth()->user()->isAdmin()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        User::create([
            'login' => $request->input('login'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'role_id' => $request->input('role_id')
        ]);

        return redirect()->route('admin.showUsers');
    }

    public function showTableLocations (Request $request) {

        $searchTerm = $request->query('search');
        
        $query = Location::when($searchTerm, function ($query) use ($searchTerm) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . mb_strtolower($searchTerm) . '%']);
        })->withCount('posts');

        $locations = $query->paginate(12);

        $locations->appends([
            'search' => $searchTerm
        ]);


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

    public function showCharactersTable (Request $request){

        $filter = $request->query('filter', 'all');
        $searchTerm = $request->query('search');
        $searchFields = ['firstName', 'secondName'];

        $query = Character::query()->with('status')
        ->when($filter === 'approved', function ($query) {   
            $query->where('status_id', 3);      
        })->when($filter === 'consideration', function ($query) {   
            $query->where('status_id', 2);      
        })->when($filter === 'preparing', function ($query) {   
            $query->where('status_id', 1);      
        })->when($filter === 'rejected', function ($query) {   
            $query->where('status_id', 4);      
        })->when($filter === 'archive', function ($query) {   
            $query->where('status_id', 5);      
        });


        if ($searchTerm) {
            $query = $query->where(function ($query) use ($searchTerm, $searchFields) {
                foreach ($searchFields as $field) {
                    $query->orWhereRaw('LOWER(' . $field . ') LIKE ?', ['%' . mb_strtolower($searchTerm) . '%']);
                }

                $query->orWhereHas('user', function($query) use ($searchTerm){
                    $query->whereRaw('LOWER(login) LIKE ?', ['%' . mb_strtolower($searchTerm) . '%']);
                });
            });
        }

        $characters = $query->paginate(20);

        $characters->appends([
            'filter' => $filter,
            'search' => $searchTerm
        ]);
        
        return view('frontend.admin.adminShowCharacters', compact('characters'));
    }

    

}
