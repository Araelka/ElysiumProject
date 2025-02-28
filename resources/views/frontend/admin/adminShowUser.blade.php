@extends('frontend.admin.admin')
@section('title', 'Редактирование пользователя: ' . $user->login)
@section('table')
<div class="button-container custom-button-container">
    <form action="" method="POST" class="data-table">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="login">Логин:</label>
            <input type="text" id="login" name="login" value="{{ $user->login }}">
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="{{ $user->email }}">
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
        </div>
        <div class="button-group">
            <div class="left-buttons">
                <button type="submit" class="save-button">Сохранить изменения</button>
            </div>
            <div class="right-buttons">
                <form action="" method="POST" style="display:inline-block;">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="reset-password-button">Сбросить пароль</button>
                </form>
                <form action="{{ route('admin.destroy', $user->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete-button">Удалить</button>
                </form>
            </div>
        </div>
    </form>
</div>
@endsection