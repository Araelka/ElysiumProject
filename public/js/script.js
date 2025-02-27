document.addEventListener('DOMContentLoaded', function () {
    const postsContainer = document.querySelector('.posts');
    if (postsContainer) {
        // Прокручиваем контейнер вниз при загрузке страницы
        postsContainer.scrollTop = postsContainer.scrollHeight;

        // Если вы добавляете новые посты динамически (через AJAX), используйте MutationObserver
        const observer = new MutationObserver(function () {
            postsContainer.scrollTop = postsContainer.scrollHeight;
        });

        // Наблюдаем за изменениями в контейнере .posts
        observer.observe(postsContainer, { childList: true });
    }
});


//Скрипт для гамбургер-меню
document.addEventListener('DOMContentLoaded', () => {
    const mobileMenu = document.getElementById('mobile-menu');
    const mainMenu = document.getElementById('main-menu');
    const userAvatarMobile = document.getElementById('user-avatar-mobile');
    const userMenuMobile = document.getElementById('user-menu-mobile');
    const userAvatarDesktop = document.getElementById('user-avatar-desktop');
    const userMenuDesktop = document.getElementById('user-menu-desktop');

    function closeMenus(...menus) {
        menus.forEach(menu => {
            if (menu && menu.classList.contains('active')) {
                menu.classList.remove('active');
            }
        });
    }

    function handleUserAvatarClick(userAvatar, userMenu) {
        userAvatar.addEventListener('click', () => {
            closeMenus(mainMenu, userMenuMobile, userMenuDesktop);
            userMenu.classList.toggle('active');
        });

        // Закрыть меню при клике вне его области
        document.addEventListener('click', (event) => {
            if (!userAvatar.contains(event.target) && !userMenu.contains(event.target)) {
                userMenu.classList.remove('active');
            }
        });
    }

    if (mobileMenu && mainMenu) {
        mobileMenu.addEventListener('click', () => {
            closeMenus(userMenuMobile, userMenuDesktop);
            mainMenu.classList.toggle('active');
            mobileMenu.classList.toggle('active');
            mobileMenu.setAttribute('aria-expanded', mainMenu.classList.contains('active'));
            mainMenu.setAttribute('aria-hidden', !mainMenu.classList.contains('active'));
        });
    }

    if (userAvatarMobile && userMenuMobile) {
        handleUserAvatarClick(userAvatarMobile, userMenuMobile);
    }

    if (userAvatarDesktop && userMenuDesktop) {
        handleUserAvatarClick(userAvatarDesktop, userMenuDesktop);
    }
});



document.addEventListener('DOMContentLoaded', function () {
const postsContainer = document.querySelector('.posts');

// Функция для показа полосы прокрутки
function showScrollbar() {
    postsContainer.classList.add('scrolling');
}

// Функция для скрытия полосы прокрутки
function hideScrollbar() {
    postsContainer.classList.remove('scrolling');
}

// Добавляем обработчик прокрутки
postsContainer.addEventListener('scroll', function () {
    showScrollbar(); // Показываем полосу при прокрутке

    // Через 2 секунды после остановки прокрутки скрываем полосу
    clearTimeout(postsContainer.scrollTimeout);
    postsContainer.scrollTimeout = setTimeout(hideScrollbar, 2000);
});

// Добавляем обработчик наведения мыши
postsContainer.addEventListener('mouseenter', showScrollbar); // Показываем полосу при наведении
postsContainer.addEventListener('mouseleave', hideScrollbar); // Скрываем полосу при уходе мыши
});

