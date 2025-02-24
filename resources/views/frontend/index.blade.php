@include('frontend/layout/header', ['title' => 'Главная станица']);

<main class="home-page">
    <div class="container d-flex justify-content-center align-items-stretch"> <!-- Центрируем контейнер и растягиваем блоки -->
        <div class="row w-100 h-100"> <!-- Устанавливаем ширину и высоту на 100% для корректного отображения колонок -->
            <!-- Блок для выбора тем (20%) -->
            <div class="col-md-2 sidebar d-flex flex-column justify-content-start">
                <h3>Выберите тему</h3>
                <ul class="topics-list">
                    <li><a href="#" class="topic-link">Тема 1</a></li>
                    <li><a href="#" class="topic-link">Тема 2</a></li>
                    <li><a href="#" class="topic-link">Тема 3</a></li>
                    <!-- Добавьте больше тем по необходимости -->
                </ul>
            </div>

            <!-- Блок для просмотра постов (80%) -->
            <div class="col-md-10 content d-flex flex-column justify-content-start">
                <h3>Посты по выбранной теме</h3>
                <div class="posts">
                    <!-- Пример поста -->
                    <div class="post">
                        <h4>Заголовок поста</h4>
                        <p>Краткое описание поста...</p>
                    </div>
                    
                    <!-- Добавьте больше постов по необходимости -->
                </div>
            </div>
        </div>
    </div>
</main>

@include('frontend/layout/footer');
