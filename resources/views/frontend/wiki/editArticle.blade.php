@extends('frontend.wiki.showArticle')

@section('article-content')
<div class="markdown-editor-container">
    <!-- Панель инструментов -->
    <div class="editor-toolbar mt-3">
        <button class="toolbar-button" onclick="insertMarkdown('<h2>', '</h2>', 'Заголовок')">Заголовок</button>
        <button class="toolbar-button" onclick="insertMarkdown('<h3>', '</h3>', 'Подзаголовок')">Подзаголовок</button>
        <button class="toolbar-button" onclick="insertMarkdown('<strong>', '</strong>', 'Полужирный текст')"><b>B</b></button>
        <button class="toolbar-button" onclick="insertMarkdown('<i>', '</i>', 'Курсив')"><i>I</i></button>
        <button class="toolbar-button" onclick="insertMarkdown('<a href=адрес>', '</a>', 'Ссылка',  'Ссылка')">Ссылка</button>
        <button class="toolbar-button" onclick="insertMarkdown('<img src=', '>', 'адрес',  'Альтернативный текст')">Изображение</button>
        <button class="toolbar-button" onclick="insertMarkdown('- ', ' ', 'Элемент списка')">Список</button>
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

<script>
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