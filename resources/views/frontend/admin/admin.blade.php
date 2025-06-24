@extends('frontend.layout.layout')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@section('title', 'Админ-панель')

@section('content')
<div class="admin-page">
    <div class="container d-flex justify-content-center align-items-stretch"> 
        <div class="row w-100 h-100"> 
            <div class="col-md-12 admin-content d-flex flex-column justify-content-start">
                <!-- Горизонтальный выбор таблицы и строка поиска -->
                <div class="table-selection-container d-flex justify-content-between align-items-center mb-3">
                    <div class="table-selection d-flex align-items-center">
                        <ul class="table-list d-flex">
                            <li><a href={{ route('admin.showUsers') }} class="table-link {{ request()->routeIs('admin.showUsers') ? 'active' : '' }}">Пользователи</a></li>
                            <li><a href="{{ route('admin.showLocations') }}" class="table-link {{ request()->routeIs('admin.showLocations') ? 'active' : '' }}">Локации</a></li>
                            <li><a href="{{ route('admin.showLocations') }}" class="table-link {{ request()->routeIs('admin.showLocations') ? 'active' : '' }}">Темы</a></li>
                        </ul>
                    </div>
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
                </div>
                <!-- Основное содержимое (таблица) -->
                @yield('table')
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для подтверждения удаления -->
<div id="confirm-delete-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Вы уверены, что хотите удалить выбранные элементы?</p>
        <button id="confirm-delete">Удалить</button>
        <button id="cancel-delete">Отмена</button>
    </div>
</div>

<!-- Модальное окно для подтверждения бана -->
<div id="confirm-ban-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Вы уверены, что хотите забанить выбранных пользователей?</p>
        <label for="ban-reason">Причина бана:</label>
        <input type="text" id="ban-reason" placeholder="Укажите причину бана">
        <button id="confirm-ban">Забанить</button>
        <button id="cancel-ban">Отмена</button>
    </div>
</div>
@endsection