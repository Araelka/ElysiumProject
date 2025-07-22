@extends('frontend.layout.layout')
@section('title', 'Персонажи')
<link rel="stylesheet" href="{{ asset('css/character.css') }}">



@section('content')
<div class="double-page">
    <div class="container d-flex justify-content-center align-items-stretch">
        <div class="row w-100 h-100">
            <!-- Боковая панель (20%) -->
            <div class="col-md-2 sidebar d-flex flex-column justify-content-start">
                <h3>Персонажи</h3>
                <ul class="topics-list">
                    <!-- Здесь можно добавить список персонажей -->
                </ul>
            </div>

            <!-- Основной контент (80%) -->
            <div class="col-md-10 content d-flex flex-column justify-content-start">
                <div class="character-form-container">
                        
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

