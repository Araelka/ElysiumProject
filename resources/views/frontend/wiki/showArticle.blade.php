@extends('frontend.wiki.index')
@section('title', $article->theme->name)
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
            
                    <p>{!! $article->content_html !!}</p>
                   
            @endif
        </div>
    </div>
</div>
</div>


@endsection