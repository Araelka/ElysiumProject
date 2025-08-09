@extends('frontend.layout.layout')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@section('title', 'Регистрация')

@section('content')
<div class="register-page">
    <div class="register-container">
        <h1 class="register-title">Регистрация</h1>
        <form class="register-form" action={{route('register')}} method="POST">
            @csrf
            <div class="register-form__group">
                <label for="login" class="register-form__label">Логин</label>
                <input type="text" id="login" name="login" class="register-form__input"  value='{{ old('login') }}'>
                @error('login')
                    <span class="form__error">{{ $message }}</span>
                @enderror
            </div>
            <div class="register-form__group">
                <label for="email" class="register-form__label">Email</label>
                <input type="email" id="email" name="email" class="register-form__input" value='{{ old('email') }}'>
                @error('email')
                    <span class="form__error">{{ $message }}</span>
                @enderror
            </div>
            <div class="register-form__group">
                <label for="password" class="register-form__label">Пароль</label>
                <input type="password" id="password" name="password" class="register-form__input">
                @error('password')
                    <span class="form__error">{{ $message }}</span>
                @enderror
            </div>
            <div class="register-form__group">
                <label for="password_confirmation" class="register-form__label">Подтвердите пароль</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="register-form__input">
                @error('password_confirmation')
                    <span class="form__error">{{ $message }}</span>
                @enderror
            </div>
            <button type="button" class="register-form__button" onclick="submitForm(this)">Зарегистрироваться</button>
        </form>
    </div>
</div>
@endsection

