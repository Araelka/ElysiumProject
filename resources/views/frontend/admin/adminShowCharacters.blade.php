@extends('frontend.admin.admin')
@section('title', 'Персонажи')
@section('table')


<div class="top-menu">
<!-- Левая часть -->
<div class="top-menu-left">
    <form action={{ route('admin.showCharactersTable') }} method="GET" id="filter-form">
        {{-- <input type="hidden" name="search" value="{{ request('search') }}"> --}}
        <button type="submit" name="filter" value="all" class="filter-button {{ request('filter') === 'all' || !request('filter') ? 'active' : '' }}">Все</button>
        <button type="submit" name="filter" value="approved" class="filter-button {{ request('filter') === 'approved' ? 'active' : '' }}">Одобренные</button>
        <button type="submit" name="filter" value="consideration" class="filter-button {{ request('filter') === 'consideration' ? 'active' : '' }}">На рассмотрении</button>
        <button type="submit" name="filter" value="preparing" class="filter-button {{ request('filter') === 'preparing' ? 'active' : '' }}">В работе</button>
        <button type="submit" name="filter" value="rejected" class="filter-button {{ request('filter') === 'rejected' ? 'active' : '' }}">Отклонённые</button>
        <button type="submit" name="filter" value="archive" class="filter-button {{ request('filter') === 'archive' ? 'active' : '' }}">В архиве</button>
    </form>
</div>

<div class="top-menu-right">
    <form id="bulk-delete-form" action={{ route('admin.bulkDestroyLocation') }} method="POST" data-action="delete">
        @csrf
        <input type="hidden" name="selected_ids" data-input-type="location-delete" value="">
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
                <th>Игрок</th>
                <th>ФИО</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
        @isset($characters)
            @foreach ($characters as $character)
            <tr>
                <td><input type="checkbox" class="bulk-checkbox" data-bulk-id="{{ $character->id }}"></td>
                <td>{{ $character->id }}</td>
                <td>{{ $character->user->login }}</td>
                <td>{{ $character->firstName . ' ' . $character->secondName }}</td>
                <td>{{ $character->status->name }}</td>
                <td>
                    <a href={{ route('admin.showLocationEditForm', $character->id) }} class="edit-button">Редактировать</a>
                    <form action="{{ route('admin.destroyLocation', $character->id) }}" method="POST" class="single-delete-form" style="display:inline-block;">
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
<div class="pagination-container">
    {{ $characters->links('vendor.pagination.default') }}
</div>
@endsection
