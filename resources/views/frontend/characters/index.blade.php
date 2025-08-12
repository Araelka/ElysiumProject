@extends('frontend.layout.layout')
@if ($selectedCharacter)
    @section('title',  $selectedCharacter->firstName . ' ' . $selectedCharacter->secondName )
@else
    @section('title', 'Персонажи')
@endif
<link rel="stylesheet" href="{{ asset('css/character.css') }}">


@section('content')
<div class="double-page">
    <div class="container d-flex justify-content-center align-items-stretch">
        <div class="row w-100 h-100">
            <!-- Боковая панель (20%) -->
            <div class="col-md-2 sidebar d-flex flex-column justify-content-start">
                <h3>Персонажи</h3>
                <ul class="topics-list">
                    @foreach ($characters as $character)
                        <li>
                            <a href="?character={{ $character->uuid }}"  class="topic-link {{ $selectedCharacter && $selectedCharacter->id == $character->id ? 'active' : '' }}">
                                @if ($character->available_points > 0 && $character->isApproved())
                                    <div class="character-available-points-container">
                                        <span class="character-available-points">&#9679;</span>
                                        <div class="character-ring"></div>
                                        <div class="character-ring"></div>
                                        <div class="character-ring"></div>
                                    </div>
                                @endif
                                
                                <span class="character-name">
                                    {{ $character->firstName . ' ' . $character->secondName }}
                                </span>

                                <span class="character-status">
                                @if ($character->isPreparing() || $character->isConsideration())
                                    <div class="status-preparing">
                                        {{ $character->status->name }}
                                    </div>
                                @elseif ($character->isApproved())
                                        <div class="status-approved">
                                            {{ $character->status->name }}
                                        </div>
                                @elseif ($character->isRejected())
                                <div class="status-rejected">
                                    {{ $character->status->name }}
                                </div>
                                @elseif ($character->isArchive())
                                        <div class="status-archive">
                                            {{ $character->status->name }}
                                        </div>
                                @elseif ($character->isDead())
                                        <div class="status-rejected">
                                            {{ $character->status->name }}
                                        </div>
                                @endif
                                </span>
                            </a>
                        </li>
                    @endforeach
                    @if (auth()->user()->getCountvailableCharacters() < 5)
                        <a href={{ route('characters.showMainInfo') }} class="topic-link-button">Создать персонажа</a>
                    @endif
                </ul>
            </div>

            <!-- Основной контент (80%) -->
            <div class="col-md-10 content d-flex flex-column justify-content-start">
                <div class="character-form-container">
                    @if ($selectedCharacter)
                        <div class="character-info-container">
                            @if($selectedCharacter->comment && $selectedCharacter->isRejected())
                                <div style="background-color: ff4d4d; border-radius: 5px; padding: 10px; color: 1a1a40;">
                                    <strong>Причина отклонения:</strong>

                                    <div>{!! nl2br(e($selectedCharacter->comment)) !!}</div>
                                </div>
                            @endif

                            <div class="character-info-section">
                                <div class="character-main-info">
                                        @if ($selectedCharacter->images->first())
                                            <div class="image-view" style="width:310px; height:310px;" >
                                                <img src="{{ asset('storage/' . $selectedCharacter->images->first()->path) }}" class="rounded-circle">
                                            </div>
                                        @else
                                            <div class="image-view" style="width:310px; height:310px;" >
                                            @if ($selectedCharacter->gender == 'Мужской')
                                                    <img src="{{ asset('images/characters/characterMale.jpg') }}" class="rounded-circle">
                                            @else
                                                    <img src="{{ asset('images/characters/characterFemale.jpg') }}" class="rounded-circle">
                                            @endif
                                            </div>
                                        @endif
                                    <div class="character-main-info-content">
                                        <div>
                                            <div class="character-main-info-double-content">
                                                <div style="width: 50%">
                                                    <strong>Имя Фамилия</strong> 
                                                </div>
                                                @if ($selectedCharacter->isArchive()  && $diffInDays > 14)
                                                    <div>
                                                        <form id="archiveForm" action="{{ route('characters.changeArchiveStatus',  $selectedCharacter->uuid) }}" style="margin: 0px;" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="delete-character-button" style="color: #f4d03f;" id="openArchiveModal"><strong>Вернуть из архива</strong></button>
                                                        </form>
                                                    </div>
                                                @endif
                                                @if ($selectedCharacter->isApproved())
                                                    <div style="display: flex; flex-direction: row; gap: 10px; margin-top: -2px;">
                                                        @if ($selectedCharacter->available_points > 0)
                                                            <div>
                                                                <a href={{ route('characters.showUpdateSkills', $selectedCharacter->uuid) }} class="editCharacter"><strong>Повышение уровня</strong> </a>
                                                            </div>
                                                        @endif
                                                        @if ($diffInDays > 14)
                                                            <div>
                                                                <form id="archiveForm" action="{{ route('characters.changeArchiveStatus',  $selectedCharacter->uuid) }}" style="margin: 0px;" method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button type="submit" class="delete-character-button" style="color: gray;" id="openArchiveModal"><strong>В архив</strong></button>
                                                                </form>
                                                            </div>    
                                                        @endif
                                                    </div>
                                                @endif
                                                @if (!$selectedCharacter->isApproved() && !$selectedCharacter->isArchive() && !$selectedCharacter->isConsideration() && !$selectedCharacter->isDead())
                                                <div style="display: flex; flex-direction: row; gap: 10px; margin-top: -2px;">
                                                    <div>
                                                        <a href={{ route('characters.showMainInfo', $selectedCharacter->uuid) }} class="editCharacter"><strong>Редактировать</strong> </a>
                                                    </div>
                                                        <div>
                                                            <form action="{{ route('characters.characterDestoy',  $selectedCharacter->uuid) }}" style="margin: 0px;" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="delete-character-button"><strong>Удалить</strong></button>
                                                            </form>
                                                    </div>
                                                </div>
                                                @endif
                                                
                                            </div>
                                            <hr>
                                            <div class="character-main-info-double-content">
                                                <div style="width: 50%">
                                                    {{ $selectedCharacter->firstName . ' ' . $selectedCharacter->secondName }}
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <div class="character-main-info-double-content">
                                                <div style="width: 50%">
                                                    <strong>Возраст</strong> 
                                                </div>
                                                
                                                <div style="width: 50%">
                                                    <strong>Пол</strong> 
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="character-main-info-double-content">
                                                <div style="width: 50%">
                                                    {{ $selectedCharacter->age }}
                                                </div>
                                                
                                                <div style="width: 50%">
                                                    {{ $selectedCharacter->gender }} 
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="character-main-info-double-content">
                                                <div style="width: 50%">
                                                    <strong>Рост</strong> 
                                                </div>
                                                
                                                <div style="width: 50%">
                                                    <strong>Вес</strong> 
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="character-main-info-double-content">
                                                <div style="width: 50%">
                                                    {{ $selectedCharacter->height }}
                                                </div>
                                                
                                                <div style="width: 50%">
                                                    {{ $selectedCharacter->weight }} 
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <strong>Национальность</strong> 
                                            <hr>
                                            {{ $selectedCharacter->nationality }}
                                        </div>

                                         <div>
                                            <strong>Место жительства</strong> 
                                            <hr>
                                            {{ $selectedCharacter->residentialAddress }}
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>

                            <div class="character-main-info-content"> 

                                <div>
                                    <strong>Род деятельности</strong> 
                                    <hr>
                                    {{ $selectedCharacter->activity }}
                                </div>

                                <div>
                                    <details>
                                        <summary><strong>Характер</strong> 
                                        </summary>
                                        <hr>
                                        <div>{!! nl2br(e($selectedCharacter->personality)) !!}</div>
                                    </details>
                                </div>

                                @if ($selectedCharacter->description())
                                    @if($selectedCharacter->description()->biography)
                                    <div>
                                        <details>
                                            <summary><strong>Биография</strong> </summary>
                                            <hr>
                                            <div>{!! nl2br(e($selectedCharacter->description()->biography)) !!}</div>
                                        </details>
                                    </div>
                                    @endif

                                    @if($selectedCharacter->description()->description)
                                        <div>
                                            <details>
                                                <summary><strong>Внешность</strong> </summary>
                                                <hr>
                                                <div>{!! nl2br(e($selectedCharacter->description()->description)) !!}</div>
                                            </details>
                                        </div>
                                    @endif

                                    @if($selectedCharacter->description()->headcounts)
                                        <div>
                                            <details>
                                                <summary><strong>Факты</strong> </summary>
                                                <hr>
                                                <div>{!! nl2br(e($selectedCharacter->description()->headcounts)) !!}</div>
                                            </details>
                                        </div>
                                    @endif
                                @endif

                                @if ($selectedCharacter->attributes->first())
                                <div>
                                    <strong>Навыки</strong> 
                                    <hr>
                                @foreach ($selectedCharacter->attributes as $attribute)
                                        <div class="attributes">
                                            <div class="attribute">
                                                <div class="attribute-content-name d-flex justify-content-between align-items-center">
                                                    <div style="font-size: 16px">
                                                        <label>{{ $attribute->attribute->name }}</label>
                                                        <label id="attribute-level-{{ $attribute->id }}"></label>
                                                    </div>
                                                </div>
                                                <div class="slills">
                                                    @foreach ($attribute->skills() as $skillId => $skill)
                                                        <div class="skill" style="background-image: url({{ asset( $skill->skill->image_path) }}); background-size: contain; width: 122px; height: 173px;">
                                                            <div class="skill-content">
                                                                <div class="skill-value-content">
                                                                    <span data-attribute-id="{{ $attribute->id }}" id="skill-value-{{ $skill->id }}" class="skill-value">{{ $skill->getLevelSkill() }}</span>
                                                                </div>

                                                                <div style="text-align: center; display: flex; flex-direction: column;">
                                                                    <label id="skill-level-{{ $skill->id }}"></label>
                                                                    <label style="word-wrap: break-word; "><strong>{{ $skill->skill->name }}</strong></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                @endforeach
                                </div>
                                @endif
                            </div>
                            
                        

                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    function updateDiamonds(attributeId, value) {
        const diamondsContainer = document.getElementById(`attribute-level-${attributeId}`);
        diamondsContainer.innerHTML = ''; // Очищаем текущие ромбики

        // Добавляем нужное количество ромбиков
        for (let i = 0; i < value; i++) {
            const diamond = document.createElement('label');
            diamond.textContent = '◆';
            diamondsContainer.appendChild(diamond);
        }

    }

    function updateSkillDiamonds(attributeId, attributeLevel) {
        const skillValues = document.querySelectorAll(`[data-attribute-id="${attributeId}"]`);

        skillValues.forEach(skillValue => {
            const skillId = skillValue.parentElement.parentElement.querySelector('label').id;
            const diamondsSkillContainer = document.getElementById(skillId);

            // Очищаем текущие ромбики
            diamondsSkillContainer.innerHTML = '';

            // Получаем количество вкаченных очков для навыка
            const skillPoints = parseInt(skillValue.textContent) - attributeLevel;

            // Добавляем закрашенные ромбики
            for (let i = 0; i < skillPoints; i++) {
                const filledDiamond = document.createElement('label');
                filledDiamond.textContent = '◆'; // Закрашенный ромбик
                diamondsSkillContainer.appendChild(filledDiamond);
            }

            // Добавляем пустые ромбики
            for (let i = skillPoints; i < attributeLevel; i++) {
                const emptyDiamond = document.createElement('label');
                emptyDiamond.textContent = '◇'; // Пустой ромбик
                diamondsSkillContainer.appendChild(emptyDiamond);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        @if ($selectedCharacter)
            @foreach ($selectedCharacter->attributes as $id => $attribute)
                updateDiamonds('{{ $attribute->id }}', {{ $attribute->level }});
                updateSkillDiamonds('{{ $attribute->id }}', {{ $attribute->level}} );
        @endforeach
        @endif
    });

</script>

@endsection

