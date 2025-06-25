@extends('frontend.wiki.index')
@section('title', 'Создание темы')
@section('table')

<h3>Создание темы</h3>

<div class="button-container custom-button-container">
    <form action="{{ route('wiki.createTheme') }}" method="POST" enctype="multipart/form-data" class="data-table">
        @csrf

        <!-- Горизонтальный блок: слева - предпросмотр, справа - поля ввода -->
        <div class="form-group flex-row form-group-flex">
            <!-- Слева: предпросмотр изображения -->
            <div class="flex-left">
                <div class="image-preview">
                    <img id="preview-image" src="#"/>
                    <div id="placeholder-text" class="placeholder">Предпросмотр изображение</div>
                </div>
            </div>

            <!-- Справа: поля ввода и кнопки -->
            <div class="flex-right">
                <!-- Поле ввода названия -->
                <div class="form-group-theme-labbe">
                    <label for="name">Наименование:</label>
                    <input type="text" id="name" name="name" required>
                    @error('name')
                        <span class="form__error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Кнопка загрузки изображения -->
                <div class="form-group-theme">
                    <label for="image" class="custom-file-upload">
                        Загрузить изображение
                    </label>
                    <input type="file" id="image" name="image" accept="image/*" class="hidden-input">
                    @error('image')
                        <span class="form__error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Кнопка сохранить -->
                <div class="form-group-theme-batton">
                    <button type="submit" class="save-button">Сохранить</button>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection