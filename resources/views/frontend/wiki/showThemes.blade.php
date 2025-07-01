@extends('frontend.wiki.index')
@section('title', 'Вики')
@section('table')

<div class="theme-container">
    <div class="theme-grid">
        @isset($themes)
            @foreach ($themes as $theme)
                    
                <div class="theme-item">
                    <a href={{ route('wiki.article.index', $theme->article->id) }} class="theme-card d-flex align-items-center justify-content-between" style="text-decoration: none; color: inherit;">
                        
                        @if (Auth::user()->isEditor())
                             <!-- Иконка видимости -->
                            <form action={{ route('wiki.toggleVisibility', $theme->id) }} method="POST" class="visibility-form">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="visibility-button" title="{{ $theme->visibility ? 'Скрыть тему' : 'Показать тему' }}">
                                    @if ($theme->visibility)
                                        <!-- Иконка "Глазик" -->
                                        <svg class="visibility-icon visible" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                            <path fill="#f4d03f" d="M12 9c-1.654 0-3 1.346-3 3s1.346 3 3 3 3-1.346 3-3-1.346-3-3-3m0-2c2.757 0 5 2.243 5 5s-2.243 5-5 5-5-2.243-5-5 2.243-5 5-5m0-2C6.477 4 2 8.477 2 13s4.477 9 10 9 10-4.477 10-9S17.523 4 12 4z"/>
                                        </svg>
                                    @else
                                        <!-- Иконка "Перечёркнутый глазик" -->
                                        <svg class="visibility-icon hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                            <path fill="#ec7063" d="M12 9c-1.654 0-3 1.346-3 3s1.346 3 3 3 3-1.346 3-3-1.346-3-3-3m0-2c2.757 0 5 2.243 5 5s-2.243 5-5 5-5-2.243-5-5 2.243-5 5-5m0-2C6.477 4 2 8.477 2 13s4.477 9 10 9 10-4.477 10-9S17.523 4 12 4zm-4 10h10v-2H8v2zm5-7l-5 5 1.414 1.414L11 13.828l-1.414-1.414L8 15l5-5-5-5-1.414 1.414L11 6.172l1.414 1.414L16 5l-5 5z"/>
                                        </svg>
                                    @endif
                                </button>
                            </form>
                        @endif
                        
                        @if (Auth::user()->isEditor())
                            <form action="{{ route('wiki.destroyTheme', $theme->id) }}" method="POST" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button class="delete-theme-button" data-id="{{ $theme->id }}">×</button>
                            </form>
                        @endif

                        <div class="theme-card-content">
                            <h5 class="theme-card-title">{{ $theme->name }}</h5>
                        </div>
                        <div class="theme-card-image">

                            @if ($theme->images->isNotEmpty())
                                <img src="{{ asset('storage/' . $theme->images->first()->path) }}" alt="Изображение" class="img-fluid">
                            @else
                                @if (Auth::user()->isEditor())
                                    <!-- Кнопка добавления изображения -->
                                    <form action={{ route('wiki.uploadImage', $theme->id) }} method="POST" enctype="multipart/form-data" class="image-upload-form">
                                        @csrf
                                        <label for="upload-image-{{ $theme->id }}" class="add-image-button">+</label>
                                        <input type="file" id="upload-image-{{ $theme->id }}" name="image" class="hidden-input" accept="image/*">
                                        <button type="submit" class="hidden-submit-button">Загрузить</button>
                                    </form>
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
    @if (isset($themes))
        {{ $themes->links('vendor.pagination.default') }}
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Находим все формы с классом image-upload-form
    const forms = document.querySelectorAll('.image-upload-form');

    forms.forEach(form => {
        const fileInput = form.querySelector('input[type="file"]');
        fileInput.addEventListener('change', function () {
            form.submit(); // Автоматическая отправка формы при выборе файла
        });
    });
});
</script>

@endsection
