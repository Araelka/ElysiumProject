@extends('frontend.layout.layout')

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
                        <li><a href="{{ route('gameroom.index', ['location_id' => $location->id]) }}" 
                            class="topic-link {{ $selectedLocation && $selectedLocation->id == $location->id ? 'active' : '' }}"
                            style="display: flex; flex-direction: row; align-items: center; justify-content: space-between;">
                             {{ $location->name }}

                              @if (isset($unreadCounts[$location->id]) && $unreadCounts[$location->id] > 0)
                                <span class="unread-count-badge">{{ $unreadCounts[$location->id] }}</span>
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
                    <div>
                        <form action="{{ request()->fullUrlWithQuery(['search' => '']) }}" method="GET" id="search-form">
                            <input type="hidden" name="filter" class="search-input">
                            <div class="search-input-wrapper">
                                <input 
                                    type="text" 
                                    name="search" 
                                    id="search-input" 
                                    value="{{ request('search') }}" 
                                    placeholder="Поиск..." 
                                    class="search-input"
                                >

                                <button type="button" id="clear-search" class="clear-search-button" style="display: none">×</button>
                            </div>
                            <button type="submit" class="search-button">Найти</button>
                        </form>
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

