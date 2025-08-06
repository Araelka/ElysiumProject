@extends('frontend.characters.publicIndex')
<link rel="stylesheet" href="{{ asset('css/character.css') }}">
@section('title', $character->firstName . ' ' . $character->secondName)
@section('table')


<div class="row w-100 h-100" > 
    <div class="col-md-12 main-content d-flex flex-column justify-content-start" style="background-color: transparent; box-shadow: none; padding: 10px 0px;">
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
                                            {{ $character->user->login }}
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
                        <strong>Характер</strong> 
                        <hr>
                        <div>{!! nl2br(e($character->personality)) !!}</div>
                    </div>

                    @if ($character->description())
                        @if($character->description()->biography)
                        <div>
                            <strong>Биография</strong> 
                            <hr>
                            <div>{!! nl2br(e($character->description()->biography)) !!}</div>
                        </div>
                        @endif

                        @if($character->description()->description)
                            <div>
                                <strong>Внешность</strong>
                                <hr>
                                <div>{!! nl2br(e($character->description()->description)) !!}</div>
                            </div>
                        @endif

                        @if($character->description()->headcounts)
                            <div>
                                <strong>Факты</strong>
                                <hr>
                                <div>{!! nl2br(e($character->description()->headcounts)) !!}</div>
                            </div>
                        @endif
                    @endif

                </div>
    </div>
    </div>
</div>

@endsection