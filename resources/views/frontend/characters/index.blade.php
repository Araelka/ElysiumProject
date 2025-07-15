@extends('frontend.layout.layout')
@section('title', 'Персонажи')
<link rel="stylesheet" href="{{ asset('css/character.css') }}">



@section('content')
<div class="double-page">
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
                    <form action="" method="POST"  enctype="multipart/form-data" style="margin-right: 5px">
                        @csrf

                        <!-- Блок: Фото и форма -->
                        <div class="form-layout">
                            <!-- Левый блок: Фото -->
                            <div class="photo-section">
                                <div class="form-control">
                                    <label >Создание персонажа</label>
                                </div>
                                <div class="image-preview" onclick="document.getElementById('photo-upload').click()">
                                    <img id="preview-image" src="#" class="rounded-circle">
                                    <div id="placeholder-text" class="placeholder">Загрузить изображение</div>
                                </div>
                                <input type="file" id="photo-upload" name="photo" class="hidden-input" accept="image/*" onchange="previewFile(this)">
                                <div class="mt-4">
                        </div>
                            </div>

                            <!-- Правый блок: Форма -->
                            <div class="form-section">
                                <div class="form-control">
                                    <label for="name" class="form-label">Имя:</label>
                                    <input type="text" id="name" name="name" placeholder="Введите имя" required>
                                </div>

                                <div class="form-control">
                                    <label for="gender">Пол:</label>
                                    <select id="gender" name="gender"  required>
                                        <option value="male">Мужской</option>
                                        <option value="female">Женский</option>
                                    </select>
                                </div>

                                <div class="form-control">
                                    <label for="age" >Возраст:</label>
                                    <input type="number" id="age" name="age"  placeholder="Введите возраст" min="0" required>
                                </div>

                                <div class="form-control">
                                    <label for="nationality">Национальность:</label>
                                    <select id="nationality" name="nationality"  required>
                                        <option value="Graad">Граад</option>
                                        <option value="Iilmaraa">Иилмараа</option>
                                        <option value="Insulinde">Инсулинда</option>
                                        <option value="Katla">Катла</option>
                                        <option value="Mundi">Мунди</option>
                                        <option value="Samara">Самара</option>
                                        <option value="Semenine">Семенин</option>
                                        <option value="Seol">Сеол</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <!-- Биография -->
                        <div class="form-control" style="margin-top: 15px">
                            <label for="biography">Биография:</label>
                            <textarea id="biography" name="biography"  rows="6" placeholder="Расскажите о персонаже..."></textarea>
                        </div>

                        <div class="form-control" style="margin-top: 15px">
                            <div class="attributes">
                                @foreach ($attributes as $attribute)

                                <div class="attribute mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>{{ $attribute->name }}</span>
                                            <div>
                                                <button type="button" class="btn btn-sm btn-secondary" onclick="decreaseAttribute('{{ $attribute->id }}')">-</button>
                                                <span id="attribute-value-{{ $attribute->id }}">{{ $attribute->min_value }}</span>
                                                <button type="button" class="btn btn-sm btn-secondary" onclick="increaseAttribute('{{ $attribute->id }}')">+</button>
                                            </div>
                                        </div>
                                    </div>

                                <div class="slills">
                                    @foreach ($attribute->skills as $skill)
                                        <div class="skill">
                                            <div class="skill-content">
                                                <div class="d-flex justify-content-between align-items-center space-between">
                                                    <div class="skill-content-name">
                                                        <span>{{ $skill->name }}</span>
                                                        <span id="attribute-value-{{ $attribute->id }}">{{ $attribute->min_value}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                                @endforeach
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

<script>
    let isDragging = false;
    let startX, startY, currentX = 0, currentY = 0, scale = 1;

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

                // Добавляем обработчики для масштабирования и перемещения
                setupImageInteractions(previewImage);
            };

            reader.onerror = function () {
                alert('Ошибка при чтении файла.');
            };

            reader.readAsDataURL(file);
        } else {
            previewImage.src = '';
            previewImage.style.display = 'none';
            placeholderText.style.display = 'block';
        }
    }
</script>
@endsection

