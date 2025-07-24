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
                            <a href="?character_id={{ $character->id }}"  class="topic-link {{ $selectedCharacter && $selectedCharacter->id == $character->id ? 'active' : '' }}">
                                <span class="character-name">
                                    {{ $character->firstName . ' ' . $character->secondName }}
                                </span>
                                <span class="character-status">
                                    {{ $character->status->name }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                    <a href={{ route('characters.showMainInfo') }} class="topic-link-button">Создать персонажа</a>
                </ul>
            </div>

            <!-- Основной контент (80%) -->
            <div class="col-md-10 content d-flex flex-column justify-content-start">
                <div class="character-form-container">
                    @if ($selectedCharacter)
                        <div class="character-info-container">
                            <div class="character-info-section">
                                <div class="character-main-info">
                                        @if ($selectedCharacter->images->first())
                                            <div class="image-view" style="width:310px; height:310px;" >
                                                <img src="{{ asset('storage/' . $selectedCharacter->images->first()->path) }}" class="rounded-circle">
                                            </div>
                                        @else
                                            <div class="image-preview" style="width:306px; height:306px;" >
                                                <img id="preview-image" src="#" class="rounded-circle" style="display: none;">
                                            </div>
                                        @endif
                                    <div class="character-main-info-content">
                                        <div>
                                            <strong>Имя Фамилия</strong> 
                                            <hr>
                                            {{ $selectedCharacter->firstName . ' ' . $selectedCharacter->secondName }}
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
                                    <strong>Характер</strong> 
                                    <hr>
                                    {{ $selectedCharacter->personality }}
                                </div>

                                @if ($selectedCharacter->description())
                                    @if($selectedCharacter->description()->biography)
                                    <div>
                                        <strong>Биография</strong> 
                                        <hr>
                                        {{ $selectedCharacter->description()->biography }}
                                    </div>
                                    @endif

                                    @if($selectedCharacter->description()->description)
                                        <div>
                                            <strong>Внешность</strong> 
                                            <hr>
                                            {{ $selectedCharacter->description()->description }}
                                        </div>
                                    @endif

                                    @if($selectedCharacter->description()->headcounts)
                                        <div>
                                            <strong>Внешность</strong> 
                                            <hr>
                                            {{ $selectedCharacter->description()->headcounts }}
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
                                                    @foreach ($attribute->skills() as $skill)
                                                        <div class="skill" style="background-image: url({{ asset( $skill->skill->image_path) }}); background-size: contain; width: 122px; height: 173px;">
                                                            <div class="skill-content">
                                                                <div class="skill-value-content">
                                                                    <span data-attribute-id="{{ $attribute->id }}" id="skill-value-{{ $skill->id }}" class="skill-value">{{$attribute->level + $skill->points }}</span>
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

