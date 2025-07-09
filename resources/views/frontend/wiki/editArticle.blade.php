@extends('frontend.wiki.showArticle')

@section('article-content')
<div class="markdown-editor-container">
    <!-- Панель инструментов -->
    <div class="editor-toolbar mt-3">
        <button class="toolbar-button" onclick="insertMarkdown('## ', '', 'Заголовок')">Заголовок</button>
        <button class="toolbar-button" onclick="insertMarkdown('### ', '', 'Подзаголовок')">Подзаголовок</button>
        <button class="toolbar-button" onclick="insertMarkdown('**', '**', 'Полужирный текст')"><b>B</b></button>
        <button class="toolbar-button" onclick="insertMarkdown('*', '*', 'Курсив')"><i>I</i></button>
        <button class="toolbar-button" onclick="insertMarkdown('[', '](адрес)', 'Ссылка',  'Ссылка')">🔗 Ссылка</button>
        {{-- <button class="toolbar-button" onclick="insertMarkdown('![', '](адрес){width=300 height=200 align=right}',  'Альтернативный текст')">🖼️ Изображение</button> --}}
        <button class="toolbar-button" onclick="openImageModal()">🖼️ Изображение</button>
        <button class="toolbar-button" onclick="insertMarkdown('- ', '\r\n', 'Элемент списка')">📝 Список</button>
        <button class="toolbar-button" onclick="insertMarkdown('1. ', ' \n', 'Элемент нумерованного списка')">🔢 Нумерованный список</button>
        <button class="toolbar-button" onclick="insertMarkdown('---', ' ', '')">― Линия</button>
        <button class="toolbar-button" onclick="insertMarkdown('<br>', '', '')">Пропуск</button>
    </div>

    <form action={{ route('wiki.editArticleContent', $article->id) }} method="POST">
        @csrf
        @method('PUT')
    

    <div class="editor-body d-flex">
        <!-- Левая панель: Markdown-ввод -->
        <div class="editor-input flex-grow-1 mr-4">
            <textarea id="markdown-input" name="content" class="markdown-textarea">{{ $article->content }}</textarea>
        </div>
    </div>

    <div class="editor-header d-flex justify-content-between align-items-center mb-3">
            <button type="submit" class="save-button">Сохранить</button>
    </div>
    </form>
</div>

<!-- Модальное окно -->
<div id="image-modal" class="modal">
    <div class="modal-content-img">
        <span class="close" onclick="closeImageModal()">&times;</span>
        <h2>Загрузить изображение</h2>

        <!-- Форма для загрузки изображения -->
        <form id="image-upload-form" enctype="multipart/form-data">
            @csrf
            <input type="text" id="article-id" name="article-id" value={{ $article->id }} style="display: none"/>
            <div class="form-group">
                <label for="image-file">Выбрать файл:</label>
                <input type="file" id="image-file" name="image" accept="image/*" required />
            </div>

            <div class="form-group" style="display: none">
                <label for="image-alt">Альтернативный текст:</label>
                <input type="text" id="image-alt" name="alt" placeholder="Альтернативный текст" />
            </div>

            <div class="form-group">
                <label for="image-width">Ширина:</label>
                <input type="number" id="image-width" name="width" placeholder="Ширина" min="50"/>
            </div>

            <div class="form-group">
                <label for="image-height">Высота:</label>
                <input type="number" id="image-height" name="height" placeholder="Высота" min="50"/>
            </div>

            <div class="form-group">
                <label for="image-align">Выравнивание:</label>
                <select id="image-align" name="align">
                    <option value="center">По центру</option>
                    <option value="left">Слева</option>
                    <option value="right">Справа</option>
                </select>
            </div>

            <button type="submit" class="save-button">Вставить</button>
        </form>
    </div>
</div>

<script>

    // Открыть модальное окно
    function openImageModal() {
        document.getElementById('image-modal').style.display = 'block';
    }

    function closeImageModal() {
        document.getElementById('image-modal').style.display = 'none';
    }

    document.getElementById('image-upload-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        const fileInput = document.getElementById('image-file');
        let altText = document.getElementById('image-alt').value;
        const width = document.getElementById('image-width').value || '';
        const height = document.getElementById('image-height').value || '';
        const align = document.getElementById('image-align').value;

        if (!altText) {
            altText = "Изображение";
        };

        if (fileInput.files.length > 0) {
            const formData = new FormData();
            formData.append('image', fileInput.files[0]);

            try {
                // Отправляем изображение на сервер
                const articleId = document.getElementById('article-id').value; // Получаем ID статьи

                const response = await fetch(`/wiki/article/edit/content/${articleId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    },
                });


                if (!response.ok) {
                    throw new Error('Ошибка загрузки изображения');
                }

                const result = await response.json();


                // Формируем Markdown с параметрами
                let markdown = `![${altText}](${result.url})`;
                if (width || height || align) {
                    markdown += `{`;
                    if (width) markdown += `width=${width} `;
                    if (height) markdown += `height=${height} `;
                    if (align) markdown += `align=${align}`;
                    markdown += `}`;
                }

                // Вставляем Markdown в текстовое поле
                insertMarkdown('', markdown);

                // Закрываем модальное окно
                closeImageModal();

            } catch (error) {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при загрузке изображения.');
            }
        }
    }); 


    // Функция для вставки Markdown в текстовое поле
    function insertMarkdown(prefix, suffix, defaultText = '') {
        const textarea = document.querySelector('textarea'); // Предполагается, что есть текстовое поле
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;

        const selectedText = textarea.value.substring(start, end) || defaultText;
        const newText = prefix + selectedText + suffix;

        textarea.value =
            textarea.value.substring(0, start) + newText + textarea.value.substring(end);

        // Перемещаем курсор
        textarea.setSelectionRange(start + newText.length, start + newText.length);
        textarea.focus();
    }


    // Функция для вставки Markdown
    function insertMarkdown(prefix, suffix = '', placeholder = '', defaultValue = '') {
        const textarea = document.getElementById('markdown-input');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;

        // Получаем текущий текст в textarea
        const currentText = textarea.value;

        // Определяем текст для вставки
        const selectedText = currentText.substring(start, end);
        const newText = prefix + (selectedText || placeholder || defaultValue) + suffix;

        // Вставляем новый текст в textarea
        textarea.value =
            currentText.substring(0, start) + newText + currentText.substring(end);

        // Перемещаем курсор в конец вставленного текста
        const newCursorPosition = start + newText.length;
        textarea.setSelectionRange(newCursorPosition, newCursorPosition);

        // Фокусируем textarea
        textarea.focus();
    }
</script>


@endsection