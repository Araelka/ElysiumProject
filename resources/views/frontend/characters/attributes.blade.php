@extends('frontend.characters.indexCreate')

@section('characterContent')
<form action={{ route("characters.createSkills") }} method="POST"  enctype="multipart/form-data" style="margin-right: 5px">
@csrf
<div class="form-control" style="margin-top: 15px">
    <label id={{ $characterId }}></label>
    <div class="attributes">
        <div >
                <div>
                    <label>Доступные очки: </label>
                    <span id="available-points">6</span> 
                </div>
        </div>

        @foreach ($attributes as $attribute)
        <div class="attribute mb-3">
                <div class="attribute-content-name d-flex justify-content-between align-items-center">
                    <div>
                        <label>{{ $attribute->name }}</label>
                        <label id="attribute-level-{{ $attribute->id }}"></label>
                    </div>

                    <div>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="decreaseAttribute('{{ $attribute->id }}')" style="font-size: 20px;"><</button>
                        <span id="attribute-value-{{ $attribute->id }}">{{ $attribute->min_value }}</span>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="increaseAttribute('{{ $attribute->id }}')" style="font-size: 20px;">></button>
                    </div>
                </div>
        </div>

        <div class="slills">
            @foreach ($attribute->skills as $skill) 
                <div class="skill" style="background-image: url({{ asset( $skill->image_path) }}); background-size: contain;">
                    <div class="skill-content">
                                <div class="skill-value-content">
                                    <span data-attribute-id="{{ $attribute->id }}" id="skill-value-{{ $skill->id }}"  class="skill-value">{{ $attribute->min_value}}</span>
                                </div>

                                <div style="text-align: center;">
                                    <label style="word-wrap: break-word; "><strong>{{ $skill->name }}</strong></label>
                                </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        @endforeach
    </div>
</div>

<div class="mt-4" style="display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary">Далее</button>
        </div>
</form>
                    

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
    }

    function increaseAttribute(attributeId) {
    const attributeValue = document.getElementById(`attribute-value-${attributeId}`);
    const skillValues = document.querySelectorAll(`[data-attribute-id="${attributeId}"]`);
    let currentValue = parseInt(attributeValue.textContent);

    const availablePoints = parseInt(document.getElementById('available-points').textContent);
    if (currentValue < 6 && availablePoints > 0) {
        attributeValue.textContent = currentValue + 1;
        
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
        const skillValues = document.querySelectorAll(`[data-attribute-id="${attributeId}"]`);
        let currentValue = parseInt(attributeValue.textContent);

        if (currentValue > 1) {
            attributeValue.textContent = currentValue - 1;
            
            skillValues.forEach(skillValue => {
                const currentSkillValue = parseInt(skillValue.textContent);
                skillValue.textContent = Math.min(currentSkillValue - 1, 2 * (currentValue - 1));
            });

            updateDiamonds(attributeId, currentValue - 1);
            
            updateAvailablePoints();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        @foreach ($attributes as $attribute)
            updateDiamonds('{{ $attribute->id }}', {{ $attribute->min_value }});
        @endforeach
    });
</script>
@endsection