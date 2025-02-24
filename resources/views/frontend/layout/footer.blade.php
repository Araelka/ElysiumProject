<!-- JavaScript для гамбургер-меню -->
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
    </body>
    </html>