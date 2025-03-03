<form class="post-form" action={{ route('post.edit', $post->id) }} method="POST">
    @csrf
    @method('PUT')
    <div class="post-form__group">
        <input type="hidden" name="location_id" value={{ $selectedLocation->id }}>
        <input type="hidden" name="post_id" value={{ $postContent->id }}>
        <input type="hidden" name="user_id" value={{ $postContent->user_id }}>
        <label for="post-text" class="post-form__label">Введите текст поста:</label>
        <textarea id="post-text" name="post_text" class="post-form__input">{{ $postContent->content }}</textarea>
    </div>
    <button type="submit" class="post-form__button">Отправить</button>
</form>