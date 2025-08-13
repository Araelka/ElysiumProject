<form id="post-form" class="post-form" action={{ route('gameroom.publish') }} method="POST" style="position: relative">
    @csrf

    <button id="button-bottom" type="button" class="button-bottom" onclick="scrollBottomPostsContainer()">⮟</button>

    <div style="display: flex; align-items: flex-end;">
        <div class="custom-dropdown">
            <div>
                <button id="dropdown-toggle" type="button" class="dropdown-toggle" onclick="toggleDropdown()">Выберите персонажа</button>
            </div>

            <div class="dropdown-menu" id="character-dropdown">
                @foreach ($characters as $character)
                    <div class="dropdown-item" data-character-id="{{ $character->uuid }}" onclick="selectCharacter(this)">
                        @if ($character->images->first())
                            <img src="{{ asset('storage/' . $character->images->first()?->path ) }}" alt="Аватар персонажа" class="dropdown-avatar">
                        @elseif ($character->gender == 'Мужской')
                            <img src="{{ asset('images/characters/characterMale.jpg') }}" alt="Аватар персонажа" class="dropdown-avatar">
                        @else 
                            <img src="{{ asset('images/characters/characterFemale.jpg') }}" alt="Аватар персонажа" class="dropdown-avatar">
                        @endif
                        
                        <span>{{ $character->firstName }} {{ $character->secondName }}</span>
                    </div>
                @endforeach
            </div>
            <input type="hidden" id="selected-character-id" name="character_uuid" required>
        </div>
    </div>

    <div class="post-form__group">
        <div id="parent-link" class="parent-link" style="display: none;">
                    @if (session('parent_post'))
                    <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: flex-start;">
                        <a href="javascript:void(0)" onclick="scrollToPost({{ session('parent_post')['id'] }})" style="text-decoration: none">
                            <div class="parent-link-content">
                                <div style="color: #f4d03f">{{ session('parent_post')['character_name'] }}</div>
                                <div>{{ session('parent_post')['content'] }}</div>
                            </div>
                        </a>
                        <div class="parent-post-close" onclick="clearParentPost()">&#10006;</div>
                    </div>
                    @endif
                </div>
            <div style="display: flex; flex-direction: row; align-items: flex-end; justify-content: space-between;">
            <div style="width: 100%">

                <input type="hidden" name="location_id" value={{ $selectedLocation->id }}>
                <input type="hidden" id="post-id" name="post_id" value="">
                <input type="hidden" id="parent-post-id" name="parent_post_id" value={{ old('parent_post_id') }}>
                <textarea id="post-text" name="post_text" class="post-form__input" placeholder="Сообщение">{{ old('post_text') }}</textarea>
            </div>
            <div style="display: flex; align-items: flex-end;">
                <button id="submit-post" type="button" class="post-form__button" onclick="submitPostForm(this)">➤</button>
            </div>
        </div>
    </div>
</form>

