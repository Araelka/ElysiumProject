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
            <select id="role" name="role_id">
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}> 
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            @error('role_id')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>
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
                <form action="{{ route('admin.destroyUser', $user->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete-button">Удалить</button>
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

