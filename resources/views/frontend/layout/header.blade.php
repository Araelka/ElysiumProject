<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title }}</title>
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
<header class="navbar">
<nav class="navbar__container">
<!-- Гамбургер-меню -->
<div class="navbar__toggle" id="mobile-menu">
<span></span>
<span></span>
<span></span>
</div>

    <ul class="navbar__menu" id="menu">
        <li><a href={{route('homePage')}} class="navbar__link">Главная</a></li>
        <li><a href="" class="navbar__link">Карта</a></li>
        <li><a href="" class="navbar__link">О нас</a></li>
        <li><a href="" class="navbar__link">Другое</a></li>
        <!-- Кнопка входа/выхода для мобильной версии -->
        @guest
        <li>
            <form action={{route('login')}} method="GET" style="display:inline;">
                @csrf
                <button type="submit" class="navbar__login-mobile">Вход</button>
            </form>
        </li>
        @else
        <li>
            <form action={{route('logout')}} method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="navbar__logout-mobile">Выход</button>
            </form>
        </li>
        @endguest
    </ul>

<!-- Кнопки входа/выхода для десктопной версии -->
<div class="navbar__auth">
    @guest
        <form action={{route('login')}} method="GET" style="display:inline;">
            @csrf
            <button type="submit" class="navbar__login">Вход</button>
        </form>
    @else
        <form action={{route('logout')}} method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="navbar__logout">Выход</button>
        </form>
    @endguest
</nav>
</header>