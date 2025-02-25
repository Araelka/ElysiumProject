@extends('frontend.layout.layout')
@section('title', 'Вход')

@section('content')
<div class="login-page">
<div class="login-container">
<h1 class="login-title">Вход</h1>

@error('identifier')
    <span class="register-form__error">{{ $message }}</span>
@enderror
<form class="login-form" action={{ route('login') }} method="POST">
@csrf
<div class="login-form__group">
<label for="identifier" class="login-form__label">Логин/email</label>
<input type="text" id="identifier" name="identifier" class="login-form__input" required>

</div>
<div class="login-form__group">
<label for="password" class="login-form__label">Пароль</label>
<input type="password" id="password" name="password" class="login-form__input" required>
</div>
<button type="submit" class="login-form__button">Войти</button>

<!-- Кнопка регистрации -->
<div class="register-link">
    <p>Нет аккаунта? <a href={{route('register')}} class="register-link__button">Зарегистрироваться</a></p>
</div>
</div>
</form>
</div>
</div>
@endsection
