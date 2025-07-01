@extends('frontend.wiki.showArticle')

@section('editTitle')
<form action={{ route('wiki.editArticleTitle', $article->theme->id) }} method="POST" enctype="multipart/form-data" style="display: flex; align-items: center; margin: 0;">
@csrf
@method('PUT')
<div class="form-group-theme-labbe" style="margin-right: 10px">
        <input type="text" id="name" name="name" value="{{ $article->theme->name }}" required>
        @error('name')
            <span class="form__error">{{ $message }}</span>
        @enderror
    </div>
    <!-- Кнопка сохранить -->
    <div class="form-group-theme-batton">
        <button type="submit" class="save-button">Сохранить</button>
    </div>
</form>
@endsection
