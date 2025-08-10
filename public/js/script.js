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


// ОБРАБОТЧИК ЧЕКБОКСОВ ДЛЯ МАССОВОГО УДАЛЕНИЯ
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.bulk-checkbox');
    const selectAllCheckbox = document.getElementById('select-all-checkbox');

    // Функция для обновления значения скрытого поля
    function updateSelectedIds() {
        const selectedIds = Array.from(checkboxes)
            .filter(function (checkbox) {
                return checkbox.checked;
            })
            .map(function (checkbox) {
                return checkbox.dataset.bulkId;
            });

        // Обновляем значение для всех форм с data-атрибутами
        document.querySelectorAll('[data-input-type]').forEach(function (input) {
            input.value = selectedIds.join(',');
        });
    }

    // Обработка выбора всех чекбоксов
    selectAllCheckbox.addEventListener('change', function () {
        checkboxes.forEach(function (checkbox) {
            checkbox.checked = selectAllCheckbox.checked;
        });
        updateSelectedIds();
    });

    // Обработка выбора отдельных чекбоксов
    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            updateSelectedIds();
        });
    });
});

// ОБРАБОТЧИК ПОИСКА
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('.search-input');
    const clearSearchButton = document.querySelector('.clear-search-button');

    // Обработчик для нажатия Enter в поле поиска
    searchInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Предотвращаем стандартное поведение при нажатии Enter

            const searchTerm = event.target.value.trim();
            const currentUrl = new URL(window.location.href);

            if (searchTerm.length > 0) {
                // Добавляем параметр search в URL
                currentUrl.searchParams.set('search', searchTerm);
            } else {
                // Если строка поиска пустая, удаляем параметр search
                currentUrl.searchParams.delete('search');
            }

            // Перенаправляем пользователя на обновленный URL
            window.location.href = currentUrl.toString();
        }
    });

    // Обработчик для кнопки "очистки" (крестик)
    if (clearSearchButton) {
        clearSearchButton.addEventListener('click', function () {
            // Очищаем поле поиска
            searchInput.value = '';

            // Удаляем параметр search из URL
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.delete('search');

            // Перенаправляем пользователя на обновленный URL
            window.location.href = currentUrl.toString();
        });
    }
});

document.getElementById('image').addEventListener('change', function(event) {
    const input = event.target;
    const previewImage = document.getElementById('preview-image');
    const placeholderText = document.getElementById('placeholder-text');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewImage.style.display = 'block';
            placeholderText.style.display = 'none';
        };

        reader.readAsDataURL(input.files[0]);
    } else {
        previewImage.src = '#';
        previewImage.style.display = 'none';
        placeholderText.style.display = 'block';
    }
});

function submitForm(btn){
    btn.disabled = true;
    btn.form.submit();
}




