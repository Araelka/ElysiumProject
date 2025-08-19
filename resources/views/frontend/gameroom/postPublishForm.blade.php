<form id="post-form" class="post-form" action={{ route('gameroom.publish') }} method="POST" style="position: relative">
    @csrf

    <button id="button-bottom" type="button" class="button-bottom" onclick="handleBottomButton()">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path d="M12 17.414 3.293 8.707l1.414-1.414L12 14.586l7.293-7.293 1.414 1.414L12 17.414z"/></svg>
    </button>


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
                <button id="submit-post" type="button" class="post-form__button" onclick="submitPostForm(this)" style="padding: 3px">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30px" viewBox="0 0 24 24"><path class="send-svg" d="M21.707 2.293a1 1 0 0 0-1.069-.225l-18 7a1 1 0 0 0 .145 1.909l8.379 1.861 1.862 8.379a1 1 0 0 0 .9.78L14 22a1 1 0 0 0 .932-.638l7-18a1 1 0 0 0-.225-1.069zm-7.445 15.275L13.1 12.319l2.112-2.112a1 1 0 0 0-1.414-1.414L11.681 10.9 6.432 9.738l12.812-4.982z" data-name="Share"/></svg>
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    window.currentLocationId = {{ $selectedLocation->id ?? 'null' }};
</script>

