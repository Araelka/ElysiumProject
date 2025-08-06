@extends('frontend.layout.layout')
<link rel="stylesheet" href="{{ asset('css/posts.css') }}">
@if ($selectedLocation) 
@section('title', $selectedLocation->name)
@endif


@section('content')
<div class="double-page">
    <div class="container d-flex justify-content-center align-items-stretch"> 
        <div class="row w-100 h-100"> 
            <div class="col-md-2 sidebar d-flex flex-column justify-content-start">
                <h3>Локации</h3>
                <ul class="topics-list">
                    @foreach ($locations as $location)
                        <li><a href="?location_id={{ $location->id }}" 
                            class="topic-link {{ $selectedLocation && $selectedLocation->id == $location->id ? 'active' : '' }}">
                             {{ $location->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Блок для просмотра постов (80%) -->
            <div class="col-md-10 content d-flex flex-column justify-content-start">
                     @if ($selectedLocation)
                    <h3>{{ $selectedLocation->name }}</h3>
                    @endif
                    <div class="posts">
                    @if ($posts->isEmpty())
                    @else
                    @foreach ($posts as $post)
                        <div class="post">
                            <div class="post-header">
                                <div style="display: flex; flex-direction: row; align-items: center; gap: 5px;">
                                    <div>
                                        @if ($post->character->images->first())
                                            <img src="{{ asset('storage/' . $post->character->images->first()->path ) }}" alt="Аватар персонажа" class="dropdown-avatar" style="height: 35px; width: 35px;">
                                        @elseif ($post->character->gender == 'Мужской')
                                            <img src="{{ asset('images/characters/characterMale.jpg') }}" alt="Аватар персонажа" class="dropdown-avatar" style="height: 35px; width: 35px;">
                                        @else 
                                            <img src="{{ asset('images/characters/characterFemale.jpg') }}" alt="Аватар персонажа" class="dropdown-avatar" style="height: 35px; width: 35px;">
                                        @endif
                                    </div>
                                    <div>
                                        <h4 style="padding-left: 5px">{{ $post->character->firstName . ' ' . $post->character->secondName }}</h4>
                                    </div>
                                </div>
                                    @auth
                                    <div style="display: flex; flex-direction: row; align-items: center;">
                                        @if (auth()->id() == $post->character->user_id) 
                                        <a href={{ route('gameroom.editShow', $post->id) }} class="edit-post-button">✎</a>
                                        @endif
                                        @if (auth()->id() == $post->character->user_id || Auth::user()->isEditor())
                                            <form action={{ route('gameroom.destroy', $post->id) }} method="POST" style="margin: 0px;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="delete-post-button">×</button>
                                            </form>
                                        @endif
                                    </div>
                                    @endauth
                                
                            </div>
                            <p>{!! nl2br(e($post->content)) !!}</p>
                            <small>
                                <div style="display: flex; flex-direction: row; justify-content: space-between;">
                                    <div class="post-date">
                                    {{ $post->updated_at }}
                                    @if ($post->updated_at != $post->created_at)
                                        (изм)
                                    @endif
                                    </div>
                                    <div>
                                        {{ $post->character->user->login }}
                                    </div>
                                </div>
                            </small>
                
                        </div>
                    @endforeach
                    @endif
                </div>

                @auth
                    @if ($selectedLocation && !isset($postContent)) 
                        @include('frontend.gameroom.postPublishForm')
                    @elseif ($selectedLocation && isset($postContent))
                        @include('frontend.gameroom.postEditForm')
                    @endif
                @endauth

            </div>
        </div>
    </div>
</div>
@endsection

