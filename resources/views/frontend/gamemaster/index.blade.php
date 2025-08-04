@extends('frontend.layout.layout')
<link rel="stylesheet" href="{{ asset('css/gamemaster.css') }}">
@section('title', 'ГМ-панель')

@section('content')
<div class="main-page">
    <div class="container d-flex justify-content-center align-items-stretch"> 
        <div class="row w-100 h-100"> 
            <div class="col-md-12 main-content d-flex flex-column justify-content-start" style="overflow-y: visible">
                <div class="table-selection-container d-flex justify-content-between align-items-center mb-3">
                    <div class="table-selection d-flex align-items-center">
                        <ul class="table-list d-flex">
                            <li><a href="{{ route('game-master.showCharactersTable') }}" class="table-link {{ request()->routeIs('game-master.showCharactersTable') ? 'active' : '' }}">Персонажи</a></li>
                        </ul>
                    </div>
                    @if (!Request::is('game-master/*/*'))
                        
                    <div class="search-container">
                        <form action="{{ url()->current() }}" method="GET" id="search-form">
                            <input type="hidden" name="filter" value="{{ request('filter', 'all') }}">
                            <div class="search-input-wrapper">
                                <input 
                                    type="text" 
                                    name="search" 
                                    id="search-input" 
                                    value="{{ request('search') }}" 
                                    placeholder="Поиск..." 
                                    class="search-input"
                                >
                                @if(request('search'))
                                    <button type="button" id="clear-search" class="clear-search-button">×</button>
                                @endif
                            </div>
                            <button type="submit" class="search-button">Найти</button>
                        </form>
                    </div>
                    @endif
                </div>
                <!-- Основное содержимое (таблица) -->
                @yield('table')
            </div>
        </div>
    </div>
</div>

@endsection