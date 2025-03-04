@extends('frontend.admin.admin')
@section('title', 'Редактирование локации: ' . $location->name)
@section('table')

<div class="button-container custom-button-container">
    <form action="{{ route ('admin.editLocation', $location->id) }}" method="POST" class="data-table">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="login">Наименование:</label>
            <input type="text" id="name" name="name" value="{{ $location->name }}">
            @error('name')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="button-group">
            <div class="left-buttons">
                <button type="submit" class="save-button">Сохранить изменения</button>
            </div>
        </form>
            <div class="right-buttons">
                <form action="{{ route('admin.destroyLocation', $location->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete-button">Удалить</button>
                </form>
            </div>
        </div>
</div>

@endsection