<script>

    let currentPage = 1;
    let isLoading = false;
    let hasMorePosts = true;

    document.addEventListener('DOMContentLoaded', function () {
        const postsContainer = document.getElementById('posts-container');

        if (postsContainer) {
            postsContainer.scrollTop = 0;
        }

        function isScrollNearTop(container) {            
            return container.scrollHeight + container.scrollTop - container.clientHeight  <= 300 ;
        }

        
        postsContainer.addEventListener('scroll', () => {
            if (isScrollNearTop(postsContainer)) {
                loadPosts();
            }
        });

        loadPosts();        

    });

    document.addEventListener('DOMContentLoaded', function () {
        const postsContainer = document.getElementById('posts-container');
        const buttonBottom = document.getElementById('button-bottom');

        
        postsContainer.addEventListener('scroll', () => {
            if (postsContainer.scrollTop <= -100) {
                buttonBottom.style.display = 'block';
            } else {
                buttonBottom.style.display = 'none';
            }
        });
        
    });

    function scrollBottomPostsContainer () {
        const postsContainer = document.getElementById('posts-container');
        postsContainer.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    let currentSearchQuery = null;

    async function loadPosts() {
        if (isLoading || !hasMorePosts) return;

        isLoading = true;
        const postsContainer = document.getElementById('posts-container');
        const locationId = {{ $selectedLocation->id ?? 'null' }};
        window.currentLocationId = {{ $selectedLocation->id ?? 'null' }};

        try {
            if (!locationId) {
                throw new Error('Локация не выбрана');
            }

            let url = `/game-room/load-posts?location_id=${locationId}&page=${currentPage}`;

            if (currentSearchQuery) {
                url += `&search=${encodeURIComponent(currentSearchQuery)}`;
            }

            const response = await fetch(url);
            if (!response.ok) {
                if(response.status === 403) {
                    throw new Error('Доступ запрещен.');
                } else if (response.status === 400) {
                    throw new Error('Неверный запрос.');
                } else {
                    throw new Error(`Ошибка сети: ${response.status}`);
                }
            }

            const data = await response.json();

            if (data.posts && data.posts.length > 0) {
                
                if (currentPage === 1 || (currentPage === 1 && currentSearchQuery)) {
                    if (postsContainer) postsContainer.innerHTML = '';
                }

                await addPostsToDOMBatch(data.posts);
                currentPage++;
                hasMorePosts = data.hasMore;

                if (currentPage === 2 && !currentSearchQuery && isInitialLoad) { 
                    postsContainer.scrollTo({
                        top: postsContainer.scrollHeight, 
                        behavior: 'instant'
                    });
                    isInitialLoad = false;
                }

            } else if (data.posts && data.posts.length === 0 && currentPage === 1) {
                if (postsContainer) {
                    postsContainer.innerHTML = currentSearchQuery ?
                        '<div class="no-results">По вашему запросу ничего не найдено.</div>' :
                        '<div class="no-results">Постов пока нет.</div>';
                }
                hasMorePosts = false;
            }
        } catch (error) {
            console.error('Ошибка загрузки постов:', error);
            if (currentPage === 1) { 
                if (postsContainer) {
                    postsContainer.innerHTML = `<div class="error-loading">Ошибка загрузки постов: ${error.message}</div>`;
                }
            }
            hasMorePosts = false;
        } finally {
            isLoading = false;
        }
    }

    function performSearch(query) {
        const trimmedQuery = query.trim();
        currentSearchQuery = trimmedQuery.length > 0 ? trimmedQuery : null;

        currentPage = 1;
        hasMorePosts = true; 

        const postsContainer = document.getElementById('posts-container');
        if (postsContainer) {
            postsContainer.innerHTML = '';
            postsContainer.innerHTML = '<div class="loading">Поиск...</div>';
        }

        loadPosts();
    }

    async function scrollToPost(postId) {
        let postElement = document.querySelector(`#post-${postId}`);
        const postsContainer = document.getElementById('posts-container');
        
        if (!postsContainer) return;

        while (!postElement && hasMorePosts && !isLoading) {
            await loadPosts(); 
            postElement = document.querySelector(`#post-${postId}`);

            if (!postElement && hasMorePosts && !isLoading) {
                postsContainer.scrollTo({
                    top: -(postsContainer.scrollHeight - postsContainer.scrollTop - postsContainer.clientHeight),
                    behavior: 'smooth'
                });
                
                await new Promise(resolve => setTimeout(resolve, 300));
            }
        }

        if (postElement) {
            const postTop = postElement.offsetTop - postsContainer.offsetTop;
            
            postsContainer.scrollTo({
                top: postTop,
                behavior: 'smooth'
            });

            postElement.style.backgroundColor = '#f4d03f20';
            setTimeout(() => {
                postElement.style.backgroundColor = '';
            }, 2000);
        } else {
            console.warn(`Пост не найден`);
        }
    }
    
    function toggleDropdown() {
        const dropdownCharacterMenu = document.getElementById('character-dropdown');
        dropdownCharacterMenu.classList.toggle('show');
    }

    function selectCharacter(item) {
        const characterId = item.getAttribute('data-character-id');
        const characterName = item.querySelector('span').innerText;

        document.querySelector('.dropdown-toggle').innerText = characterName;

        document.getElementById('selected-character-id').value = characterId;

        toggleDropdown();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const searchForm = document.getElementById('search-form');
        const searchInput = document.getElementById('search-input');
        const clearSearchButton = document.getElementById('clear-search');

        if (searchForm) {
            searchForm.addEventListener('submit', function(event) {
                event.preventDefault(); 
                clearSearchButton.style.display = 'block';

                if (searchInput) {
                    const searchTerm = searchInput.value; 
                    performSearch(searchTerm);
                }
            });
        }

        if (clearSearchButton && searchInput) {
            clearSearchButton.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopImmediatePropagation();
                clearSearchButton.style.display = 'none';
                searchInput.value = ''; 
                performSearch(''); 
            });
        }
    });

</script>