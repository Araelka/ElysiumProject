@extends('frontend.layout.layout')
@if ($selectedThemeId)
@section('title', $themes[$selectedThemeId-1]->name)
@endif


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
                     @if ($selectedThemeId)
                    <h3>{{ $themes[$selectedThemeId-1]->name }}</h3>
                    @endif
                    <div class="posts">
                    @if ($posts->isEmpty())
                    @else
                    @foreach ($posts as $post)
                        <div class="post">
                            <div class="post-header">
                                <h4>{{ $post->user->login }}</h4>
                                @auth
                                    @if (auth()->id() == $post->user_id)
                                    <a href={{ route('post.editShow', $post->id) }} class="edit-post-button" style="transform: rotate(90deg);">&#9998</a>
                                    <form action={{ route('post.destroy', $post->id) }} method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-post-button">×</button>
                                    </form>
                                    @endif
                                @endauth
                            </div>
                            <p>{!! nl2br(e($post->content)) !!}</p>
                            <small>{{ $post->created_at }}</small>
                        </div>
                    @endforeach
                    @endif
                </div>

                @auth
                    @if ($selectedThemeId && !isset($postContent)) 
                        @include('frontend.layout.postPublishForm')
                    @elseif ($selectedThemeId && isset($postContent))
                        @include('frontend.layout.postEditForm')
                    @endif
                @endauth


                

            </div>
        </div>
    </div>
</div>
@endsection

