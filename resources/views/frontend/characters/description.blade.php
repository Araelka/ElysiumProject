@extends('frontend.characters.indexCreate')
@isset ($characterDescripron)
    @section('title', $characterDescripron->character->firstName . ' ' . $characterDescripron->character->secondName)
@endisset
@section('characterContent')

@isset($characterDescripron)
    <form action={{ route("characters.updateDescription", $characterId) }} method="POST"  enctype="multipart/form-data">
        @csrf
        @method('PUT')
@else
    <form action={{ route("characters.createDescription", $characterId) }} method="POST"  enctype="multipart/form-data">
        @csrf
@endisset
         <!-- Биография -->
        <div class="form-control">
            <div style="display: flex; flex-direction: row; justify-content: space-between;">
                <label for="biography">Биография:</label>
                <span id="biography-word-count">0/10000</span>
            </div>
            <textarea maxlength="10000" id="biography" name="biography" style="height: 285px;" rows="6" placeholder="Расскажите биографию персонажа..." required>{{ old('biography') ?? $characterDescripron->biography ?? '' }}</textarea>
            @error('biography')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Описание -->
        <div class="form-control" style="margin-top: 10px">
            <div style="display: flex; flex-direction: row; justify-content: space-between;">
                <label for="description">Внешность:</label>
                <span id="description-word-count">0/5000</span>
            </div>
            <textarea maxlength="5000" id="description" name="description" style="height: 175px;" rows="6" placeholder="Опишите внешность персонажа..." required>{{ old('description') ?? $characterDescripron->description ?? '' }}</textarea>
            @error('description')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Факты -->
        <div class="form-control" style="margin-top: 10px">
            <div style="display: flex; flex-direction: row; justify-content: space-between;">
                <label for="headcounts">Факты:</label>
                <span id="headcounts-word-count">0/1000</span>
            </div>
            <textarea maxlength="1000" id="headcounts" name="headcounts" style="height: 175px;"  rows="6" placeholder="Место для коротких фактов о персонаже (необязательно)">{{ old('headcounts') ?? $characterDescripron->headcounts ?? '' }}</textarea>
            @error('headcounts')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Кнопка отправки формы -->
        <div class="mt-4" style="display: flex; justify-content: space-between;">
            <a href="{{ route('characters.showCreateSkills', ['id' => $characterId]) }}" style="font-family: sans-serif; text-decoration: none;" class="btn btn-primary">Назад</a>
            <button type="submit" class="btn btn-primary">Отправить на проверку</button>
        </div>

    </form>

<script>
    function updateWordCount(personalityTextarea, wordCountElement) {
        const currentLength = personalityTextarea.value.length;
        const maxLength = personalityTextarea.getAttribute('maxlength');
        wordCountElement.textContent = `${currentLength}/${maxLength}`;
        
        if (currentLength >= maxLength * 0.9) {
            wordCountElement.style.color = '#ec7063'; 
        } else {
            wordCountElement.style.color = '#fff'; 
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const biographyTextarea = document.getElementById('biography');
        const biographyWordCountElement = document.getElementById('biography-word-count');

        const descriptionTextarea = document.getElementById('description');
        const descriptionWordCountElement = document.getElementById('description-word-count');

        const headcountsTextarea = document.getElementById('headcounts');
        const headcountsWordCountElement = document.getElementById('headcounts-word-count');

        updateWordCount(biographyTextarea, biographyWordCountElement);
        updateWordCount(descriptionTextarea, descriptionWordCountElement);
        updateWordCount(headcountsTextarea, headcountsWordCountElement);

        biographyTextarea.addEventListener('input', () => {
            updateWordCount(biographyTextarea, biographyWordCountElement);
        });

        descriptionTextarea.addEventListener('input', () => {
            updateWordCount(descriptionTextarea, descriptionWordCountElement);
        });

        headcountsTextarea.addEventListener('input', () => {
            updateWordCount(headcountsTextarea, headcountsWordCountElement);
        });
    });
</script>
@endsection