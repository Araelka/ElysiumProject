@extends('frontend.admin.admin')
@section('title', 'Создание локации')
@section('table')

<div class="button-container custom-button-container">
    <form action={{ route('admin.createLocation') }} method="POST" class="data-table">
        @csrf
        <div class="form-group">
            <label for="name">Наименование:</label>
            <input type="text" id="name" name="name">
            @error('name')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="button-group">
            <div class="left-buttons">
                <button type="batton" class="save-button" onclick="submitForm(this)">Сохранить</button>
            </div>
    </form>
        </div>
</div>

@endsection

