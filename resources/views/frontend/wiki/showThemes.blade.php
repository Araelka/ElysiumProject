@extends('frontend.wiki.index')
@section('title', 'Вики')
@section('table')

<div class="theme-container">
    <div class="theme-grid">
        @isset($themes)
            @foreach ($themes as $theme)
                @if (Auth::check() && !Auth::user()->isEditor() && !$theme->visibility)
                    @continue
                @else
                <div class="theme-item">
                    <a href={{ route('wiki.article.index', $theme->article->id) }} class="theme-card d-flex align-items-center justify-content-between" style="text-decoration: none; color: inherit;">
                        
                        @if (Auth::check() && Auth::user()->isEditor())
                             <!-- Иконка видимости -->
                            <form action={{ route('wiki.toggleVisibility', $theme->id) }} method="POST" class="visibility-form">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="visibility-button" title="{{ $theme->visibility ? 'Скрыть тему' : 'Показать тему' }}">
                                    @if ($theme->visibility)
                                        <svg class="visibility-icon visible" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" fill="#f4d03f" clip-rule="evenodd" d="M8 13.078c4.418 0 8-5 8-5s-3.582-5-8-5-8 5-8 5 3.582 5 8 5zm0-2a3 3 0 100-6 3 3 0 000 6zm0-1.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" fill="#000"></path>
                                        </svg>
                                    @else
                                        <svg class="visibility-icon hidden" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" fill="#ec7063" d="M15.406 1.125l-3.229 3.229C14.47 5.834 16 7.969 16 7.969s-3.582 5-8 5c-1.244 0-2.422-.397-3.472-.966l-3.372 3.372-.707-.707 3.2-3.2C1.451 9.997 0 7.969 0 7.969s3.582-5 8-5c1.17 0 2.28.351 3.282.867L14.7.418l.707.707zM8 4.969c.61 0 1.179.182 1.653.496L8.546 6.57a1.5 1.5 0 00-1.943 1.943L5.495 9.622A3 3 0 018 4.968zm-.742 4.304l-1.08 1.08a3 3 0 004.205-4.205l-1.079 1.08a1.5 1.5 0 01-2.046 2.046z" fill="#000"></path>
                                        </svg>
                                    @endif
                                </button>
                            </form>
                        @endif
             
                        @if (Auth::check() && Auth::user()->isEditor())
                            <form id="delete-form-{{ $theme->id }}" action="{{ route('wiki.destroyTheme', $theme->id) }}" method="POST" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="delete-theme-button" data-id="{{ $theme->id }}" onclick="confirmDelete(event, {{ $theme->id }})">×</button>
                            </form>
                        @endif

                        <div class="theme-card-content">
                            <h5 class="theme-card-title">{{ $theme->name }}</h5>
                        </div>
                        <div class="theme-card-image">

                            @if ($theme->images->isNotEmpty())
                                @if (Auth::check() && Auth::user()->isEditor())
                                    <form action={{ route('wiki.uploadImage', $theme->id) }} method="POST" enctype="multipart/form-data" class="image-upload-form">
                                        @csrf
                                        <label for="upload-image-{{ $theme->id }}" class="add-image-button">
                                            <img src="{{ asset('storage/' . $theme->images->first()->path) }}" alt="Изображение" class="img-fluid">
                                        </label>
                                        <input type="file" id="upload-image-{{ $theme->id }}" name="image" class="hidden-input" accept="image/*">
                                        <button type="submit" class="hidden-submit-button">Загрузить</button>
                                    </form>
                                @else
                                    <img src="{{ asset('storage/' . $theme->images->first()->path) }}" alt="Изображение" class="img-fluid">
                                @endif
                            @else
                                @if (Auth::check() && Auth::user()->isEditor())
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
                @endif
            @endforeach
        @endisset
    </div>
</div>

<div class="footer-pagination">
    @if (isset($themes))
        {{ $themes->links('vendor.pagination.default') }}
    @endif
</div>

<!-- Модальное окно -->
<div id="delete-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <p>Вы уверены, что хотите удалить эту тему?</p>
        <button id="confirm-delete" class="btn btn-danger">Удалить</button>
        <button id="cancel-delete" class="btn btn-secondary">Отмена</button>
    </div>
</div>

<script>
    let currentThemeId = null;

    // Функция открытия модального окна
    function confirmDelete(event, themeId) {
        event.preventDefault();
        event.stopPropagation(); 
        currentThemeId = themeId;
        const modal = document.getElementById('delete-modal');
        modal.style.display = 'block';
    }

    // Функция закрытия модального окна
    document.getElementById('cancel-delete').addEventListener('click', function () {
        const modal = document.getElementById('delete-modal');
        modal.style.display = 'none';
        currentThemeId = null;
    });

    // Функция подтверждения удаления
    document.getElementById('confirm-delete').addEventListener('click', function () {
        if (currentThemeId) {
            const form = document.getElementById(`delete-form-${currentThemeId}`);
            form.submit(); 
        }
        const modal = document.getElementById('delete-modal');
        modal.style.display = 'none';
        currentThemeId = null;
    });

    document.addEventListener('DOMContentLoaded', function () {
    // Находим все формы с классом image-upload-form
    const forms = document.querySelectorAll('.image-upload-form');

    forms.forEach(form => {
        const fileInput = form.querySelector('input[type="file"]');
        fileInput.addEventListener('change', function () {
            form.submit(); 
        });
    });
});
</script>

@endsection
