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
                <input type="hidden" name="location_id" value={{ $selectedLocation->id }}>
                <textarea id="post-text" name="post_text" class="post-form__input"></textarea>
            </div>
            <div style="display: flex; align-items: flex-end;">
                <button type="submit" class="post-form__button">➤</button>
            </div>
    </div>
</form>

<script>
    function toggleDropdown() {
        const dropdownMenu = document.getElementById('character-dropdown');
        dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
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
            const dropdownMenu = document.getElementById('character-dropdown');
            const customDropdown = document.querySelector('.custom-dropdown');

            if (!customDropdown.contains(event.target)) {
                dropdownMenu.style.display = 'none';
            }
        });
    });
</script>