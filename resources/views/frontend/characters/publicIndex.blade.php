@extends('frontend.layout.layout')
<link rel="stylesheet" href="{{ asset('css/character.css') }}">
@section('title', 'Персонажи')

@section('content')
<div class="main-page">
    <div class="container d-flex justify-content-center align-items-stretch"> 
        <div class="row w-100 h-100"> 
            <div class="col-md-12 main-content d-flex flex-column justify-content-start">
                 @if (Request::is('characters/public'))
                <div class="character-selection-container d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2>
                            Персонажи
                        </h2>
                    </div>
                        <div class="search-container">
                            <form action="{{ request()->fullUrlWithQuery(['search' => '']) }}" method="GET" id="search-form">
                                <div class="search-input-wrapper">
                                    <input 
                                        type="text" 
                                        name="search" 
                                        id="search-input" 
                                        value="{{ request('search') }}" 
                                        placeholder="Поиск..." 
                                        class="search-input"
                                    >
                                    @if(request('search'))
                                        <button type="button" id="clear-search" class="clear-search-button">×</button>
                                    @endif
                                </div>
                                <button type="submit" class="search-button">Найти</button>
                            </form>
                        </div>
                </div>
                @endif
                <div class="character-container">
                    <div class="character-grid">
                        @isset($characters)
                            @foreach ($characters as $character)
                                <div class="character-item">
                                    <a href={{ route('wiki.article.index', $character->id) }} class="character-card d-flex align-items-center justify-content-between" style="text-decoration: none; color: inherit;">
                                        


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
            </div>
        </div>
    </div>
</div>



@endsection