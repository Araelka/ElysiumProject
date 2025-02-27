<header class="navbar">
    <nav class="navbar__container">
        <!-- Гамбургер-меню -->
        <div class="navbar__toggle" id="mobile-menu" role="button" aria-expanded="false" aria-controls="menu">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <ul class="navbar__menu" id="main-menu" role="menu" aria-hidden="true">
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
            <div class="navbar__user-mobile">
                <!-- Кнопка вызова меню -->
                <span class="navbar__user-name">{{ Auth::user()->login }}</span>
                <img src="images/default-avatar.png" alt="User Avatar" class="navbar__user-avatar" id="user-avatar-mobile">
            </div>
            
            
            <!-- Меню пользователя (мобильная версия) -->
            <ul class="navbar__user-menu" id="user-menu-mobile" role="menu" aria-hidden="true">
                <li><a href="" class="navbar__link">Админ-панель</a></li>
                <li><a href="" class="navbar__link">Настройки</a></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="navbar__link">Выход</button>
                    </form>
                </li>
            </ul>
        @endguest
        <!-- Кнопки для десктопной версии -->
        <div class="navbar__auth">
            @guest
                <a href="{{ route('login') }}" class="navbar__login">Вход</a>
            @else
                <!-- Кнопка аватара пользователя (десктопная версия) -->
                <div class="navbar__user">
                    <span class="navbar__user-name">{{ Auth::user()->login }}</span>
                    <img src="images/default-avatar.png" alt="User Avatar" class="navbar__user-avatar" id="user-avatar-desktop">
                    <ul class="navbar__user-menu" id="user-menu-desktop" role="menu" aria-hidden="true">
                        <li><a href="" class="navbar__link">Админ-панель</a></li>
                        <li><a href="" class="navbar__link">Настройки</a></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="navbar__link">Выход</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endguest
        </div>
    </nav>
</header>