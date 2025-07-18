@extends('frontend.characters.indexCreate')

@section('characterContent')
    <form action={{ route("characters.create") }} method="POST"  enctype="multipart/form-data" style="margin-right: 5px">
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
                <div class="form-control" style="display: flex; flex-direction: row; justify-content: space-between; gap: 10px;">
                    <div style="width: 50%">
                        <label for="firstName" class="form-label">Имя:</label>
                        <input type="text" id="firstName" name="firstName" placeholder="Введите имя" required>
                    </div>

                    <div style="width: 50%">
                        <label for="secondName" class="form-label">Фамилия:</label>
                        <input type="text" id="secondName" name="secondName" placeholder="Введите фамилию" required>
                    </div>
                    
                </div>

                <div class="form-control">
                    <label for="age" >Возраст:</label>
                    <input type="number" id="age" name="age"  placeholder="Введите возраст" min="0" required>
                </div>

                <div class="form-control">
                    <label for="gender">Пол:</label>
                    <select id="gender" name="gender"  required>
                        <option value="Мужской">Мужской</option>
                        <option value="Женский">Женский</option>
                    </select>
                </div>

                <div class="form-control">
                    <label for="nationality">Национальность:</label>
                    <select id="nationality" name="nationality"  required>
                        <option value="Граад">Граад</option>
                        <option value="Иилмараа">Иилмараа</option>
                        <option value="Инсулинда">Инсулинда</option>
                        <option value="Катла">Катла</option>
                        <option value="Мунди">Мунди</option>
                        <option value="Самара">Самара</option>
                        <option value="Семенин">Семенин</option>
                        <option value="Сеол">Сеол</option>
                    </select>
                </div>

            </div>
        </div>

        <div class="form-control" style="margin-top: 10px">
            <label for="residentialAddress" class="form-label">Адрес проживания:</label>
            <input type="text" id="residentialAddress" name="residentialAddress" placeholder="Введите адрес проживания" required>
        </div>

        <div class="form-control" style="margin-top: 10px">
            <label for="activity" class="form-label">Род деятельности:</label>
            <input type="text" id="activity" name="activity" placeholder="Введите род деятельности" required>
        </div>

         <!-- Характер -->
        <div class="form-control" style="margin-top: 10px">
            <label for="personality">Характер:</label>
            <textarea id="personality" name="personality"  rows="6" placeholder="Расскажите о характере..." style="height: 225px" required></textarea>
        </div>

        {{-- <!-- Биография -->
        <div class="form-control" style="margin-top: 10px">
            <label for="biography">Биография:</label>
            <textarea id="biography" name="biography"  rows="6" placeholder="Расскажите о персонаже..." required></textarea>
        </div>

        <!-- Описание -->
        <div class="form-control" style="margin-top: 10px">
            <label for="description">Описание:</label>
            <textarea id="description" name="description"  rows="6" placeholder="Опишите персонажа..." required></textarea>
        </div> --}}

        <!-- Кнопка отправки формы -->
        <div class="mt-4" style="display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary">Далее</button>
        </div>
    </form>

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