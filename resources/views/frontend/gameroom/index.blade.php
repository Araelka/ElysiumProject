@extends('frontend.layout.layout')

<meta name="base-url" content="{{ url('/') }}/">

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
                    <div>
                        @include('frontend.gameroom.post', $post)
                    </div>
                    @endforeach

                    @endif
                </div>

                @if ($selectedLocation) 
                    @include('frontend.gameroom.postPublishForm')
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
        currentPostId = null;
    });

    document.getElementById('confirm-delete').addEventListener('click', function () {
        if (currentPostId) {
            
            const form = document.getElementById(`delete-post-form-${currentPostId}`);
            
            fetch(`/game-room/destroy/${currentPostId}`, {        
                method: form.method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector(`#delete-post-form-${currentPostId} input[name="_token"]`).value
                },
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Ошибка при удалении.');
                    }
                    return response.json();
                })
                .finally(() => {
                    modal.style.display = 'none';
                });
        }
        
        const modal = document.getElementById('delete-post-modal');
        modal.style.display = 'none';
        currentPostId = null;
    });

    

    

</script>


<script src="{{ asset('js/post.js') }}"></script>
@endsection

