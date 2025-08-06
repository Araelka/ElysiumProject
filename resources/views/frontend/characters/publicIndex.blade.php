@extends('frontend.layout.layout')
<link rel="stylesheet" href="{{ asset('css/character.css') }}">
@section('title', 'Персонажи')

@section('content')
<div class="main-page">
    <div class="container d-flex justify-content-center align-items-stretch"> 
        <div class="row w-100 h-100"> 
            <div class="col-md-12 main-content d-flex flex-column justify-content-start">
                 @if (Request::is('characters/public'))
                <div class="character-selection-container d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2>
                            Персонажи
                        </h2>
                    </div>
                        <div class="search-container">
                            <form action="{{ request()->fullUrlWithQuery(['search' => '']) }}" method="GET" id="search-form">
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
                </div>
                @endif

            @yield('table')

        </div>
    </div>
</div>
@endsection