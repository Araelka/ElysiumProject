@extends('frontend.admin.admin')
<link rel="stylesheet" href="{{ asset('css/character.css') }}">
@section('title', 'Редактирование персонажа: ' . $character->firstName . ' ' . $character->secondName)
@section('table')


<div class="row w-100 h-100" style="overflow-y: auto !important"> 
    <div class="col-md-12 main-content d-flex flex-column justify-content-start" style="background-color: transparent; box-shadow: none;">
        <div class="character-form-container" style="padding: 0px; margin-bottom: 0px; overflow-y: clip;">
            <div class="character-info-container">
                <div class="character-info-section">
                    <div class="character-main-info">
                        @if ($character->images->first())
                            <div class="image-view" style="width:310px; height:310px;" >
                                <img src="{{ asset('storage/' . $character->images->first()->path) }}" class="rounded-circle">
                            </div>
                        @else
                            <div class="image-view" style="width:310px; height:310px;" >
                            @if ($character->gender == 'Мужской')
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
                                        <div style="display: flex; flex-direction: row; gap: 10px; align-items: center;">
                                            <div class="character-status-select">
                                                <form id="statusForm" action={{ route('admin.chatgeCharacterStatus', $character->id) }} style="margin-bottom: 0px" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <select id="status" name="status_id" onchange="document.getElementById('statusForm').submit()">
                                                        @foreach ($statuses as $status)
                                                            <option value={{ $status->id }} {{ $character->status_id == $status->id ? 'selected' : '' }}> 
                                                                {{ $status->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            </div>   
                                        </div>                             
                                </div>
                                <hr>
                                <div class="character-main-info-double-content">
                                    <div style="width: 50%">
                                        {{ $character->firstName . ' ' . $character->secondName }}
                                    </div>
                                </div>
                                <div>
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
                                            {{ $character->age }}
                                        </div>
                                        
                                        <div style="width: 50%">
                                            {{ $character->gender }} 
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
                                            {{ $character->height }}
                                        </div>
                                        
                                        <div style="width: 50%">
                                            {{ $character->weight }} 
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <strong>Национальность</strong> 
                                    <hr>
                                    {{ $character->nationality }}
                                </div>

                                    <div>
                                    <strong>Место жительства</strong> 
                                    <hr>
                                    {{ $character->residentialAddress }}
                                </div>
                        </div>
                    </div>
                </div>

                <div class="character-main-info-content"> 
                    <div>
                        <strong>Род деятельности</strong> 
                        <hr>
                        {{ $character->activity }}
                    </div>

                    <div>
                        <details>
                            <summary><strong>Характер</strong> 
                            </summary>
                            <hr>
                            <div>{!! nl2br(e($character->personality)) !!}</div>
                        </details>
                    </div>

                    @if ($character->description())
                        @if($character->description()->biography)
                        <div>
                            <details>
                                <summary><strong>Биография</strong> </summary>
                                <hr>
                                <div>{!! nl2br(e($character->description()->biography)) !!}</div>
                            </details>
                        </div>
                        @endif

                        @if($character->description()->description)
                            <div>
                                <details>
                                    <summary><strong>Внешность</strong> </summary>
                                    <hr>
                                    <div>{!! nl2br(e($character->description()->description)) !!}</div>
                                </details>
                            </div>
                        @endif

                        @if($character->description()->headcounts)
                            <div>
                                <details>
                                    <summary><strong>Факты</strong> </summary>
                                    <hr>
                                    <div>{!! nl2br(e($character->description()->headcounts)) !!}</div>
                                </details>
                            </div>
                        @endif
                    @endif

                    @if ($character->attributes->first())
                    <div>
                        <strong>Навыки</strong> 
                        <hr>
                    @foreach ($character->attributes as $attribute)
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
                                            <div class="skill" style="background-image: url({{ asset( $skill->skill->image_path) }}); background-size: contain; height: 230px;">
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

                <div style="display: flex; flex-direction: row; justify-content: flex-end; gap: 10px;">
                    <div>
                        <button class="save-button">
                            <a href="{{ route('characters.showMainInfo', $character->uuid) }}">Редактировать</a>
                        </button>
                    </div>
                    <div>
                        <form action="{{ route('characters.increaseAvailablePoints', $character->uuid) }}" method="POST" class="single-delete-form" style="display:inline-block;">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="save-button">Повысить уровень</button>
                        </form>
                    </div>
                    <div>
                        <form action="{{ route('characters.characterDestoy', $character->uuid) }}" method="POST" class="single-delete-form" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-button">Удалить</button>
                        </form>
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
        @if ($character)
            @foreach ($character->attributes as $id => $attribute)
                updateDiamonds('{{ $attribute->id }}', {{ $attribute->level }});
                updateSkillDiamonds('{{ $attribute->id }}', {{ $attribute->level}} );
        @endforeach
        @endif
    });

</script>

@endsection