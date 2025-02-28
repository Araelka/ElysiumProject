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
                            <li><a href="#" class="table-link">Таблица 2</a></li>
                            <li><a href="#" class="table-link">Таблица 3</a></li>
                        </ul>
                    </div>
                    <div class="search-container">
                        <input type="text" class="search-input" placeholder="Поиск...">
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
        <p>Вы уверены, что хотите удалить этот элемент?</p>
        <button id="confirm-delete">Удалить</button>
        <button id="cancel-delete">Отмена</button>
    </div>
</div>

@endsection