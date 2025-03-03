@extends('frontend.admin.admin')
@section('title', 'Локации')
@section('table')

<div class="table-container">
    <div class="top-menu">
        <form action="" method="GET">
            <button type="submit" class="add-button">Создать</button>
        </form>

        <form action="" method="GET">
            <button type="submit" class="delete-button">Удалить</button>
        </form>
    </div>


    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Наименование</th>
                <th>Количество записей</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
        @isset($locations)
            @foreach ($locations as $location)
            <tr>
                <td>{{ $location->id }}</td>
                <td>{{ $location->name }}</td>
                <td>{{ $location->posts_count }}</td>
                <td>
                    <a href={{ route('admin.showLocationEditForm', $location->id) }} class="edit-button">Редактировать</a>
                    <form action={{ route('admin.destroyLocation', $location->id) }} method="POST" style="display:inline;">
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