@extends('frontend.characters.indexCreate')

@section('characterContent')
    <form action={{ route("characters.create") }} method="POST"  enctype="multipart/form-data" style="margin-right: 5px">
        @csrf
        
        <!-- Биография -->
        <div class="form-control" style="margin-top: 10px">
            <label for="biography">Биография:</label>
            <textarea id="biography" name="biography" style="height: 250px;" rows="6" placeholder="Расскажите биографию персонажа..." required></textarea>
        </div>

        <!-- Описание -->
        <div class="form-control" style="margin-top: 10px">
            <label for="description">Внешность:</label>
            <textarea id="description" name="description" style="height: 175px;" rows="6" placeholder="Опишите внешность персонажа..." required></textarea>
        </div>

        <!-- Факты -->
        <div class="form-control" style="margin-top: 10px">
            <label for="description">Факты:</label>
            <textarea id="description" name="description" style="height: 175px;"  rows="6" placeholder="Место для коротких фактов о персонаже (необязательно)" required></textarea>
        </div>

        <!-- Кнопка отправки формы -->
        <div class="mt-4" style="display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary">Отправить на проверку</button>
        </div>

    </form>
@endsection