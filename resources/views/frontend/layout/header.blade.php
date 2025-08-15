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
            @if (auth()->check() && auth()->user()->isPlayer())
                <li><a href="{{ route('gameroom.index') }}" class="navbar__link">Игровая
                    <span>12</span>    
                </a></li>
            @endif
            <li><a href="{{ route('character.publicIndex') }}" class="navbar__link">Персонажи</a></li>
            <li><a href="{{ route('wiki.index') }}"  class="navbar__link">Вики</a></li>
            <li><a href="" class="navbar__link">Карта</a></li>
            <li><a href="" class="navbar__link">О нас</a></li>
        </ul>
        <!-- Кнопки для мобильной версии -->
        @guest
            <form action="{{ route('login') }}" method="GET" style="display:inline;">
                @csrf
                <button type="submit" class="navbar__login-mobile">Вход</button>
            </form>
        @else
            <!-- Меню пользователя (мобильная версия) -->
            <div class="navbar__user-mobile">
                <!-- Кнопка вызова меню -->
                <div class="navbar__user-trigger" id="user-trigger-mobile">
                    <span class="navbar__user-name">{{ Auth::user()->login }}</span>
                    <img src="{{ asset('images/default-avatar.png') }}" alt="User Avatar" class="navbar__user-avatar" id="user-avatar-mobile">
                </div>
            </div>
                <ul class="navbar__user-menu" id="user-menu-mobile" role="menu" aria-hidden="true">
                    @if (Auth::user()->isAdmin())
                        <li><a href={{route('admin') }} class="navbar__link">Админ-панель</a></li>
                    @endif
                    @if (Auth::user()->isModerator() || Auth::user()->isGameMaster() || Auth::user()->isQuestionnaireSpecialist())
                        <li><a href={{route('game-master.index') }} class="navbar__link">ГМ-панель</a></li>
                    @endif
                    <li><a href={{ route('characters.index') }} class="navbar__link">Персонажи</a></li>
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
                <!-- Меню пользователя (десктопная версия) -->
                <div class="navbar__user">
                    <div class="navbar__user-trigger" id="user-trigger-desktop">
                        <span class="navbar__user-name">{{ Auth::user()->login }}</span>
                        <img src="{{ asset('images/default-avatar.png') }}" alt="User Avatar" class="navbar__user-avatar" id="user-avatar-desktop">
                    </div>
                    <ul class="navbar__user-menu" id="user-menu-desktop" role="menu" aria-hidden="true">
                        @if (Auth::user()->isAdmin())
                            <li><a href={{route('admin') }} class="navbar__link">Админ-панель</a></li>
                        @endif
                        @if (Auth::user()->isModerator() || Auth::user()->isGameMaster() || Auth::user()->isQuestionnaireSpecialist())
                            <li><a href={{route('game-master.index') }} class="navbar__link">ГМ-панель</a></li>
                        @endif
                        <li><a href={{ route('characters.index') }} class="navbar__link">Персонажи</a></li>
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