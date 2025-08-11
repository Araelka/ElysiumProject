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
                                    <a href="javascript:void(0)" data-post-id="{{ $post->id }}" onclick="replyPost(this)" data-close-dropdown="true">
                                        <div style="padding: 5px 10px">
                                            Ответить
                                        </div>
                                    </a>
                            </div>

                        @if (auth()->user()->id == $post->character->user_id && $diffInHours[$post->id] < 24)
                            <div class="dropdown-item-post" style="padding: 0px">
                                    <a href="javascript:void(0)" data-post-id="{{ $post->id }}" onclick="editPost(this)" data-close-dropdown="true">
                                        <div style="padding: 5px 10px">
                                            Редактировать
                                        </div>
                                    </a>
                            </div>
                        @endif
                        @if ((auth()->user()->id == $post->character->user_id || Auth::user()->isModerator()) && $diffInHours[$post->id] < 24)
                            <div  data-post-id="{{ $post->id }}">
                                <form id="delete-post-form-{{ $post->id }}" action={{ route('gameroom.destroy', $post->id) }} method="POST" style="margin: 0px;">
                                    @csrf
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

    <p class='post-content'>{!! $post->content !!}</p>
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