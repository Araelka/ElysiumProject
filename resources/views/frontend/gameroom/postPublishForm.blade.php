<form class="post-form" action={{ route('gameroom.publish') }} method="POST">
    @csrf

    <div style="display: flex; align-items: flex-end;">
        <div class="custom-dropdown">
            <div>
                <button type="button" class="dropdown-toggle" onclick="toggleDropdown()">Выберите персонажа</button>
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
            <div style="width: 100%">
                <div id="parent-link" class="parent-link" style="display: none;">
                    @if (session('parent_post'))
                        <a href="javascript:void(0)" onclick="scrollToPost({{ session('parent_post')['id'] }})" style="text-decoration: none">
                            <div class="parent-link-content">
                                <div style="color: #f4d03f">{{ session('parent_post')['character_name'] }}</div>
                                <div>{{ session('parent_post')['content'] }}</div>
                            </div>
                        </a>
                    @endif
                </div>

                <input type="hidden" name="location_id" value={{ $selectedLocation->id }}>
                <input type="hidden" id="parent-post-id" name="parent_post_id" value={{ old('parent_post_id') }}>
                <textarea id="post-text" name="post_text" class="post-form__input" placeholder="Сообщение">{{ old('post_text') }}</textarea>
            </div>
            <div style="display: flex; align-items: flex-end;">
                <button type="submit" class="post-form__button">➤</button>
            </div>
    </div>
</form>

<script>
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
        const textarea = document.getElementById('post-text');

        function autoResize() {
            textarea.style.height = 'auto'; 
            textarea.style.height = `${textarea.scrollHeight}px`; 
        }

        textarea.addEventListener('input', autoResize);
        autoResize();

        document.addEventListener('click', function (event) {
            const dropdownCharacterMenu = document.getElementById('character-dropdown');
            const customDropdown = document.querySelector('.custom-dropdown');

            if (!customDropdown.contains(event.target)) {
                dropdownCharacterMenu.classList.remove('show');
            }
        });
    });

    function setParentPostId(button) {
        const postId = button.getAttribute('data-post-id'); 
        const parentPostIdInput = document.getElementById('parent-post-id'); 
        const parentLink = document.getElementById('parent-link');

        parentPostIdInput.value = postId;

        if (postId) {
            fetch(`/game-room/get-post-content/${postId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Сообщение не найдено');
                    }
                    return response.json();
                })
                .then(data => {
                    if (parentLink) {
                        parentLink.innerHTML = '';

                        parentLink.innerHTML = `
                            <a href="javascript:void(0)" onclick="scrollToPost(${postId})" style="text-decoration: none">
                                <div class="parent-link-content">
                                    <div style="color: #f4d03f">${data.character_name}</div>
                                    <div>${data.content}</div>
                                </div>
                            </a>
                        `;
                        parentLink.style.display = 'block'; 
                    }
                })
                .catch(error => console.error('Ошибка:', error));
        } else {
            if (parentLink) {
                parentLink.style.display = 'none';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const parentPostId = "{{ old('parent_post_id') ?? session('parent_post.id') }}";
        const parentLink = document.getElementById('parent-link');

        if (parentPostId && parentLink) {
            fetch(`/game-room/get-post-content/${parentPostId}`)
                .then(response => response.json())
                .then(data => {
                    if (parentLink) {
                        parentLink.innerHTML = `
                            <a href="javascript:void(0)" onclick="scrollToPost(${parentPostId})" style="text-decoration: none">
                                <div class="parent-link-content">
                                    <div style="color: #f4d03f">${data.character_name}</div>
                                    <div>${data.content}</div>
                                </div>
                            </a>
                        `;
                        parentLink.style.display = 'block';
                    }
                })
                .catch(error => console.error('Ошибка:', error));
        } else if (session('parent_post')) {
            const parentPost = @json(session('parent_post'));
            if (parentPost) {
                parentLink.innerHTML = `
                    <a href="javascript:void(0)" onclick="scrollToPost(${parentPost.id})" style="text-decoration: none">
                        <div class="parent-link-content">
                            <div style="color: #f4d03f">${parentPost.character_name}</div>
                            <div>${parentPost.content}</div>
                        </div>
                    </a>
                `;
                parentLink.style.display = 'block';
            }
        }
    });
</script>