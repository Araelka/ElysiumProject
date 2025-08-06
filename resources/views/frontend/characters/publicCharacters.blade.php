@extends('frontend.characters.publicIndex')
@section('title', 'Персонажи')
@section('table')

<div class="character-container">
    <div class="character-grid">
        @isset($characters)
            @foreach ($characters as $character)
                <div class="character-item">
                    <a href={{ route('character.publicCharacter', $character->id) }} class="character-card d-flex align-items-center justify-content-between" style="text-decoration: none; color: inherit;">

                        <div class="character-card-content">
                            <h5 class="character-card-title">{{ $character->firstName . ' ' . $character->secondName }}</h5>
                        </div>
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
                        
                        <span class="character-user" style="color: #f4d03f">
                            <div>
                                {{ $character->user->login }}
                            </div>
                        </span>

                        <div class="character-card-image"> 
                            @if ($character->images->first())
                                <img src="{{ asset('storage/' . $character->images->first()->path) }}" alt="Изображение" class="img-fluid">
                            @else
                                @if ($character->gender == 'Мужской')
                                    <img src="{{ asset('images/characters/characterMale.jpg') }}" alt="Изображение" class="img-fluid">
                                @else
                                    <img src="{{ asset('images/characters/characterFemale.jpg') }}" alt="Изображение" class="img-fluid">
                                @endif
                            @endif
                        </div>
                    </a>
                </div>
            @endforeach
        @endisset
    </div>
</div>

<div class="footer-pagination">
    @if (isset($character))
        {{ $characters->links('vendor.pagination.default') }}
    @endif
</div>

@endsection