@extends('frontend.wiki.index')
@section('title', 'Создание темы')
@section('table')

<h3>Создание темы</h3>

<div class="button-container custom-button-container">
    <form action="{{ route('wiki.createTheme') }}" method="POST" enctype="multipart/form-data" class="data-table">
        @csrf

        <!-- Горизонтальный блок: слева - кнопка загрузки, справа - поле ввода -->
        <div class="form-group flex-row form-group-flex">
            <!-- Слева: кнопка загрузки -->
            <div class="flex-left">
                <label for="">Изображение:</label>
                <div class="image-preview" style="margin-bottom: 10px">
                    <img id="preview-image" src="#" alt="Предпросмотр изображения" style="display: none; max-width: 100%; max-height: 200px;" />
                    <div id="placeholder-text" class="placeholder">Нажмите "Загрузить изображение"</div>
                </div>
                <label for="image" class="custom-file-upload">
                    Загрузить изображение
                </label>
                <input type="file" id="image" name="image" accept="image/*" class="hidden-input">
                @error('image')
                    <span class="form__error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Справа: поле ввода -->
            <div class="flex-right">
                <label for="name">Наименование:</label>
                <input type="text" id="name" name="name" required>
                @error('name')
                    <span class="form__error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Кнопка сохранить -->
        <div class="button-group">
            <div class="left-buttons">
                <button type="submit" class="save-button">Сохранить</button>
            </div>
        </div>
    </form>
</div>

@endsection