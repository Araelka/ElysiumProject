@extends('frontend.wiki.showArticle')

@section('article-content')
<div class="markdown-editor-container">
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

    <!-- Панель инструментов -->
    <div class="editor-toolbar mt-3">
        <button class="toolbar-button" onclick="insertMarkdown('# ', 'Заголовок')">H1</button>
        <button class="toolbar-button" onclick="insertMarkdown('## ', 'Подзаголовок')">H2</button>
        <button class="toolbar-button" onclick="insertMarkdown('**', 'Полужирный текст')"><b>B</b></button>
        <button class="toolbar-button" onclick="insertMarkdown('*', 'Курсив')"><i>I</i></button>
        <button class="toolbar-button" onclick="insertMarkdown('[', '](', 'https://example.com )', 'Ссылка')">Link</button>
        <button class="toolbar-button" onclick="insertMarkdown('![', '](', 'https://placehold.co/600x400 ', 'Альтернативный текст')">Image</button>
        <button class="toolbar-button" onclick="insertMarkdown('- ', 'Элемент списка')">List</button>
    </div>
</div>

@endsection