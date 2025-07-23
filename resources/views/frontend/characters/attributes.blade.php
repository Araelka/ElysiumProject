@extends('frontend.characters.indexCreate')

@section('characterContent')
@isset($characterAttributes)
    <form id="character-form" action={{ route('characters.updateAttributes', $characterId) }} method="POST"  enctype="multipart/form-data" style="margin-right: 5px">
    @csrf
    @method('PUT')
@else
    <form id="character-form" action={{ route("characters.createSkills", $characterId) }} method="POST"  enctype="multipart/form-data" style="margin-right: 5px">
    @csrf
@endisset

<div class="form-control" >
    <div class="attributes">
        <div >
                <div>
                    <label>Доступные очки: </label>
                    <span id="available-points">6</span> 
                </div>
        </div>

        @foreach ($attributes as $id => $attribute)
        <div class="attribute mb-3">
                <div class="attribute-content-name d-flex justify-content-between align-items-center">
                    <div>
                        <label>{{ $attribute->name }}</label>
                        <label id="attribute-level-{{ $attribute->id }}"></label>
                    </div>

                    <div>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="decreaseAttribute('{{ $attribute->id }}')" style="font-size: 20px;"><</button>
                        <span id="attribute-value-{{ $attribute->id }}">{{  $characterAttributes?->get($id)?->level ?? $attribute->min_value }}</span>
                        <input type="hidden"  name="attributes[{{ $attribute->id }}]" id="hidden-attribute-value-{{ $attribute->id }}" value={{ ($characterAttributes?->get($id)?->level ?? $attribute->min_value)-1 }}>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="increaseAttribute('{{ $attribute->id }}')" style="font-size: 20px;">></button>
                    </div>
                </div>
        </div>
    

        <div class="slills">
            @foreach ($attribute->skills as $skill) 
                <div class="skill" style="background-image: url({{ asset( $skill->image_path) }}); background-size: contain;">
                    <div class="skill-content">
                                <div class="skill-value-content">
                                    <span data-attribute-id="{{ $attribute->id }}" id="skill-value-{{ $skill->id }}"  class="skill-value">{{$characterAttributes?->get($id)?->level ?? $attribute->min_value }}</span>
                                    <input type="hidden" name="skills[{{ $skill->id }}]"  value="0">
                                </div>

                                <div style="text-align: center; display: flex; flex-direction: column;">
                                    <label id="skill-level-{{ $skill->id }}"></label>
                                    <label style="word-wrap: break-word; "><strong>{{ $skill->name }}</strong></label>
                                </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        @endforeach
    </div>
</div>
        <div class="mt-4" style="display: flex; justify-content: space-between;">
            <a href={{ route('characters.showMainInfo', $characterId) }} style="font-family: sans-serif; text-decoration: none;" class="btn btn-primary">Назад</a>
            <button type="submit" id="submit-button" class="btn btn-primary">Далее</button>
        </div>  
</form>



<!-- Модальное окно -->
<div id="confirmation-modal" class="modal">
    <div class="modal-content">
        <p>Вы уверены, что хотите продолжить?</p>
        <p>У вас остались неиспользованные очки: <span id="remaining-points"></span>.</p>
        <div >
            <button id="confirm-submit" class="btn btn-accept">Да, сохранить</button>
            <button id="cancel-submit" class="btn btn-cancel">Отмена</button>
        </div>
    </div>
</div>
                    

