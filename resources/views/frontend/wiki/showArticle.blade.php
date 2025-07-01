@extends('frontend.wiki.index')
@section('title', $article->title)
@section('table')


<div class="article-container">
    <!-- Блок заголовка -->
    <div class="article-header d-flex justify-content-between align-items-center mb-3">

    @if (Request::is('wiki/article/edit/title/*'))
    @yield('editTitle')
    @else
     <!-- Заголовок статьи -->
    <h2 class="article-title mb-0">{{ $article->theme->name }}</h2>

    <!-- Кнопка редактирования названия -->
    @if (Auth::user()->isEditor())
        <a href={{ route('wiki.showEditArticleTitle', $article->id) }} class="edit-title-button">✎</a>
    @endif
    @endif
    </div>




        

    <!-- Основной контент -->
    <div class="article-body d-flex">
            <!-- Содержание статьи -->
        <div class="article-content flex-grow-1">
            <!-- Кнопка редактирования статьи -->
            @if (Auth::user()->isEditor() && !Request::is('wiki/article/edit/content/*')) 
                <a href={{ route('wiki.showEditArticleContent', $article->id) }} class="edit-content-button">Редактировать</a>
            @endif
            @if (Request::is('wiki/article/edit/content/*'))
                @yield('article-content')
            @else
                {{-- @if ($article->images->isNotEmpty())
                <div class="image-frame">
                    <img src="{{ asset('storage/' . $article->images->first()->path) }}" alt="Изображение" class="img-fluid rounded" style="object-fit: cover; width: 300px; height: 300px; padding-right: 5px;" align="left">
                </div>
                @endif --}}
                    <p class="article-content-p">{!! nl2br($article->content) !!}</p>
            @endif
        </div>

        <!-- Изображение справа -->
        {{-- <div class="article-image mr-4">
            @if ($article->images->isNotEmpty())
                <div class="image-frame">
                    <img src="{{ asset('storage/' . $article->images->first()->path) }}" alt="Изображение" class="img-fluid rounded" >
                </div>
            @else
                <div class="no-image-placeholder">Нет изображения</div>
            @endif
        </div> --}}
    </div>
</div>
</div>

@endsection