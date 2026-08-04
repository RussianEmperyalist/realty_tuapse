@extends('layouts.admin')

@section('title', $newsPost->exists ? 'Редактирование новости' : 'Новая новость')

@section('content')
    <div class="admin-page-header">
        <div>
            <h1 style="margin: 0 0 8px;">{{ $newsPost->exists ? 'Редактирование новости' : 'Новая новость' }}</h1>
            <p style="margin: 0; color: #667085;">Заголовок, анонс, изображение и текст публикации.</p>
        </div>
        <div class="admin-actions">
            <a class="btn btn-default" href="{{ route('admin.news.index') }}">К списку</a>
            @if ($newsPost->exists)
                <a class="btn btn-primary" href="{{ route('news.show', $newsPost->slug) }}" target="_blank">Открыть на сайте</a>
            @endif
        </div>
    </div>

    <form method="post" action="{{ $formAction }}" enctype="multipart/form-data">
        @csrf
        @if ($method !== 'post')
            @method($method)
        @endif

        <div class="admin-form-card">
            <div class="admin-grid">
                <div class="admin-grid--full">
                    <label for="title">Заголовок</label>
                    <input class="form-control" id="title" name="title" type="text" value="{{ old('title', $newsPost->title) }}" required>
                </div>
                <div>
                    <label for="slug">Slug</label>
                    <input class="form-control" id="slug" name="slug" type="text" value="{{ old('slug', $newsPost->slug) }}">
                </div>
                <div>
                    <label for="legacy_path">Legacy path</label>
                    <input class="form-control" id="legacy_path" name="legacy_path" type="text" value="{{ old('legacy_path', $newsPost->legacy_path) }}">
                </div>
                <div>
                    <label for="published_at">Дата публикации</label>
                    <input class="form-control" id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', optional($newsPost->published_at)->format('Y-m-d\\TH:i')) }}">
                </div>
                <div class="admin-grid--full">
                    <label for="excerpt">Анонс</label>
                    <textarea class="form-control" id="excerpt" name="excerpt" rows="4">{{ old('excerpt', $newsPost->excerpt) }}</textarea>
                </div>
                <div class="admin-grid--full">
                    <label for="body">Текст новости</label>
                    <textarea class="form-control" id="body" name="body" rows="12">{{ old('body', $newsPost->body) }}</textarea>
                </div>
            </div>

            <div class="checkbox" style="margin-top: 20px;">
                <label>
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" name="is_published" value="1" @checked((bool) old('is_published', $newsPost->is_published))> Опубликовать новость
                </label>
            </div>
        </div>

        <div class="admin-form-card">
            <h2 style="margin-top: 0;">Изображение</h2>
            @if ($newsPost->image_path)
                <div style="margin-bottom: 20px; max-width: 280px;">
                    <img src="{{ asset($newsPost->image_path) }}" alt="{{ $newsPost->title }}" style="width: 100%; border-radius: 8px;">
                    <div class="checkbox" style="margin-top: 12px;">
                        <label><input type="checkbox" name="delete_image" value="1"> Удалить текущее изображение</label>
                    </div>
                </div>
            @endif
            <div class="form-group">
                <label for="image">Загрузить изображение</label>
                <input id="image" name="image" type="file">
            </div>
        </div>

        <div class="admin-actions">
            <button class="btn btn-primary" type="submit">Сохранить</button>
            <a class="btn btn-default" href="{{ route('admin.news.index') }}">Отменить</a>
        </div>
    </form>
@endsection
