@extends('frontend.layout.layout')

@section('content')
<div class="home-page">
    <div class="container d-flex justify-content-center align-items-stretch"> 
        <div class="row w-100 h-100"> 
            <div class="col-md-2 sidebar d-flex flex-column justify-content-start">
                <h3>Локации</h3>
                <ul class="topics-list">
                    @foreach ($themes as $theme)
                        <li><a href="?theme_id={{ $theme->id }}" 
                            class="topic-link {{ $selectedThemeId == $theme->id ? 'active' : '' }}">
                             {{ $theme->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Блок для просмотра постов (80%) -->
            <div class="col-md-10 content d-flex flex-column justify-content-start">
                     @if (!$posts->isEmpty())
                    <h3>{{ $posts[0]->theme->name }}</h3>
                    @endif
                    <div class="posts">
                    @if ($posts->isEmpty())
                    @else
                    @foreach ($posts as $post)
                        @section('title-theme', $post->theme->name)
                        <div class="post">
                            <div class="post-header">
                                <h4>{{ $post->user->login }}</h4>
                                <form action={{ route('post.destroy', $post->id) }} method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-post-button">×</button>
                                </form>
                            </div>
                            <p>{!! nl2br(e($post->content)) !!}</p>
                            <small>{{ $post->created_at }}</small>
                        </div>
                    @endforeach
                    @endif
                </div>

                @if (!$posts->isEmpty())
                <form class="post-form" action={{ route('post.publish') }} method="POST">
                    @csrf
                    <div class="post-form__group">
                        <input type="hidden" name="theme_id" value={{ $selectedThemeId }}>
                        <label for="post-text" class="post-form__label">Введите текст поста:</label>
                        <textarea id="post-text" name="post_text" class="post-form__input"></textarea>
                    </div>
                    <button type="submit" class="post-form__button">Отправить</button>
                </form>
                @endif


            </div>
        </div>
    </div>
</div>
@endsection

