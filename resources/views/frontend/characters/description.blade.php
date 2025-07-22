@extends('frontend.characters.indexCreate')

@section('characterContent')
    <form action={{ route("characters.createDescription") }} method="POST"  enctype="multipart/form-data" style="margin-right: 5px">
        @csrf

        <input type="hidden" name="characterId" value="{{ $characterId }}">

        <!-- Биография -->
        <div class="form-control" style="margin-top: 10px">
            <label for="biography">Биография:</label>
            <textarea id="biography" name="biography" style="height: 250px;" rows="6" placeholder="Расскажите биографию персонажа..." required>{{ old('biography') }}</textarea>
        </div>

        <!-- Описание -->
        <div class="form-control" style="margin-top: 10px">
            <label for="description">Внешность:</label>
            <textarea id="description" name="description" style="height: 175px;" rows="6" placeholder="Опишите внешность персонажа..." required>{{ old('description') }}</textarea>
        </div>

        <!-- Факты -->
        <div class="form-control" style="margin-top: 10px">
            <label for="headcounts">Факты:</label>
            <textarea id="headcounts" name="headcounts" style="height: 175px;"  rows="6" placeholder="Место для коротких фактов о персонаже (необязательно)">{{ old('headcounts') }}</textarea>
        </div>

        <!-- Кнопка отправки формы -->
        <div class="mt-4" style="display: flex; justify-content: space-between;">
            <a href="{{ route('characters.index', ['id' => $characterId]) }}" style="font-family: sans-serif; text-decoration: none;" class="btn btn-primary">Назад</a>
            <button type="submit" class="btn btn-primary">Отправить на проверку</button>
        </div>

    </form>
@endsection