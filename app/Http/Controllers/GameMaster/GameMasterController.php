<?php

namespace App\Http\Controllers\GameMaster;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Models\CharacterStatus;
use Illuminate\Http\Request;

class GameMasterController extends Controller
{
    public function index() {
        if (!auth()->user()->isGameMaster() && !auth()->user()->isQuestionnaireSpecialist()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        return redirect()->route('game-master.showCharactersTable');
    }

    public function showCharactersTable (Request $request){

        if (!auth()->user()->isGameMaster() && !auth()->user()->isQuestionnaireSpecialist()){
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

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
        })->when($filter === 'dead', function ($query) {   
            $query->where('status_id', 6);      
        });


        if ($searchTerm) {
            $query = $query->where(function ($query) use ($searchTerm, $searchFields) {
                foreach ($searchFields as $field) {
                    $query->orWhereRaw('LOWER(' . $field . ') LIKE ?', ['%' . mb_strtolower($searchTerm) . '%']);
                }

                $query->orWhereRaw('CONCAT(LOWER(firstName), \' \', LOWER(secondName)) LIKE ?', ['%' . mb_strtolower($searchTerm) . '%']);

                $query->orWhereHas('user', function($query) use ($searchTerm){
                    $query->whereRaw('LOWER(login) LIKE ?', ['%' . mb_strtolower($searchTerm) . '%']);
                });
            });
        }
        
        if(auth()->user()->isGameMaster() && auth()->user()->isQuestionnaireSpecialist()){
            $queryTemp = $query;
        }
        elseif (auth()->user()->isGameMaster()){
            $queryTemp = $query->whereIn('status_id', [3,5,6]);
        }   
        elseif (auth()->user()->isQuestionnaireSpecialist()){
            $queryTemp = $query->whereIn('status_id', [1,2,4]);
        } 
        
            
        $characters = $query->paginate(20);

        $characters->appends([
            'filter' => $filter,
            'search' => $searchTerm
        ]);
        
        return view('frontend.gamemaster.gmShowCharacters', compact('characters'));
    }

    public function showCharacter($id){

        if (!auth()->user()->isGameMaster() && !auth()->user()->isQuestionnaireSpecialist()) {
            return redirect()->back()->withError('У вас нет прав на совершение данного действия');
        }

        $character = Character::findOrFail($id);
        

        if (!auth()->user()->isGameMaster() || !auth()->user()->isQuestionnaireSpecialist()){
            if (auth()->user()->isQuestionnaireSpecialist() && !$character->isPreparing() && !$character->isConsideration() & !$character->isRejected()){
                return redirect()->back()->withError('У вас нет прав на совершение данного действия');
            }
            elseif (auth()->user()->isGameMaster() && !$character->isApproved() && !$character->isArchive() & !$character->isDead()){
                return redirect()->back()->withError('У вас нет прав на совершение данного действия');
            }
        }


        $statuses = CharacterStatus::all();

        return view('frontend.gamemaster.gmShowCharacter', compact('character', 'statuses'));
    }
}
