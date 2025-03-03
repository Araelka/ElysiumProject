@extends('frontend.admin.admin')
@section('title', 'Пользователи')

@section('table')
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
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