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


//Скрипт-обработчик меню
document.addEventListener('DOMContentLoaded', () => {
    const mobileMenu = document.getElementById('mobile-menu');
    const mainMenu = document.getElementById('main-menu');
    const userTriggerMobile = document.getElementById('user-trigger-mobile');
    const userMenuMobile = document.getElementById('user-menu-mobile');
    const userTriggerDesktop = document.getElementById('user-trigger-desktop');
    const userMenuDesktop = document.getElementById('user-menu-desktop');

    // Функция для закрытия всех меню
    function closeMenus(...menus) {
        menus.forEach(menu => {
            if (menu && menu.classList.contains('active')) {
                menu.classList.remove('active');
            }
        });

        // Сбрасываем состояние гамбургер-меню
        if (mobileMenu) {
            mobileMenu.classList.remove('active');
            mobileMenu.setAttribute('aria-expanded', false);
        }
        if (mainMenu) {
            mainMenu.setAttribute('aria-hidden', true);
        }
    }

    // Обработчик для гамбургер-меню
    if (mobileMenu && mainMenu) {
        mobileMenu.addEventListener('click', (event) => {
            event.stopPropagation(); // Предотвращаем всплытие события
            closeMenus(userMenuMobile, userMenuDesktop); // Закрываем другие меню
            mainMenu.classList.toggle('active');
            mobileMenu.classList.toggle('active');
            mobileMenu.setAttribute('aria-expanded', mainMenu.classList.contains('active'));
            mainMenu.setAttribute('aria-hidden', !mainMenu.classList.contains('active'));
        });
    }

    // Общая функция для обработки кликов по триггерам пользовательского меню
    function handleUserTriggerClick(userTrigger, userMenu) {
        userTrigger.addEventListener('click', (event) => {
            event.stopPropagation(); // Останавливаем распространение события
            if (userMenu.classList.contains('active')) {
                userMenu.classList.remove('active'); // Закрываем меню, если оно уже открыто
            } else {
                closeMenus(mainMenu, userMenuMobile, userMenuDesktop); // Закрываем другие меню
                userMenu.classList.add('active'); // Открываем текущее меню
            }
        });
    }

    // Привязываем обработчики для мобильного и десктопного пользовательского меню
    if (userTriggerMobile && userMenuMobile) {
        handleUserTriggerClick(userTriggerMobile, userMenuMobile);
    }
    if (userTriggerDesktop && userMenuDesktop) {
        handleUserTriggerClick(userTriggerDesktop, userMenuDesktop);
    }

    // Общий обработчик для закрытия меню при клике вне их области
    document.addEventListener('click', (event) => {
        const isClickInsideMobileMenu = mobileMenu && mobileMenu.contains(event.target);
        const isClickInsideMainMenu = mainMenu && mainMenu.contains(event.target);
        const isClickInsideUserTriggerMobile = userTriggerMobile && userTriggerMobile.contains(event.target);
        const isClickInsideUserMenuMobile = userMenuMobile && userMenuMobile.contains(event.target);
        const isClickInsideUserTriggerDesktop = userTriggerDesktop && userTriggerDesktop.contains(event.target);
        const isClickInsideUserMenuDesktop = userMenuDesktop && userMenuDesktop.contains(event.target);

        // Если клик был вне всех меню, закрываем все меню
        if (
            !isClickInsideMobileMenu &&
            !isClickInsideMainMenu &&
            !isClickInsideUserTriggerMobile &&
            !isClickInsideUserMenuMobile &&
            !isClickInsideUserTriggerDesktop &&
            !isClickInsideUserMenuDesktop
        ) {
            closeMenus(mainMenu, userMenuMobile, userMenuDesktop);
        }
    });
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

