@extends('frontend.layout.layout')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@section('title', 'Вики')

@section('content')
<div class="admin-page">
    <div class="container d-flex justify-content-center align-items-stretch"> 
        <div class="row w-100 h-100"> 
            <div class="col-md-12 admin-content d-flex flex-column justify-content-start">
                 @if (Request::is('wiki'))
                <div class="theme-selection-container d-flex justify-content-between align-items-center mb-3">
                    <div class="theme-selection d-flex align-items-center">
                        @if (Auth::user()->isEditor())
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

<!-- Модальное окно для подтверждения удаления -->
<div id="confirm-delete-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Вы уверены, что хотите удалить эту тему?</p>
        <form id="delete-form" method="POST">
            @csrf
            @method('DELETE')
        </form>
        <button id="confirm-delete">Удалить</button>
        <button id="cancel-delete">Отмена</button>
    </div>
</div>

<script>
    const modal = document.getElementById("confirm-delete-modal");
    const deleteButtons = document.querySelectorAll(".delete-theme-button");
    const closeBtn = document.querySelector(".close");
    const cancelBtn = document.getElementById("cancel-delete");
    const confirmDeleteBtn = document.getElementById("confirm-delete");
    const deleteForm = document.getElementById("delete-form");

    function openModal(themeId) {
        modal.style.display = "block";
        deleteForm.setAttribute("action", `/wiki/delete/${themeId}`);
    }

    function closeModal() {
        modal.style.display = "none";
    }

    deleteButtons.forEach(button => {
        button.addEventListener("click", () => {
            const themeId = button.getAttribute("data-id");
            openModal(themeId);
        });
    });

    closeBtn.addEventListener("click", closeModal);
    cancelBtn.addEventListener("click", closeModal);

    window.addEventListener("click", (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Отправляем форму ТОЛЬКО по клику на "Удалить"
    confirmDeleteBtn.addEventListener("click", function (e) {
        e.preventDefault(); // Останавливаем стандартное поведение кнопки
        closeModal(); // Закрываем модальное окно
        deleteForm.submit(); // Принудительно отправляем форму
    });
</script>

@endsection