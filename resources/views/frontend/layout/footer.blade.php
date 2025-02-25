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

