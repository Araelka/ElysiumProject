<form id="post-form" class="post-form" action={{ route('gameroom.publish') }} method="POST">
    @csrf

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
                <button type="button" class="post-form__button" onclick="submitPostForm(this)">➤</button>
            </div>
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

    function replyPost(button) {
        const postId = button.getAttribute('data-post-id'); 
        const parentPostIdInput = document.getElementById('parent-post-id'); 
        const parentLink = document.getElementById('parent-link');
        const dropdownToggle = document.getElementById('dropdown-toggle');
        
        document.getElementById('post-form').action = `/game-room/publish`;
        parentPostIdInput.value = postId;
        dropdownToggle.disabled = false;
        dropdownToggle.style.cursor = 'pointer';
        dropdownToggle.onmouseover = function () {
        dropdownToggle.style.textDecoration = 'underline';
        };

        dropdownToggle.onmouseout = function () {
            dropdownToggle.style.textDecoration = 'none';
        };

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
                        <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: flex-start;">
                            <a href="javascript:void(0)" onclick="scrollToPost(${postId})" style="text-decoration: none">
                                <div class="parent-link-content">
                                    <div style="color: #f4d03f">${data.character_name}</div>
                                    <div>${data.content}</div>
                                </div>
                            </a>
                            <div class="parent-post-close" onclick="clearParentPost()">&#10006;</div>
                        </div>
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
                        <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: flex-start;">
                            <a href="javascript:void(0)" onclick="scrollToPost(${parentPostId})" style="text-decoration: none">
                                <div class="parent-link-content">
                                    <div style="color: #f4d03f">${data.character_name}</div>
                                    <div>${data.content}</div>
                                </div>
                            </a>
                            <div class="parent-post-close" onclick="clearParentPost()">&#10006;</div>
                        </div>
                        `;
                        parentLink.style.display = 'block';
                    }
                })
                .catch(error => console.error('Ошибка:', error));
        } else if (session('parent_post')) {
            const parentPost = @json(session('parent_post'));
            if (parentPost) {
                parentLink.innerHTML = `
                <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: flex-start;">
                    <a href="javascript:void(0)" onclick="scrollToPost(${parentPost.id})" style="text-decoration: none">
                        <div class="parent-link-content">
                            <div style="color: #f4d03f">${parentPost.character_name}</div>
                            <div>${parentPost.content}</div>
                        </div>
                    </a>
                    <div class="parent-post-close" onclick="clearParentPost()">&#10006;</div>
                </div>
                `;
                parentLink.style.display = 'block';
            }
        }
    });

    function clearParentPost() {
        const parentLink = document.getElementById('parent-link');
        const parentPostIdInput = document.getElementById('parent-post-id');

        if (parentLink) {
            parentLink.innerHTML = '';
            parentLink.style.display = 'none'; 
        }

        if (parentPostIdInput) {
            parentPostIdInput.value = ''; 
        }
    }

    function clearEditPost() {
        const parentLink = document.getElementById('parent-link');
        const parentPostIdInput = document.getElementById('parent-post-id');
        const postText = document.getElementById('post-text');
        const postId = document.getElementById('post-id');
        const characterId = document.getElementById('selected-character-id');
        const dropdownToggle = document.getElementById('dropdown-toggle');

        document.getElementById('post-form').action = `/game-room/publish`;

        if (parentLink) {
            parentLink.innerHTML = '';
            parentLink.style.display = 'none'; 
            postText.value = '';
            postId.value = null;
            characterId.value = null;
            dropdownToggle.innerText = 'Выберите персонажа';
            dropdownToggle.disabled = false;
            dropdownToggle.style.cursor = 'pointer';
            
            dropdownToggle.onmouseover = function () {
            dropdownToggle.style.textDecoration = 'underline';
            };

            dropdownToggle.onmouseout = function () {
                dropdownToggle.style.textDecoration = 'none';
            };
        }

        if (parentPostIdInput) {
            parentPostIdInput.value = ''; 
        }
    }


    function editPost(button) {
        const postId = button.getAttribute('data-post-id');

        fetch(`/game-room/get-post-content/${postId}`)
            .then(response => response.json())
            .then(data => {                
                document.getElementById('post-id').value = postId; 
                document.getElementById('selected-character-id').value = data.character_uuid;
                document.querySelector('.dropdown-toggle').innerText = `${data.character_name}`; 
                document.getElementById('post-text').value = data.content; 
                
                const dropdownToggle = document.getElementById('dropdown-toggle');
                dropdownToggle.disabled = true;
                dropdownToggle.style.cursor = 'auto';
                dropdownToggle.onmouseover = function () {
                dropdownToggle.style.textDecoration = 'none';
                };

                dropdownToggle.onmouseout = function () {
                    dropdownToggle.style.textDecoration = 'none';
                };

                const parentLink = document.getElementById('parent-link');
                    parentLink.innerHTML = `
                        <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: flex-start;">
                            <a href="javascript:void(0)" onclick="scrollToPost(${data.id})" style="text-decoration: none">
                                <div class="parent-link-content">
                                    <div style="color: #f4d03f">${data.character_name}</div>
                                    <div>${data.content}</div>
                                </div>
                            </a>
                            <div class="parent-post-close" onclick="clearEditPost()">&#10006;</div>
                        </div>
                    `;
                    parentLink.style.display = 'block';

                document.getElementById('post-form').action = `/game-room/edit/${postId}`;
            })
            .catch(error => console.error('Ошибка:', error));
    }
    
    function submitPostForm(btn) {
        btn.disabled = true;

        const form = document.getElementById('post-form');
        const formData = new FormData(form);
                

        fetch(form.action, {
            method: form.method,
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('#post-form input[name="_token"]').value,
            },
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Ошибка при отправке.');
                }
                return response.json();
            })
            .then(data => {
                                                
                clearEditPost();

            })
            .finally(() => {
                btn.disabled = false;
            });
    }

    function updatePostInDOM(postData) {

        const postElement = document.getElementById(`post-${postData.postId}`);
        if (postElement) {
            
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = postData.html;

            postElement.replaceWith(tempDiv.firstChild);
        }
    }

    

    function deletePostInDOM(postData) {

        const postElement = document.getElementById(`post-${postData.postId}`);
                
        if (postElement) {
            postElement.remove();
        }
        
}

</script>