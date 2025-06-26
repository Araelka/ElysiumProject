@extends('frontend.wiki.index')
@section('title', 'Редактирование статьи')
@section('table')

<div class="markdown-editor-container">
    <div class="editor-header d-flex justify-content-between align-items-center mb-3">
        <h2>Редактирование статьи</h2>
        <form action="{{ route('wiki.article.update', $article->id) }}" method="POST">
            @csrf
            @method('PUT')
            <button type="submit" class="save-button">Сохранить</button>
        </form>
    </div>

    <div class="editor-body d-flex">
        <!-- Левая панель: Markdown-ввод -->
        <div class="editor-input flex-grow-1 mr-4">
            <textarea id="markdown-input" name="content" class="markdown-textarea">{{ $article->content }}</textarea>
        </div>

        <!-- Правая панель: Предпросмотр -->
        <div class="editor-preview flex-grow-1" id="markdown-preview">
            {!! \Illuminate\Support\Str::markdown($article->content) !!}
        </div>
    </div>

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