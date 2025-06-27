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

// ОБРАБОТКА МОДАЛЬНОГО ОКНА
document.addEventListener('DOMContentLoaded', function () {
    // Элементы для модальных окон
    const deleteModal = document.getElementById('confirm-delete-modal');
    const banModal = document.getElementById('confirm-ban-modal');

    // Общие элементы для обоих модальных окон
    const confirmDeleteButton = document.getElementById('confirm-delete');
    const cancelDeleteButton = document.getElementById('cancel-delete');
    const deleteCloseButton = deleteModal.querySelector('.close');

    const confirmBanButton = document.getElementById('confirm-ban');
    const cancelBanButton = document.getElementById('cancel-ban');
    const banCloseButton = banModal.querySelector('.close');
    const banReasonInput = document.getElementById('ban-reason');

    let currentForm = null;
    let currentUserId = null; // Для хранения ID пользователя при бане конкретного пользователя

    // Функция для открытия модального окна удаления
    function openDeleteModal(event) {
        event.preventDefault();
        deleteModal.style.display = 'block';
        currentForm = event.target.closest('form');
    }

    // Функция для закрытия модального окна удаления
    function closeDeleteModal() {
        deleteModal.style.display = 'none';
        currentForm = null;
    }

    // Функция для открытия модального окна бана
    function openBanModal(event) {
        event.preventDefault();

        // Проверяем, является ли это баном конкретного пользователя
        const banButton = event.target.closest('.ban-button');
        if (banButton && banButton.dataset.userId) {
            // Бан конкретного пользователя
            currentUserId = banButton.dataset.userId; // Сохраняем ID пользователя
        } else {
            // Массовый бан
            const selectedIdsInput = document.querySelector('[data-input-type="users-ban"]');
            if (!selectedIdsInput || !selectedIdsInput.value) {
                alert('Не выбраны пользователи для бана');
                return;
            }
        }

        banModal.style.display = 'block';
    }

    // Функция для закрытия модального окна бана
    function closeBanModal() {
        banModal.style.display = 'none';
        banReasonInput.value = ''; // Очищаем поле причины бана
        currentUserId = null; // Сбрасываем ID пользователя
    }

    // Обработчик события для всех кнопок отправки форм
    document.querySelectorAll('.delete-button, .ban-button').forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();

            const form = event.target.closest('form');
            const action = form.dataset.action;

            if (action === 'ban') {
                openBanModal(event);
            } else if (action === 'delete') {
                openDeleteModal(event);
            }
        });
    });

    // Добавляем обработчик для кнопок бана отдельных пользователей
    document.querySelectorAll('.single-ban-form .ban-button').forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            openBanModal(event);
        });
    });

    // Добавляем обработчик для кнопок удаления одного элемента
    document.querySelectorAll('.single-delete-form .delete-button').forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            openDeleteModal(event);
        });
    });

    // Обработчик события для кнопки подтверждения удаления
    confirmDeleteButton.addEventListener('click', function () {
        if (currentForm) {
            currentForm.submit();
        }
        closeDeleteModal();
    });

    // Обработчик события для кнопки отмены и крестика (удаление)
    [cancelDeleteButton, deleteCloseButton].forEach(button => {
        button.addEventListener('click', closeDeleteModal);
    });

    // Обработчик события для кнопки подтверждения бана
    confirmBanButton.addEventListener('click', function () {
        let reason = banReasonInput.value.trim(); // Получаем значение из поля ввода

        // Если поле пустое, отправляем null
        if (!reason) {
            reason = null;
        }

        if (currentUserId) {
            // Бан конкретного пользователя
            const banForm = document.querySelector(`.single-ban-form button[data-user-id="${currentUserId}"]`).closest('form');

            // Добавляем причину бана в форму как скрытое поле
            const hiddenReasonInput = document.createElement('input');
            hiddenReasonInput.type = 'hidden';
            hiddenReasonInput.name = 'ban_reason';
            hiddenReasonInput.value = reason;

            banForm.appendChild(hiddenReasonInput);
            banForm.submit(); // Отправляем форму
        } else {
            // Массовый бан
            const selectedIdsInput = document.querySelector('[data-input-type="users-ban"]');
            if (!selectedIdsInput || !selectedIdsInput.value) {
                alert('Не выбраны пользователи для бана');
                return;
            }

            // Добавляем причину бана в форму как скрытое поле
            const hiddenReasonInput = document.createElement('input');
            hiddenReasonInput.type = 'hidden';
            hiddenReasonInput.name = 'ban_reason';
            hiddenReasonInput.value = reason;

            const banForm = document.querySelector('#bulk-ban-form');
            banForm.appendChild(hiddenReasonInput);
            banForm.submit(); // Отправляем форму
        }

        closeBanModal();
    });

    // Обработчик события для кнопки отмены и крестика (бан)
    [cancelBanButton, banCloseButton].forEach(button => {
        button.addEventListener('click', closeBanModal);
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



