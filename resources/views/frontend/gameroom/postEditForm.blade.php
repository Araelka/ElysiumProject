<form class="post-form" action={{ route('gameroom.edit', $post->id) }} method="POST">
    @csrf
    @method('PUT')
    <div style="display: flex; align-items: flex-end;">
        <input type="hidden" name="character_uuid" value={{ $postContent->character->uuid }}>
        <span style="padding: 1px 6px; font-weight: bold;">{{ $postContent->character->firstName }} {{ $postContent->character->secondName }}</span>
    </div>

    <div class="post-form__group">
            <div style="width: 100%">
                @if ($parentPost)
                    <div class="parent-link">
                        <a href="javascript:void(0)" onclick="scrollToPost({{ $parentPost->id }})" style="text-decoration: none">
                            <div class="parent-link-content">
                                <div style="color: #f4d03f">
                                    {{ $parentPost->character->firstName . ' ' . $parentPost->character->secondName }}
                                </div>
                                <div>
                                    {!! nl2br(e(Str::limit($parentPost->content, 100))) !!}
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

                <input type="hidden" name="post_id" value={{ $postContent->id }}>
                <input type="hidden" name="location_id" value={{ $selectedLocation->id }}>
                <textarea id="post-text" name="post_text" class="post-form__input">{{ $postContent->content }}</textarea>
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
</script>

