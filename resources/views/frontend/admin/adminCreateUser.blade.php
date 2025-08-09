@extends('frontend.admin.admin')
@section('title', 'Создание пользователя')
@section('table')
<meta name="csrf-token" content="{{ csrf_token() }}">


<div class="button-container custom-button-container">
    <form action={{ route('admin.createUser') }} method="POST" class="data-table">
        @csrf
        <div class="form-group">
            <label for="login">Логин:</label>
            <input type="text" id="login" name="login">
            @error('login')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email">
            @error('email')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password">
            @error('password')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="password_confirmation">Подтвердите пароль:</label>
            <input type="password" id="password_confirmation" name="password_confirmation">
            @error('password_confirmation')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="role">Роль:</label>
            <div class="dropdown-container">

                
                <!-- Кнопка для открытия выпадающего списка -->
                <select id="rolesDropdownToggle" readonly>
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
                                    data-role-id="{{ $role->id }}">
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

        <div class="button-group">
            <div class="left-buttons">
                <button type="batton" class="save-button" onclick="submitForm(this)">Сохранить</button>
            </div>
            </div>
        </div>
    </form>
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
        });

        document.addEventListener('click', function (event) {
            if (!dropdownMenu.contains(event.target) && !toggleButton.contains(event.target)) {
                dropdownMenu.classList.remove('open');
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

