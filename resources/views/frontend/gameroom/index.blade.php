@extends('frontend.layout.layout')
@section('title', 'Игровая')

<meta name="base-url" content="{{ url('/') }}/">

<link rel="stylesheet" href="{{ asset('css/posts.css') }}">
@if ($selectedLocation) 
@section('title', $selectedLocation->name)
@endif


@section('content')

<div class="double-page">
    <div class="container d-flex justify-content-center align-items-stretch"> 
        <div class="row w-100 h-100"> 
            <div class="col-md-2 sidebar d-flex flex-column justify-content-start">
                <h3>Локации</h3>
                <ul class="topics-list">
                    @foreach ($locations as $location)
                        <li style="padding-right: 2px; padding-left: 2px;"><a href="{{ route('gameroom.index', ['location_id' => $location->id]) }}" 
                            class="topic-link {{ $selectedLocation && $selectedLocation->id == $location->id ? 'active' : '' }}"
                            style="display: flex; flex-direction: row; align-items: center; justify-content: space-between;">
                            {{ $location->name }}
                            @if (isset($unreadCounts[$location->id]) && $unreadCounts[$location->id]['total'] > 0)
                            <div class="badge-container">
                                    @if ($unreadCounts[$location->id]['replies_to_me'] > 0)
                                    <div class="replies-to-me-count-badge">↳</div>
                                    @endif
                                <div class="unread-count-badge">
                                    {{ $unreadCounts[$location->id]['total'] }}
                                </div>
                            </div>
                            @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Блок для просмотра постов (80%) -->
            <div class="col-md-10 content d-flex flex-column justify-content-start">
                @if ($selectedLocation)
                <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: center;">
                    <div>
                        <h3>{{ $selectedLocation->name }}</h3>
                    </div>
                    <div style="display:flex; flex-direction: row; align-items: center; gap: 10px; padding-right: 5px;">
                        <form action="{{ request()->fullUrlWithQuery(['search' => '']) }}" method="GET" id="post-search-form" style="margin: 0px; display: none;" id="post-search-form">
                            <input type="hidden" name="filter" class="search-input">
                            <div class="search-input-wrapper">
                                <input 
                                    type="text" 
                                    name="search" 
                                    id="post-search-input" 
                                    value="{{ request('search') }}" 
                                    placeholder="Поиск..." 
                                    class="search-input"
                                >

                                <button type="button" id="post-clear-search" class="clear-search-button" style="display: none">×</button>
                            </div>
                            {{-- <button type="submit" class="search-button">Найти</button> --}}
                        </form>
                        <div>
                            <button type="button" class="post-search-button" onclick="changeVisibilitySearchForm()">
                                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="35" height="35" viewBox="0,0,256,256" >
                                    <g class="post-search-button-svg" fill="#f4d03f" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g transform="scale(5.12,5.12)"><path d="M21,3c-9.39844,0 -17,7.60156 -17,17c0,9.39844 7.60156,17 17,17c3.35547,0 6.46094,-0.98437 9.09375,-2.65625l12.28125,12.28125l4.25,-4.25l-12.125,-12.09375c2.17969,-2.85937 3.5,-6.40234 3.5,-10.28125c0,-9.39844 -7.60156,-17 -17,-17zM21,7c7.19922,0 13,5.80078 13,13c0,7.19922 -5.80078,13 -13,13c-7.19922,0 -13,-5.80078 -13,-13c0,-7.19922 5.80078,-13 13,-13z"></path></g></g>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                @endif

                <div class="posts" id="posts-container">
                </div>

                @if ($selectedLocation) 
                    @include('frontend.gameroom.postPublishForm')
                @endif


            </div>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div id="delete-post-modal" class="modal">
    <div class="modal-content">
        <h3>Удалить сообщение</h3>
        <p>Вы уверены, что хотите удалить сообщение?</p>
        <div class="modal-buttons">
            <button id="confirm-delete" class="btn btn-danger">Удалить</button>
            <button id="cancel-delete" class="btn btn-secondary">Отмена</button>
        </div>
    </div>
</div>

<script src="{{ asset('js/post.js') }}"></script>
@endsection

