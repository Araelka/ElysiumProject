<!-- JavaScript для гамбургер-меню -->
<script>
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
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
    const mobileMenu = document.getElementById('mobile-menu');
    const menu = document.getElementById('menu');
    mobileMenu.addEventListener('click', () => {
    menu.classList.toggle('active');
    mobileMenu.classList.toggle('active'); // Добавляем/удаляем класс 'active'
    });
    });
</script>

<script>
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
</script>
