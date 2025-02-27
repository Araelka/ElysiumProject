<header class="navbar">
    <nav class="navbar__container">
        <!-- Гамбургер-меню -->
        <div class="navbar__toggle" id="mobile-menu" role="button" aria-expanded="false" aria-controls="menu">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <ul class="navbar__menu" id="menu" role="menu" aria-hidden="true">
            <li><a href="{{ route('homePage') }}" class="navbar__link">Главная</a></li>
            <li><a href="" class="navbar__link">Карта</a></li>
            <li><a href="" class="navbar__link">О нас</a></li>
            <li><a href="" class="navbar__link">Другое</a></li>
        </ul>
        <!-- Кнопки для мобильной версии -->
        @guest
            <form action="{{ route('login') }}" method="GET" style="display:inline;">
                @csrf
                <button type="submit" class="navbar__login-mobile">Вход</button>
            </form>
        @else
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="navbar__logout-mobile">Выход</button>
            </form>
        @endguest
        <!-- Кнопки для десктопной версии -->
        <div class="navbar__auth">
            @guest
                <a href="{{ route('login') }}" class="navbar__login">Вход</a>
            @else
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="navbar__logout">Выход</button>
                </form>
            @endguest
        </div>
    </nav>
</header>