@extends('frontend.admin.admin')
@section('title', 'Пользователи')

@section('table')
<div class="top-menu">
    <!-- Левая часть -->
    <div class="top-menu-left">
        <form action={{ route('admin.showUsers') }} method="GET" id="filter-form">
            {{-- <input type="hidden" name="search" value="{{ request('search') }}"> --}}
            <button type="submit" name="filter" value="all" class="filter-button {{ request('filter') === 'all' || !request('filter') ? 'active' : '' }}">Все</button>
            <button type="submit" name="filter" value="active" class="filter-button {{ request('filter') === 'active' ? 'active' : '' }}">Активные</button>
            <button type="submit" name="filter" value="banned" class="filter-button {{ request('filter') === 'banned' ? 'active' : '' }}">Забаненные</button>
        </form>
    </div>

    <!-- Правая часть -->
    <div class="top-menu-right">
        <form action={{ route('admin.showCreateUserForm') }} method="GET">
            <button type="submit" class="add-button">Создать</button>
        </form>

        <form id="bulk-ban-form" action={{ route('admin.bulkUserBan') }} method="POST" data-action="ban">
            @csrf
            @method('PUT')
            <input type="hidden" name="selected_ids" data-input-type="users-ban" value="">
            <button type="submit" class="ban-button">Забанить выбранные</button>
        </form>

        <form id="bulk-delete-form" action={{ route('admin.bulkUserDestroy') }} method="POST" data-action="delete">
            @csrf
            <input type="hidden" name="selected_ids" data-input-type="users-delete" value="">
            <button type="submit" class="delete-button">Удалить выбранные</button>
        </form>
    </div>
</div>
<div class="table-container">
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
                <td><input type="checkbox" class="bulk-checkbox" data-bulk-id="{{ $user->id }}"></td>
                <td>{{ $user->id }}</td>
                <td>{{ $user->login }}</td>
                <td>{{ $user->email }}</td>
                <td>
                @foreach ($user->roles as $role)
                    {{ $role->name }}
                @endforeach
                </td>
                <td>
                    <a href={{ route('admin.showUserEditForm', $user->id) }} class="edit-button">Редактировать</a>
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

                    {{-- <form action="{{ route('admin.destroyUser', $user->id) }}" method="POST" class="single-delete-form" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete-button">Удалить</button>
                    </form>
                </td> --}}
            </tr>
            @endforeach
        @endisset
        </tbody>
    </table>
</div>

<div class="pagination-container">
    {{ $users->links('vendor.pagination.default') }}
</div>
@endsection
