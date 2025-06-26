@extends('frontend.wiki.index')
@section('title', $article->title)
@section('table')

<div class="theme-container">
    <div class="article-header d-flex justify-content-between align-items-center mb-3">
        <!-- Заголовок статьи -->
        <h2 class="article-title">{{ $article->title }}</h2>

        <!-- Кнопка редактирования названия темы -->
        @if (Auth::user()->isEditor())
            <a href="" class="edit-title-button">Редактировать название</a>
        @endif
    </div>

    <div class="article-body d-flex">
        <!-- Основной текст статьи -->
        <div class="article-content flex-grow-1">
            <!-- Кнопка редактирования содержания статьи -->
            @if (Auth::user()->isEditor())
                <a href="" class="edit-content-button">Редактировать содержание</a>
            @endif
            
            <p>{!! nl2br(e($article->content)) !!}</p>
        </div>

        <!-- Изображение справа сверху -->
        <div class="article-image ml-4">
            @if ($article->images->isNotEmpty())
                <img src="{{ asset('storage/' . $article->images->first()->path) }}" alt="Изображение" class="img-fluid rounded">
            @else
                <p>Нет изображений</p>
            @endif
        </div>
    </div>
</div>

@endsection