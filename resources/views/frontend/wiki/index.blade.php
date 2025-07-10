@extends('frontend.layout.layout')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<link rel="stylesheet" href="{{ asset('css/wiki.css') }}">
@section('title', 'Вики')

@section('content')
<div class="admin-page">
    <div class="container d-flex justify-content-center align-items-stretch"> 
        <div class="row w-100 h-100"> 
            <div class="col-md-12 admin-content d-flex flex-column justify-content-start">
                 @if (Request::is('wiki'))
                <div class="theme-selection-container d-flex justify-content-between align-items-center mb-3">
                    <div class="theme-selection d-flex align-items-center">
                        @if (Auth::check() && Auth::user()->isEditor())
                            <ul class="theme-list d-flex">
                            <li><a href={{ route('wiki.showCreateThemeForm') }} class="theme-link">Создать</a></li>
                            </ul>
                        @endif
                    </div>
                        <div class="search-container">
                            <form action="{{ request()->fullUrlWithQuery(['search' => '']) }}" method="GET" id="search-form">
                                <input type="hidden" name="filter">
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
</div>



@endsection