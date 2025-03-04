@extends('frontend.admin.admin')
@section('title', 'Пользователи')

@section('table')
<div class="table-container">
    <div class="top-menu">
        <form action="" method="GET">
            <button type="submit" class="add-button">Создать</button>
        </form>

        <form id="bulk-delete-form" action="" method="POST">
            @csrf
            <input type="hidden" name="selected_ids" id="selected-ids" value="">
            <button type="submit" class="delete-button">Забанить выбранные</button>
        </form>

        <form id="bulk-delete-form" action="" method="POST">
            @csrf
            <input type="hidden" name="selected_ids" id="selected-ids" value="">
            <button type="submit" class="delete-button">Удалить выбранные</button>
        </form>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th><input type="checkbox" id="select-all-checkbox"></th>
                <th>ID</th>
                <th>Логин</th>
                <th>Email</th>
                <th>Роль</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
        @isset($users)
            @foreach ($users as $user)
            <tr>
                <td><input type="checkbox" class="location-checkbox" data-location-id="{{ $user->id }}"></td>
                <td>{{ $user->id }}</td>
                <td>{{ $user->login }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role->name }}</td>
                <td>
                    <a href={{ route('admin.showUserEditForm', $user->id) }} class="edit-button">Редактировать</a>
                    <form action={{ route('admin.destroyUser', $user->id) }} method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete-button">Удалить</button>
                    </form>
                </td>
            </tr>
            @endforeach
        @endisset
        </tbody>
    </table>
</div>

@endsection