<header class="navbar">
    <nav class="navbar__container">
        <!-- Гамбургер-меню -->
        <div class="navbar__toggle" id="mobile-menu">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <ul class="navbar__menu" id="menu">
            <li><a href="{{ route('homePage') }}" class="navbar__link">Главная</a></li>
            <li><a href="" class="navbar__link">Карта</a></li>
            <li><a href="" class="navbar__link">О нас</a></li>
            <li><a href="" class="navbar__link">Другое</a></li>
        </ul>
        <!-- Кнопки входа/выхода для мобильной версии -->
        @guest
            <form action="{{ route('login') }}" method="GET" style="display:inline;">
                @csrf
                <button type="submit" class="navbar__login-mobile">Вход</button>
            </form>
        @else
            <div class="navbar__user-dropdown-mobile">
                <div class="navbar__user-avatar">
                    <img src="{{ Auth::user()->avatar ?? '/images/default-avatar.png' }}" alt="Аватар">
                </div>
                <ul class="navbar__dropdown-menu">
                    @if(Auth::user()->isAdmin())
                        <li><a href="">Админ-панель</a></li>
                    @endif
                    <li><a href="">Настройки</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="navbar__logout">Выход</button>
                        </form>
                    </li>
                </ul>
            </div>
        @endguest
        <!-- Кнопки входа/выхода для десктопной версии -->
        <div class="navbar__auth">
            @guest
                <form action="{{ route('login') }}" method="GET" style="display:inline;">
                    @csrf
                    <button type="submit" class="navbar__login">Вход</button>
                </form>
            @else
                <div class="navbar__user-dropdown">
                    <div class="navbar__user-avatar">
                        <img src="{{ Auth::user()->avatar ?? '/images/default-avatar.png' }}" alt="Аватар">
                    </div>
                    <ul class="navbar__dropdown-menu">
                        @if(Auth::user()->isAdmin())
                            <li><a href="">Админ-панель</a></li>
                        @endif
                        <li><a href="">Настройки</a></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="navbar__logout">Выход</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endguest
        </div>
    </nav>
</header>