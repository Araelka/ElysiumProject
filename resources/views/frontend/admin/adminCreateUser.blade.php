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
            <input type="password_confirmation" id="password_confirmation" name="password_confirmation">
            @error('password_confirmation')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="role">Роль:</label>
            <select id="role" name="role_id">
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>
            @error('role_id')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="button-group">
            <div class="left-buttons">
                <button type="submit" class="save-button">Сохранить</button>
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

