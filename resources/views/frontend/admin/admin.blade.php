@extends('frontend.layout.layout')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@section('title', 'Админ-панель')

@section('content')
<div class="main-page">
    <div class="container d-flex justify-content-center align-items-stretch"> 
        <div class="row w-100 h-100"> 
            <div class="col-md-12 main-content d-flex flex-column justify-content-start" style="overflow-y: visible">
                <!-- Горизонтальный выбор таблицы и строка поиска -->
                <div class="table-selection-container d-flex justify-content-between align-items-center mb-3">
                    <div class="table-selection d-flex align-items-center">
                        <ul class="table-list d-flex">
                            <li><a href="{{ route('admin.showUsers') }}" class="table-link {{ request()->routeIs('admin.showUsers') ? 'active' : '' }}">Пользователи</a></li>
                            <li><a href="{{ route('admin.showCharactersTable') }}" class="table-link {{ request()->routeIs('admin.showCharactersTable') ? 'active' : '' }}">Персонажи</a></li>
                            <li><a href="{{ route('admin.showLocations') }}" class="table-link {{ request()->routeIs('admin.showLocations') ? 'active' : '' }}">Локации</a></li>
                        </ul>
                    </div>
                    @if (!Request::is('admin/*/*'))
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Элементы для модальных окон
        const deleteModal = document.getElementById('confirm-delete-modal');
        const banModal = document.getElementById('confirm-ban-modal');

        // Общие элементы для обоих модальных окон
        const confirmDeleteButton = document.getElementById('confirm-delete');
        const cancelDeleteButton = document.getElementById('cancel-delete');
        const deleteCloseButton = deleteModal.querySelector('.close');

        const confirmBanButton = document.getElementById('confirm-ban');
        const cancelBanButton = document.getElementById('cancel-ban');
        const banCloseButton = banModal.querySelector('.close');
        const banReasonInput = document.getElementById('ban-reason');

        let currentForm = null;
        let currentUserId = null; // Для хранения ID пользователя при бане конкретного пользователя

        // Функция для открытия модального окна удаления
        function openDeleteModal(event) {
            event.preventDefault();
            deleteModal.style.display = 'block';
            currentForm = event.target.closest('form');
        }

        // Функция для закрытия модального окна удаления
        function closeDeleteModal() {
            deleteModal.style.display = 'none';
            currentForm = null;
        }

        // Функция для открытия модального окна бана
        function openBanModal(event) {
            event.preventDefault();

            // Проверяем, является ли это баном конкретного пользователя
            const banButton = event.target.closest('.ban-button');
            if (banButton && banButton.dataset.userId) {
                // Бан конкретного пользователя
                currentUserId = banButton.dataset.userId; // Сохраняем ID пользователя
            } else {
                // Массовый бан
                const selectedIdsInput = document.querySelector('[data-input-type="users-ban"]');
                if (!selectedIdsInput || !selectedIdsInput.value) {
                    alert('Не выбраны пользователи для бана');
                    return;
                }
            }

            banModal.style.display = 'block';
        }

        // Функция для закрытия модального окна бана
        function closeBanModal() {
            banModal.style.display = 'none';
            banReasonInput.value = ''; // Очищаем поле причины бана
            currentUserId = null; // Сбрасываем ID пользователя
        }

        // Обработчик события для всех кнопок отправки форм
        document.querySelectorAll('.delete-button, .ban-button').forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();

                const form = event.target.closest('form');
                const action = form.dataset.action;

                if (action === 'ban') {
                    openBanModal(event);
                } else if (action === 'delete') {
                    openDeleteModal(event);
                }
            });
        });

        // Добавляем обработчик для кнопок бана отдельных пользователей
        document.querySelectorAll('.single-ban-form .ban-button').forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                openBanModal(event);
            });
        });

        // Добавляем обработчик для кнопок удаления одного элемента
        document.querySelectorAll('.single-delete-form .delete-button').forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                openDeleteModal(event);
            });
        });

        // Обработчик события для кнопки подтверждения удаления
        confirmDeleteButton.addEventListener('click', function () {
            if (currentForm) {
                currentForm.submit();
            }
            closeDeleteModal();
        });

        // Обработчик события для кнопки отмены и крестика (удаление)
        [cancelDeleteButton, deleteCloseButton].forEach(button => {
            button.addEventListener('click', closeDeleteModal);
        });

        // Обработчик события для кнопки подтверждения бана
        confirmBanButton.addEventListener('click', function () {
            let reason = banReasonInput.value.trim(); // Получаем значение из поля ввода

            // Если поле пустое, отправляем null
            if (!reason) {
                reason = null;
            }

            if (currentUserId) {
                // Бан конкретного пользователя
                const banForm = document.querySelector(`.single-ban-form button[data-user-id="${currentUserId}"]`).closest('form');

                // Добавляем причину бана в форму как скрытое поле
                const hiddenReasonInput = document.createElement('input');
                hiddenReasonInput.type = 'hidden';
                hiddenReasonInput.name = 'ban_reason';
                hiddenReasonInput.value = reason;

                banForm.appendChild(hiddenReasonInput);
                banForm.submit(); // Отправляем форму
            } else {
                // Массовый бан
                const selectedIdsInput = document.querySelector('[data-input-type="users-ban"]');
                if (!selectedIdsInput || !selectedIdsInput.value) {
                    alert('Не выбраны пользователи для бана');
                    return;
                }

                // Добавляем причину бана в форму как скрытое поле
                const hiddenReasonInput = document.createElement('input');
                hiddenReasonInput.type = 'hidden';
                hiddenReasonInput.name = 'ban_reason';
                hiddenReasonInput.value = reason;

                const banForm = document.querySelector('#bulk-ban-form');
                banForm.appendChild(hiddenReasonInput);
                banForm.submit(); // Отправляем форму
            }

            closeBanModal();
        });

        // Обработчик события для кнопки отмены и крестика (бан)
        [cancelBanButton, banCloseButton].forEach(button => {
            button.addEventListener('click', closeBanModal);
        });
    }); 
</script>
@endsection