@extends('frontend.layout.layout')
@section('title', 'Создание перосонажа')
<link rel="stylesheet" href="{{ asset('css/character.css') }}">



@section('content')
<div class="main-page">
    <div class="container d-flex justify-content-center align-items-stretch"> 
        <div class="row w-100 h-100"> 
            <div class="col-md-12 main-content d-flex flex-column justify-content-start">
                <div class="character-form-container">
                    @yield('characterContent')
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection

