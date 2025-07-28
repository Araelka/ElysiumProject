@extends('frontend.characters.indexCreate')
@section('title', $character->firstName . ' ' . $character->secondName)
@section('characterContent')
@isset($characterAttributes)
    <form id="character-form" action={{ route('characters.updateSkills', $characterId) }} method="POST"  enctype="multipart/form-data">
    @csrf
    @method('PUT')
@endisset

<div class="form-control" >
    <div class="attributes">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <label>Доступные очки: </label>
                <span id="available-points">{{ $character->getAvailablePoints() }}</span> 
            </div>
            <div class="mt-4" style="display: flex; justify-content: flex-end;">
                <button type="submit" id="submit-button" class="btn btn-primary">Повышение уровня</button>
            </div>  
        </div>

        @foreach ($character->attributes as $attribute)
        <div class="attribute mb-3">
                <div class="attribute-content-name d-flex justify-content-between align-items-center">
                    <div>
                        <label>{{ $attribute->attribute->name }}</label>
                        <span hidden id="attribute-value-{{ $attribute->id }}">{{ $attribute->level }}</span>
                        <label id="attribute-level-{{ $attribute->id }}"></label>
                    </div>
                </div>
        </div>
    
        
        <div class="slills">
            @foreach ($attribute->skills() as $skillId => $skill) 
                <div class="skill-update" style="background-image: url({{ asset( $skill->skill->image_path) }}); background-size: contain;">
                    <div class="skill-content">
                        <div class="skill-value-content">
                            <span data-attribute-id="{{ $attribute->id }}" id="skill-value-{{ $skill->id }}"  class="skill-value">{{ $skill->getLevelSkill() }}</span>
                            <input type="hidden" id="skill-{{ $skill->id }}" name="skills[{{ $skill->id }}]"  value={{ $skill->points }} disabled>
                        </div>

                        <div style="text-align: center; display: flex; flex-direction: column;">
                            <label id="skill-level-{{ $skill->id }}"></label>
                            <label class="skill-name" style="word-wrap: break-word; "><strong>{{ $skill->skill->name }}</strong></label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        @endforeach
    </div>
</div>
</form>

                    

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

    function increaseSkillLevel(skillId, attributeLevel, availablePointsElement) {
        const skillValueElement = document.getElementById(`skill-value-${skillId}`);
        const diamondsSkillContainer = document.getElementById(`skill-level-${skillId}`);
        const skillInput = document.getElementById(`skill-${skillId}`);
        const skillPoints = parseInt(skillInput.value);

        let currentSkillValue = parseInt(skillValueElement.textContent);

        const availablePoints = parseInt(availablePointsElement.textContent);
        if (availablePoints > 0 && currentSkillValue < 2 * attributeLevel) {
            currentSkillValue += 1;
            document.getElementById(`skill-${skillId}`).value = parseInt(skillPoints) + 1;
            skillValueElement.textContent = currentSkillValue;
            skillInput.removeAttribute('disabled');

            diamondsSkillContainer.innerHTML = '';

            for (let i = 0; i < currentSkillValue - attributeLevel; i++) {
                const filledDiamond = document.createElement('label');
                filledDiamond.textContent = '◆'; 
                diamondsSkillContainer.appendChild(filledDiamond);
            }

            for (let i = currentSkillValue - attributeLevel; i < attributeLevel; i++) {
                const emptyDiamond = document.createElement('label');
                emptyDiamond.textContent = '◇'; 
                diamondsSkillContainer.appendChild(emptyDiamond);
            }

            updateAvailablePoints(availablePointsElement, -1);
        } 
    }

    function decreaseSkillLevel(skillId, attributeLevel, availablePointsElement) {
        const skillValueElement = document.getElementById(`skill-value-${skillId}`);
        const diamondsSkillContainer = document.getElementById(`skill-level-${skillId}`);
        const skillInput = document.getElementById(`skill-${skillId}`);
        const skillPoints = parseInt(skillInput.value);

        let currentSkillValue = parseInt(skillValueElement.textContent);

        const availablePoints = parseInt(availablePointsElement.textContent);
        if (currentSkillValue > attributeLevel) {
            currentSkillValue -= 1;
            document.getElementById(`skill-${skillId}`).value = parseInt(skillPoints) - 1;
            skillValueElement.textContent = currentSkillValue;

            if (currentSkillValue === attributeLevel) {
                skillInput.setAttribute('disabled', true);
            }

            diamondsSkillContainer.innerHTML = '';

            for (let i = 0; i < currentSkillValue - attributeLevel; i++) {
                const filledDiamond = document.createElement('label');
                filledDiamond.textContent = '◆'; 
                diamondsSkillContainer.appendChild(filledDiamond);
            }

            for (let i = currentSkillValue - attributeLevel; i < attributeLevel; i++) {
                const emptyDiamond = document.createElement('label');
                emptyDiamond.textContent = '◇'; 
                diamondsSkillContainer.appendChild(emptyDiamond);
            }

            updateAvailablePoints(availablePointsElement, 1);
        } 
    }

    function updateAvailablePoints(availablePointsElement, pointsChange) {
        const availablePoints = parseInt(availablePointsElement.textContent);
        availablePointsElement.textContent = Math.max(availablePoints + pointsChange, 0);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const skillUpdateElements = document.querySelectorAll('.skill-update');
        const availablePointsElement = document.getElementById('available-points');
        let activeSkillId = null; 

        skillUpdateElements.forEach(skillElement => {
            skillElement.addEventListener('click', () => {
                const skillId = skillElement.querySelector('[id^="skill-value-"]').id.split('-')[2]; // Получаем ID навыка
                const attributeId = skillElement.querySelector('[data-attribute-id]').getAttribute('data-attribute-id');
                const attributeLevel = parseInt(document.getElementById(`attribute-value-${attributeId}`).textContent); // Получаем уровень атрибута
                
                if (activeSkillId === skillId) {
                    decreaseSkillLevel(skillId , attributeLevel, availablePointsElement);
                    activeSkillId = null;
                    skillElement.classList.remove('active');
                } 
                else if (activeSkillId != null) {
                    const previousSkillValueElement = document.getElementById(`skill-value-${activeSkillId}`);
                    const previousAttributeId = document.querySelector(`[id="skill-value-${activeSkillId}"]`).dataset.attributeId;
                    const previousAttributeLevel = parseInt(document.getElementById(`attribute-value-${previousAttributeId}`).textContent);

                    decreaseSkillLevel(activeSkillId, previousAttributeLevel, availablePointsElement);
                    document.querySelector(`[id="skill-value-${activeSkillId}"]`).parentElement.parentElement.parentElement.classList.remove('active');
                }

                increaseSkillLevel(skillId, attributeLevel, availablePointsElement);
                activeSkillId = skillId; 
                // skillUpdateElements.forEach(el => el.classList.remove('active'));
                skillElement.classList.add('active');
            });
        });

        @if ($character)
            @foreach ($character->attributes as $id => $attribute)
                updateDiamonds('{{ $attribute->id }}', {{ $attribute->level }});
                updateSkillDiamonds('{{ $attribute->id }}', {{ $attribute->level}} );
        @endforeach
        @endif
    });
</script>
@endsection