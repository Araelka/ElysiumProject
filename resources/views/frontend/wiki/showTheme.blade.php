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
                <div class="image-preview" onclick="triggerFileInput()">
                    <img id="preview-image" src="#" style="display: none;"/>
                    <div id="placeholder-text" class="placeholder">Загрузить изображение</div>
                </div>
            </div>

            <!-- Справа: поля ввода и кнопки -->
            <div class="flex-right">
                <!-- Поле ввода названия -->
                <div class="form-group-theme-labbe">
                    <label for="name">Наименование:</label>
                    <input type="text" id="name" name="name" required>
                    <input type="file" id="image" name="image" accept="image/*" class="hidden-input" hidden>
                    @error('name')
                        <span class="form__error">{{ $message }}</span>
                    @enderror
                </div>


                <!-- Кнопка сохранить -->
                <div class="form-group-theme-batton">
                    <button type="batton" class="save-button" onclick="submitForm(this)">Сохранить</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // Функция для открытия диалога выбора файла при клике на предпросмотр
    function triggerFileInput() {
        document.getElementById('image').click();
    }

    // Функция для предпросмотра выбранного изображения
    function previewFile(input) {
        const file = input.files[0];
        const previewImage = document.getElementById('preview-image');
        const placeholderText = document.getElementById('placeholder-text');

        if (file) {
            const reader = new FileReader();

            reader.onload = function (e) {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
                placeholderText.style.display = 'none';
            };

            reader.readAsDataURL(file);
        } else {
            // Если файл не выбран, сбрасываем интерфейс
            previewImage.src = '';
            previewImage.style.display = 'none';
            placeholderText.style.display = 'block';
        }
    }

    document.getElementById('image').addEventListener('change', function(event) {
        const input = event.target;
        const previewImage = document.getElementById('preview-image');
        const placeholderText = document.getElementById('placeholder-text');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
                placeholderText.style.display = 'none';
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            previewImage.src = '#';
            previewImage.style.display = 'none';
            placeholderText.style.display = 'block';
        }
    });
</script>

@endsection