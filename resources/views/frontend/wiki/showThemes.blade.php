@extends('frontend.wiki.index')
@section('title', 'Вики')
@section('table')

<div class="theme-container">
    <div class="theme-grid">
        @isset($themes)
            @foreach ($themes as $theme)
                <div class="theme-item">
                    <a href="" class="theme-card d-flex align-items-center justify-content-between" style="text-decoration: none; color: inherit;">
                        @if (Auth::user()->isEditor())
                            <button class="delete-theme-button" data-id="{{ $theme->id }}">×</button>
                        @endif

                        <div class="theme-card-content">
                            <h5 class="theme-card-title">{{ $theme->name }}</h5>
                        </div>
                        <div class="theme-card-image">
                            @if ($theme->article->images->isNotEmpty())
                                <img src="{{ asset('storage/' . $theme->article->images->first()->path) }}" alt="Изображение" class="img-fluid">
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
@endsection
