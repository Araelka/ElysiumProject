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
                        <li><a href="{{ route('gameroom.index', ['location_id' => $location->id]) }}" 
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
                    <div class="posts" id="posts-container">
                    @if ($posts->isEmpty())
                    @else
                    @foreach ($posts as $post)
                        <div class="post" id="post-{{ $post->id }}" data-post-id="{{ $post->id }}">
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
                                    <div style="display: flex; flex-direction: row; align-items: center;">
                                        <div class="custom-dropdown-post">
                                            <div>
                                                <button type="button" class="dropdown-toggle-post" onclick="toggleDropdownPostMenu(this)">...</button>
                                            </div>

                                            <div class="dropdown-menu-post">
                                                <div class="dropdown-item-post" style="padding: 0px">
                                                            <a href="javascript:void(0)" data-post-id="{{ $post->id }}" onclick="setParentPostId(this)" data-close-dropdown="true">
                                                                <div style="padding: 5px 10px">
                                                                    Ответить
                                                                </div>
                                                            </a>
                                                    </div>

                                                @if (auth()->user()->id == $post->character->user_id)
                                                    <div class="dropdown-item-post" style="padding: 0px">
                                                            <a href="javascript:void(0)" data-post-id="{{ $post->id }}" onclick="editPost(this)" data-close-dropdown="true">
                                                                <div style="padding: 5px 10px">
                                                                    Редактировать
                                                                </div>
                                                            </a>
                                                    </div>
                                                @endif
                                                @if (auth()->user()->id == $post->character->user_id || Auth::user()->isEditor())
                                                    <div  data-post-id="{{ $post->id }}">
                                                        <form id="delete-post-form-{{ $post->id }}" action={{ route('gameroom.destroy', $post->id) }} method="POST" style="margin: 0px;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="dropdown-item-delete-post " type="button" onclick="confirmDelete(event, {{ $post->id }})" data-close-dropdown="true">Удалить</button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                            </div>

                            @if ($post->parent_post_id)
                                <div class="parent-link">
                                    <a href="javascript:void(0)" onclick="scrollToPost({{ $post->parent_post_id }})" style="text-decoration: none">
                                        <div class="parent-link-content">
                                            <div style="color: #f4d03f">
                                                {{ $post->parent->character->firstName . ' ' . $post->parent->character->secondName }}
                                            </div>
                                            <div>
                                                {!! nl2br(e(Str::limit($post->parent->content, 100))) !!}
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endif

                            <p>{!! nl2br(e($post->content)) !!}</p>
                            <small>
                                <div style="display: flex; flex-direction: row; justify-content: space-between;">
                                    <div class="post-date">
                                    {{ $post->updated_at->isoFormat('HH:mm DD.MM.YYYY') }}
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

                @if ($selectedLocation && !isset($postContent)) 
                    @include('frontend.gameroom.postPublishForm')
                @elseif ($selectedLocation && isset($postContent))
                    @include('frontend.gameroom.postEditForm')
                @endif


            </div>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div id="delete-post-modal" class="modal">
    <div class="modal-content">
        <h3>Удалить сообщение</h3>
        <p>Вы уверены, что хотите удалить сообщение?</p>
        <div class="modal-buttons">
            <button id="confirm-delete" class="btn btn-danger">Удалить</button>
            <button id="cancel-delete" class="btn btn-secondary">Отмена</button>
        </div>
    </div>
</div>

<script>
    function toggleDropdownPostMenu(button) {
        const dropdownMenu = button.closest('.custom-dropdown-post').querySelector('.dropdown-menu-post');
        const allDropdownMenus = document.querySelectorAll('.dropdown-menu-post');

        allDropdownMenus.forEach(menu => {
            if (menu !== dropdownMenu) {
                menu.classList.remove('show');
            }
        });

        dropdownMenu.classList.toggle('show');

    }

    document.addEventListener('click', function (event) {
        const allDropdownMenus = document.querySelectorAll('.dropdown-menu-post');

        allDropdownMenus.forEach(menu => {
            const customDropdown = menu.closest('.custom-dropdown-post');
            if (!customDropdown.contains(event.target)) {
                menu.classList.remove('show');
            }

            if (event.target.dataset.closeDropdown === 'true') {
                menu.classList.remove('show');
            }

            if (event.target.closest('[data-close-dropdown="true"]')) {
                menu.classList.remove('show');
            }
            
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const postsContainer = document.querySelector('.posts');
        if (postsContainer) {
            postsContainer.scrollTop = postsContainer.scrollHeight;

            const observer = new MutationObserver(function () {
                postsContainer.scrollTop = postsContainer.scrollHeight;
            });

            observer.observe(postsContainer, { childList: true });
        }
    });

    let currentPostId = null;

    function confirmDelete(event, postId) {
        event.preventDefault();
        event.stopPropagation(); 
        currentPostId = postId;
        const modal = document.getElementById('delete-post-modal');
        modal.style.display = 'block';
    }

    document.getElementById('cancel-delete').addEventListener('click', function () {
        const modal = document.getElementById('delete-post-modal');
        modal.style.display = 'none';
        currentThemeId = null;
    });

    document.getElementById('confirm-delete').addEventListener('click', function () {
        if (currentPostId) {
            const form = document.getElementById(`delete-post-form-${currentPostId}`);
            form.submit(); 
        }
        const modal = document.getElementById('delete-post-modal');
        modal.style.display = 'none';
        currentPostId = null;
    });

    function scrollToPost(postId) {
    const postsContainer = document.getElementById('posts-container'); 
    const postElement = document.querySelector(`#post-${postId}`);

    if (postElement && postsContainer) {
            
            const postTop = postElement.offsetTop - postsContainer.offsetTop;

            postsContainer.scrollTo({
                top: postTop,
                behavior: 'smooth' 
            });

            postElement.style.backgroundColor = '#f4d03f20'; 
            setTimeout(() => {
                postElement.style.backgroundColor = ''; 
            }, 2000);
        }
    }


</script>

@endsection

