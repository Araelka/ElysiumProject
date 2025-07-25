@extends('frontend.characters.indexCreate')

@section('characterContent')
    @isset($character)
        <form action={{ route('characters.updateMainInfo', $character->uuid) }} method="POST"  enctype="multipart/form-data" style="margin-right: 5px">
            @csrf
            @method('PUT')
    @else
        <form action={{ route("characters.createMainInfo") }} method="POST"  enctype="multipart/form-data" style="margin-right: 5px">
            @csrf
    @endisset

    
        <!-- Блок: Фото и форма -->
        <div class="form-layout">
            <!-- Левый блок: Фото -->
            <div class="photo-section">
                <div class="form-control">
                    <label >Создание персонажа</label>
                </div>
                <div class="image-preview" style="width: 200px; height: 265px; ;" onclick="document.getElementById('photo-upload').click()">
                    @if (session('temp_photo_path'))
                        <img id="preview-image" src="{{ asset('storage/' . session('temp_photo_path')) }}" class="rounded-circle">
                        <div id="placeholder-text" class="placeholder">Изменить изображение</div>
                    @isset($character)
                        @elseif ($character->images->first())
                        <img id="preview-image" src="{{ asset('storage/' . $character->images->first()->path) }}" class="rounded-circle">
                        <div id="placeholder-text" class="placeholder">Изменить изображение</div>
                    @endisset
                    @else
                        <img id="preview-image" src="#" class="rounded-circle" style="display: none;">
                        <div id="placeholder-text" class="placeholder">Загрузить изображение</div>
                    @endif
                </div>
                <input type="file" id="photo-upload" name="image" class="hidden-input" accept="image/*" onchange="previewFile(this)">
            </div>

            <!-- Правый блок: Форма -->
            <div class="form-section">
                <div class="form-control" style="display: flex; flex-direction: row; justify-content: space-between; gap: 10px;">
                    <div style="width: 50%">
                        <label for="firstName" class="form-label">Имя:</label>
                        <input type="text" id="firstName" name="firstName" placeholder="Введите имя" value='{{ old('firstName') ?? $character->firstName ?? '' }}' required>
                        @error('firstName')
                            <span class="form__error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="width: 50%">
                        <label for="secondName" class="form-label">Фамилия:</label>
                        <input type="text" id="secondName" name="secondName" placeholder="Введите фамилию" value='{{ old('secondName') ?? $character->secondName ?? '' }}' required>
                        @error('secondName')
                            <span class="form__error">{{ $message }}</span>
                        @enderror
                    </div>
                    
                </div>

                <div class="form-control" style="display: flex; flex-direction: row; justify-content: space-between; gap: 10px;">
                    <div style="width: 50%">
                        <label for="age" >Возраст:</label>
                        <input type="number" id="age" name="age"  placeholder="Введите возраст" min="0" value='{{ old('age') ?? $character->age ?? ''}}' required>
                        @error('age')
                            <span class="form__error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="width: 50%">
                        <label for="gender">Пол:</label>
                        <select id="gender" name="gender" required>
                            <option value="Мужской" {{ (old('gender') ?? $character->gender ?? '') === 'Мужской' ? 'selected' : '' }}>Мужской</option>
                            <option value="Женский" {{ (old('gender') ?? $character->gender ?? '') === 'Женский' ? 'selected' : '' }}>Женский</option>
                        </select>
                    </div>
                </div>

                <div class="form-control" style="display: flex; flex-direction: row; justify-content: space-between; gap: 10px;">
                    <div style="width: 50%">
                        <label for="age" >Рост:</label>
                        <input type="number" id="height" name="height"  placeholder="Введите рост" min="0" value='{{ old('height') ?? $character->height ?? ''}}' required>
                        @error('height')
                            <span class="form__error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="width: 50%">
                        <label for="age" >Вес:</label>
                        <input type="number" id="weight" name="weight"  placeholder="Введите вес" min="0" value='{{ old('weight') ?? $character->weight ?? ''}}' required>
                        @error('weight')
                            <span class="form__error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-control">
                    <label for="nationality">Национальность:</label>
                    <select id="nationality" name="nationality"  required>
                        <option value="Граад" {{ (old('nationality') ?? $character->nationality ?? '') === 'Граад' ? 'selected' : '' }}>Граад</option>
                        <option value="Иилмараа" {{ (old('nationality') ?? $character->nationality ?? '') === 'Иилмараа' ? 'selected' : '' }}>Иилмараа</option>
                        <option value="Инсулинда" {{ (old('nationality') ?? $character->nationality ?? '') === 'Инсулинда' ? 'selected' : '' }}>Инсулинда</option>
                        <option value="Катла" {{ (old('nationality') ?? $character->nationality ?? '') === 'Катла' ? 'selected' : '' }}>Катла</option>
                        <option value="Мунди" {{ (old('nationality') ?? $character->nationality ?? '') === 'Мунди' ? 'selected' : '' }}>Мунди</option>
                        <option value="Самара" {{ (old('nationality') ?? $character->nationality ?? '') === 'Самара' ? 'selected' : '' }}>Самара</option>
                        <option value="Семенин" {{ (old('nationality') ?? $character->nationality ?? '') === 'Семенин' ? 'selected' : '' }}>Семенин</option>
                        <option value="Сеол" {{ (old('nationality') ?? $character->nationality ?? '') === 'Сеол' ? 'selected' : '' }}>Сеол</option>
                    </select>
                </div>

            </div>
        </div>

        <!-- Нижний блок -->
        <div class="form-control" style="margin-top: 10px">
            <label for="residentialAddress" class="form-label">Адрес проживания:</label>
            <input type="text" id="residentialAddress" name="residentialAddress" placeholder="Введите адрес проживания"  value='{{ old('residentialAddress') ?? $character->residentialAddress ?? ''}}'  required>
            @error('residentialAddress')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-control" style="margin-top: 10px">
            <label for="activity" class="form-label">Род деятельности:</label>
            <input type="text" id="activity" name="activity" placeholder="Введите род деятельности" value='{{ old('activity') ?? $character->activity ?? ''}}'  required>
            @error('activity')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Характер -->
        <div class="form-control" style="margin-top: 10px">
            <label for="personality">Характер:</label>
            <textarea id="personality" name="personality"  rows="6" placeholder="Расскажите о характере..." style="height: 225px"  required>{{ old('personality') ?? $character->personality ?? ''}}</textarea>
            @error('personality')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>

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
                // Отображаем изображение
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
                placeholderText.style.display = 'none';
            };

            reader.onerror = function () {
                alert('Ошибка при чтении файла.');
                // Сбрасываем интерфейс
                previewImage.src = '';
                previewImage.style.display = 'none';
                placeholderText.style.display = 'block';
            };

            reader.readAsDataURL(file);
        } else {
            // Если файл не выбран, сбрасываем интерфейс
            previewImage.src = '';
            previewImage.style.display = 'none';
            placeholderText.style.display = 'block';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const tempPhotoPath = "{{ session('temp_photo_path') }}";
        @isset($character)
            const photoPath = "{{ $character->images->first()?->path }}"
        @endisset
        const previewImage = document.getElementById('preview-image');
        const placeholderText = document.getElementById('placeholder-text');

        if (tempPhotoPath) {
            // Если есть временный файл, отображаем его
            previewImage.src = "{{ asset('storage/' . session('temp_photo_path')) }}";
            previewImage.style.display = 'block';
            placeholderText.style.display = 'none';
        } 
        @isset($character)
            else if (photoPath) {
                previewImage.src = "{{ asset('storage/' . $character->images->first()?->path) }}";
                previewImage.style.display = 'block';
                placeholderText.style.display = 'none';
            }
        @endisset
        
        else {
            // Если временного файла нет, показываем placeholder
            previewImage.style.display = 'none';
            placeholderText.style.display = 'block';
        }
    });

    window.addEventListener('beforeunload', function () {
    const tempPhotoPath = "{{ session('temp_photo_path') }}";

    if (tempPhotoPath) {
        fetch('/delete-temp-file', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ path: tempPhotoPath }),
        });
    }
});
</script>
@endsection