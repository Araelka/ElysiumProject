@extends('frontend.wiki.index')
@section('title', 'Создание темы')
@section('table')


<h3>Создание темы</h3>


<div class="button-container custom-button-container">
    <form action={{ route('wiki.createTheme') }} method="POST" class="data-table">
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
                <button type="submit" class="save-button">Сохранить</button>
            </div>
    </form>
        </div>
</div>

@endsection

