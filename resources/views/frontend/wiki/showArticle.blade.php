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
                    
                    <!-- Блок для галереи -->
                    @if ($article->images->isNotEmpty())
                    <div class="gallery-block mt-4">
                        <h3 class="gallery-title">Галерея</h3>
                        <hr class="divider">
                        <div class="gallery-images d-flex flex-wrap gap-3">
                            <!-- Пример вывода изображений -->
                                @foreach ($article->images as $image)
                                    <div class="gallery-image">
                                        <img src="{{ asset('storage/' . $image->path) }}" alt="Изображение" class="img-fluid rounded">
                                    </div>
                                @endforeach
                        </div>
                    </div>
                    @endif
            @endif
        </div>
    </div>
</div>
</div>


@endsection