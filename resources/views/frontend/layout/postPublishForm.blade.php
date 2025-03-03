
<form class="post-form" action={{ route('post.publish') }} method="POST">
    @csrf
    <div class="post-form__group">
        <input type="hidden" name="location_id" value={{ $selectedLocation->id }}>
        <label for="post-text" class="post-form__label">Введите текст поста:</label>
        <textarea id="post-text" name="post_text" class="post-form__input"></textarea>
    </div>
    <button type="submit" class="post-form__button">Отправить</button>
</form>