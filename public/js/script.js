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
        menus.forEach(menu => menu?.classList.remove('active'));
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
            event.stopPropagation();
            closeMenus(userMenuMobile, userMenuDesktop);
            mainMenu.classList.toggle('active');
            mobileMenu.classList.toggle('active');
            mobileMenu.setAttribute('aria-expanded', mainMenu.classList.contains('active'));
            mainMenu.setAttribute('aria-hidden', !mainMenu.classList.contains('active'));
        });
    }

    // Общая функция для обработки кликов по триггерам пользовательского меню
    function handleUserTriggerClick(userTrigger, userMenu) {
        userTrigger.addEventListener('click', (event) => {
            event.stopPropagation();
            if (userMenu.classList.contains('active')) {
                userMenu.classList.remove('active');
            } else {
                closeMenus(mainMenu, userMenuMobile, userMenuDesktop);
                userMenu.classList.add('active');
            }
        });
    }

    // Привязываем обработчики для мобильного и десктопного пользовательского меню
    [userTriggerMobile, userTriggerDesktop].forEach((trigger, index) => {
        const menu = index === 0 ? userMenuMobile : userMenuDesktop;
        if (trigger && menu) {
            handleUserTriggerClick(trigger, menu);
        }
    });

    // Общий обработчик для закрытия меню при клике вне их области
    document.addEventListener('click', (event) => {
        const isClickInsideMenu = 
            (mobileMenu?.contains(event.target) || mainMenu?.contains(event.target)) ||
            (userTriggerMobile?.contains(event.target) || userMenuMobile?.contains(event.target)) ||
            (userTriggerDesktop?.contains(event.target) || userMenuDesktop?.contains(event.target));

        if (!isClickInsideMenu) {
            closeMenus(mainMenu, userMenuMobile, userMenuDesktop);
        }
    });
});

// ОБРАТНОКА МОДАЛЬНОГО ОКНА
document.addEventListener('DOMContentLoaded', function() {
    // Получаем элементы
    const deleteButtons = document.querySelectorAll('.delete-button');
    const modal = document.getElementById('confirm-delete-modal');
    const confirmDeleteButton = document.getElementById('confirm-delete');
    const cancelDeleteButton = document.getElementById('cancel-delete');
    const closeButton = document.querySelector('.close');

    let currentForm = null;

    // Функция для открытия модального окна
    function openModal(event) {
        event.preventDefault();
        modal.style.display = 'block';
        currentForm = event.target.closest('form');
    }

    // Функция для закрытия модального окна
    function closeModal() {
        modal.style.display = 'none';
        currentForm = null;
    }

    // Обработчик события для кнопок удаления
    deleteButtons.forEach(button => {
        button.addEventListener('click', openModal);
    });

    // Обработчик события для кнопки подтверждения удаления
    confirmDeleteButton.addEventListener('click', function() {
        if (currentForm) {
            currentForm.submit();
        }
        closeModal();
    });

    // Обработчик события для кнопки отмены и крестика
    [cancelDeleteButton, closeButton].forEach(button => {
        button.addEventListener('click', closeModal);
    });
});