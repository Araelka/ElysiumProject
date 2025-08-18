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
    window.currentLocationId = {{ $selectedLocation->id ?? 'null' }};
</script>