<script>
    function updateAvailablePoints() {
        const attributeValues = document.querySelectorAll('[id^="attribute-value-"]');
        let totalPointsUsed = 0;

        attributeValues.forEach(attributeValue => {
            totalPointsUsed += parseInt(attributeValue.textContent);
        });

        const availablePoints = 6 - (totalPointsUsed - attributeValues.length); 
        document.getElementById('available-points').textContent = Math.max(availablePoints, 0);
    }

    function updateDiamonds(attributeId, value) {
        const diamondsContainer = document.getElementById(`attribute-level-${attributeId}`);
        diamondsContainer.innerHTML = ''; // Очищаем текущие ромбики

        // Добавляем нужное количество ромбиков
        for (let i = 0; i < value; i++) {
            const diamond = document.createElement('label');
            diamond.textContent = '♦';
            diamondsContainer.appendChild(diamond);
        }

        const skillValues = document.querySelectorAll(`[data-attribute-id="${attributeId}"]`);
        skillValues.forEach(skillValue => {
            const skillId = skillValue.parentElement.parentElement.querySelector('label').id;
            const diamondsSkillContainer = document.getElementById(skillId);
            diamondsSkillContainer.innerHTML = '';
            
            for (let i = 0; i < value; i++) {
                const diamondSkill = document.createElement('label');
                diamondSkill.textContent = '◊';
                diamondsSkillContainer.appendChild(diamondSkill); 
            }
        })
    }

    function increaseAttribute(attributeId) {
    const attributeValue = document.getElementById(`attribute-value-${attributeId}`);
    const hiddenAttributeValue = document.getElementById(`hidden-attribute-value-${attributeId}`);
    const skillValues = document.querySelectorAll(`[data-attribute-id="${attributeId}"]`);
    let currentValue = parseInt(attributeValue.textContent);

    const availablePoints = parseInt(document.getElementById('available-points').textContent);
    if (currentValue < 6 && availablePoints > 0) {
        attributeValue.textContent = currentValue + 1;
        hiddenAttributeValue.value= currentValue;
        
        skillValues.forEach(skillValue => {
            const currentSkillValue = parseInt(skillValue.textContent);
            skillValue.textContent = Math.min(currentSkillValue + 1, 2 * (currentValue + 1));
        });
        
        updateDiamonds(attributeId, currentValue + 1);

        updateAvailablePoints();
    }
}

    function decreaseAttribute(attributeId) {
        const attributeValue = document.getElementById(`attribute-value-${attributeId}`);
        const hiddenAttributeValue = document.getElementById(`hidden-attribute-value-${attributeId}`);
        const skillValues = document.querySelectorAll(`[data-attribute-id="${attributeId}"]`);
        let currentValue = parseInt(attributeValue.textContent);

        if (currentValue > 1) {
            attributeValue.textContent = currentValue - 1;
            hiddenAttributeValue.value = currentValue - 2;

            
            skillValues.forEach(skillValue => {
                const currentSkillValue = parseInt(skillValue.textContent);
                skillValue.textContent = Math.min(currentSkillValue - 1, 2 * (currentValue - 1));
            });

            updateDiamonds(attributeId, currentValue - 1);
            
            updateAvailablePoints();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        @foreach ($attributes as $id => $attribute)
            updateDiamonds('{{ $attribute->id }}', {{ $characterAttributes?->get($id)?->level ?? $attribute->min_value }});
        @endforeach

        updateAvailablePoints();

        // Обработка отправки формы
        const form = document.getElementById('character-form');
        const modal = document.getElementById('confirmation-modal');
        const remainingPointsSpan = document.getElementById('remaining-points');
        const confirmSubmitButton = document.getElementById('confirm-submit');
        const cancelSubmitButton = document.getElementById('cancel-submit');

        form.addEventListener('submit', (event) => {
            const availablePoints = parseInt(document.getElementById('available-points').textContent);

            if (availablePoints > 0) {
                event.preventDefault(); // Отменяем отправку формы

                // Показываем модальное окно
                remainingPointsSpan.textContent = availablePoints;
                modal.style.display = 'block';

                // Подтверждение отправки
                confirmSubmitButton.onclick = () => {
                    modal.style.display = 'none';
                    form.submit(); // Принудительно отправляем форму
                };

                // Отмена отправки
                cancelSubmitButton.onclick = () => {
                    modal.style.display = 'none';
                };
            }
        });
    });
</script>
@endsection