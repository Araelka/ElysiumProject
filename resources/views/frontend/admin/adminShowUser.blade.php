@extends('frontend.admin.admin')
@section('title', 'Редактирование пользователя: ' . $user->login)
@section('table')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="button-container custom-button-container">
    <form action={{ route('admin.editUser', $user->id) }} method="POST" class="data-table">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="login">Логин:</label>
            <input type="text" id="login" name="login" value="{{ $user->login }}" readonly>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="{{ $user->email }}">
            @error('email')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="role">Роль:</label>            
            
           <div class="dropdown-container">

                
                <!-- Кнопка для открытия выпадающего списка -->
                <select class="dropdown-toggle" id="rolesDropdownToggle" readonly>
                    <option id="selectedOption"></option>
                </select>

                <!-- Выпадающий список с чекбоксами -->
                <div class="dropdown-menu" id="rolesDropdownMenu">
                    @foreach ($roles as $role)
                        <div class="form-check">
                            <div>
                                <input 
                                    type="checkbox" 
                                    class="form-check-input role-checkbox" 
                                    id="role_{{ $role->id }}" 
                                    data-role-id="{{ $role->id }}"
                                    {{ in_array($role->id, $user->roles->pluck('id')->toArray()) ? 'checked' : '' }}
                                >
                            </div>
                            <div>
                                <label class="form-check-label" for="role_{{ $role->id }}">
                                    {{ $role->name }}
                                </label>
                            </div>
        
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Скрытый input для отправки данных -->
            <input type="hidden" name="roles[]" id="selectedRoles">



            @error('role_id')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>

        @if ($user->is_banned)
            <div class="form-group">
                <label for="ban-reason">Причина бана:</label>
                <input type="text" id="ban-reason" name="ban-reason" value="{{ $user->ban_reason }}" readonly>
            </div>
        @endif
        <div class="button-group">
            <div class="left-buttons">
                <button type="submit" class="save-button">Сохранить изменения</button>
            </div>
        </form>
            <div class="right-buttons">
                <form  action={{ route('admin.resetPassword', $user->id) }} method="POST" style="display:inline-block;">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="reset-password-button">Сбросить пароль</button>
                </form>

                @if ($user->is_banned) 
                    <form action="{{ route('admin.userUnban', $user->id) }}" method="POST" class="single-unban-form" style="display:inline-block;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="unban-button">Разбанить</button>
                    </form>
                @else
                    <form action="{{ route('admin.userBan', $user->id) }}" method="POST" class="single-ban-form" style="display:inline-block;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="ban-button" data-user-id="{{ $user->id }}">Забанить</button>
                    </form>
                @endif

                <form action="{{ route('admin.destroyUser', $user->id) }}" method="POST" class="single-delete-form" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete-button">Удалить</button>
                </form>
            </div>
        </div>
</div>

{{-- Модальное окно для сброса пароля --}}
<div id="reset-password-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Новый пароль:</p>
        <p id="new-password">{{ session('newPassword') }}</p>
        <button id="close-reset-password-modal">Закрыть</button>
    </div>
</div>

<script>

    document.addEventListener('DOMContentLoaded', function () {
        const toggleButton = document.getElementById('rolesDropdownToggle');
        const selectedOption = document.getElementById('selectedOption');
        const dropdownMenu = document.getElementById('rolesDropdownMenu');
        const checkboxes = document.querySelectorAll('.role-checkbox');
        const selectedRolesInput = document.getElementById('selectedRoles');

        toggleButton.addEventListener('mousedown', function (event) {
            event.preventDefault(); 
            dropdownMenu.classList.toggle('open'); 
            toggleButton.classList.toggle('active');
        });

        document.addEventListener('click', function (event) {
            if (!dropdownMenu.contains(event.target) && !toggleButton.contains(event.target)) {
                dropdownMenu.classList.remove('open');
                toggleButton.classList.remove('active');
            }
        });

        // Обработка изменения чекбоксов
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const selectedRoles = Array.from(checkboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.dataset.roleId);

                // Обновляем текст select
                selectedOption.textContent = selectedRoles.length > 0
                    ? `Выбрано ${selectedRoles.length}`
                    : 'Выберите роли';

                // Обновляем скрытое поле
                selectedRolesInput.value = selectedRoles.join(',');
            });
        });

        const initialSelectedRoles = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.dataset.roleId);
        selectedOption.textContent = initialSelectedRoles.length > 0
            ? `Выбрано ${initialSelectedRoles.length}`
            : 'Выберите роли';
        selectedRolesInput.value = initialSelectedRoles.join(',');
    });

    document.addEventListener('DOMContentLoaded', function() {
        const newPassword = "{{ session('newPassword') }}";
        const resetPasswordModal = document.getElementById('reset-password-modal');
        const closeModal = document.querySelector('.close');
        const closeResetPasswordModal = document.getElementById('close-reset-password-modal');

        if (newPassword) {
            resetPasswordModal.style.display = 'block';
        }

        // Закрываем модальное окно при нажатии на крестик
        if (closeModal) {
            closeModal.addEventListener('click', function() {
                resetPasswordModal.style.display = 'none';
            });
        }

        // Закрываем модальное окно при нажатии на кнопку "Закрыть"
        if (closeResetPasswordModal) {
            closeResetPasswordModal.addEventListener('click', function() {
                resetPasswordModal.style.display = 'none';
            });
        }

        // Закрываем модальное окно при клике вне его области
        window.addEventListener('click', function(event) {
            if (event.target == resetPasswordModal) {
                resetPasswordModal.style.display = 'none';
            }
        });
    });
</script>
@endsection

