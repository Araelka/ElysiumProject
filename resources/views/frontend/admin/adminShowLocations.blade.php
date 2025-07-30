@extends('frontend.admin.admin')
@section('title', 'Локации')
@section('table')


<div class="top-menu-right">
    <form action={{ route('admin.showLocationCreateForm') }} method="GET">
        <button type="submit" class="add-button">Создать</button>
    </form>
    <form id="bulk-delete-form" action={{ route('admin.bulkDestroyLocation') }} method="POST" data-action="delete">
        @csrf
        <input type="hidden" name="selected_ids" data-input-type="location-delete" value="">
        <button type="submit" class="delete-button">Удалить выбранные</button>
    </form>
</div>
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th><input type="checkbox" id="select-all-checkbox"></th>
                <th>ID</th>
                <th>Наименование</th>
                <th>Количество постов</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
        @isset($locations)
            @foreach ($locations as $location)
            <tr>
                <td><input type="checkbox" class="bulk-checkbox" data-bulk-id="{{ $location->id }}"></td>
                <td>{{ $location->id }}</td>
                <td>{{ $location->name }}</td>
                <td>{{ $location->posts_count }}</td>
                <td>
                    <a href={{ route('admin.showLocationEditForm', $location->id) }} class="edit-button">Редактировать</a>
                    <form action="{{ route('admin.destroyLocation', $location->id) }}" method="POST" class="single-delete-form" style="display:inline-block;">
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
    {{ $locations->links('vendor.pagination.default') }}
</div>
@endsection
