@extends('frontend.layout.layout')
@section('title', 'Персонажи')
<link rel="stylesheet" href="{{ asset('css/character.css') }}">



@section('content')
<div class="home-page">
    <div class="container d-flex justify-content-center align-items-stretch">
        <div class="row w-100 h-100">
            <!-- Боковая панель (20%) -->
            <div class="col-md-2 sidebar d-flex flex-column justify-content-start">
                <h3>Персонажи</h3>
                <ul class="topics-list">
                    <!-- Здесь можно добавить список персонажей -->
                </ul>
            </div>

            <!-- Основной контент (80%) -->
            <div class="col-md-10 content d-flex flex-column justify-content-start">
                <div class="character-form-container">
                    <form action="" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Блок: Фото и форма -->
                        <div class="form-layout">
                            <!-- Левый блок: Фото -->
                            <div class="photo-section">
                                <div class="image-preview" onclick="document.getElementById('photo-upload').click()">
                                    <img id="preview-image" src="#" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <input type="file" id="photo-upload" name="photo" class="hidden-input" accept="image/*" onchange="previewFile(this)">
                            </div>

                            <!-- Правый блок: Форма -->
                            <div class="form-section">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Имя:</label>
                                    <input type="text" id="name" name="name" class="form-control" placeholder="Введите имя персонажа" required>
                                </div>

                                <div class="mb-3">
                                    <label for="gender" class="form-label">Пол:</label>
                                    <select id="gender" name="gender" class="form-control" required>
                                        <option value="male">Мужской</option>
                                        <option value="female">Женский</option>
                                        <option value="other">Иное</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="age" class="form-label">Возраст:</label>
                                    <input type="number" id="age" name="age" class="form-control" placeholder="Введите возраст" min="0" required>
                                </div>

                                <div class="mb-3">
                                    <label for="race" class="form-label">Раса:</label>
                                    <input type="text" id="race" name="race" class="form-control" placeholder="Введите расу персонажа" required>
                                </div>
                            </div>
                        </div>

                        <!-- Биография -->
                        <div class="mt-4">
                            <label for="biography" class="form-label">Биография:</label>
                            <textarea id="biography" name="biography" class="form-control" rows="6" placeholder="Расскажите о персонаже..."></textarea>
                        </div>

                        <!-- Кнопка отправки формы -->
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary custom-file-upload">Создать персонажа</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Скрипт для предпросмотра изображения -->
<script>
    function previewFile(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('preview-image').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection

