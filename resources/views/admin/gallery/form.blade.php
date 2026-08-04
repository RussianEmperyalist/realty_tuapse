@extends('layouts.admin')

@section('title', $album->exists ? 'Редактирование альбома' : 'Новый альбом')

@section('content')
    <div class="admin-page-header">
        <div>
            <h1 style="margin: 0 0 8px;">{{ $album->exists ? 'Редактирование альбома' : 'Новый альбом' }}</h1>
            <p style="margin: 0; color: #667085;">Название, порядок показа и фотографии альбома.</p>
        </div>
        <div class="admin-actions">
            <a class="btn btn-default" href="{{ route('admin.gallery.index') }}">К списку</a>
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
                    <label for="title">Название альбома</label>
                    <input class="form-control" id="title" name="title" type="text" value="{{ old('title', $album->title) }}" required>
                </div>
                <div>
                    <label for="slug">Slug</label>
                    <input class="form-control" id="slug" name="slug" type="text" value="{{ old('slug', $album->slug) }}">
                </div>
                <div>
                    <label for="sort_order">Порядок</label>
                    <input class="form-control" id="sort_order" name="sort_order" type="number" value="{{ old('sort_order', $album->sort_order) }}">
                </div>
                <div class="admin-grid--full">
                    <label for="description">Описание</label>
                    <textarea class="form-control" id="description" name="description" rows="5">{{ old('description', $album->description) }}</textarea>
                </div>
            </div>

            <div class="checkbox" style="margin-top: 20px;">
                <label>
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" name="is_published" value="1" @checked((bool) old('is_published', $album->is_published))> Показывать альбом на сайте
                </label>
            </div>
        </div>

        <div class="admin-form-card">
            <h2 style="margin-top: 0;">Обложка</h2>
            @if ($album->cover_image_path)
                <div style="margin-bottom: 20px; max-width: 280px;">
                    <img src="{{ asset($album->cover_image_path) }}" alt="{{ $album->title }}" style="width: 100%; border-radius: 8px;">
                    <div class="checkbox" style="margin-top: 12px;">
                        <label><input type="checkbox" name="delete_cover_image" value="1"> Удалить обложку</label>
                    </div>
                </div>
            @endif
            <div class="form-group">
                <label for="cover_image">Загрузить обложку</label>
                <input id="cover_image" name="cover_image" type="file">
            </div>
        </div>

        <div class="admin-form-card">
            <h2 style="margin-top: 0;">Фотографии альбома</h2>
            @if ($album->exists && $album->items->isNotEmpty())
                <div class="admin-media-grid" style="margin-bottom: 20px;">
                    @foreach ($album->items as $item)
                        <div class="admin-media-card">
                            <img src="{{ asset($item->thumb_path ?: $item->image_path) }}" alt="{{ $item->title ?: $album->title }}">
                            <div class="form-group">
                                <label for="item-title-{{ $item->id }}">Название</label>
                                <input class="form-control" id="item-title-{{ $item->id }}" name="item_titles[{{ $item->id }}]" type="text" value="{{ old('item_titles.' . $item->id, $item->title) }}">
                            </div>
                            <div class="form-group">
                                <label for="item-sort-{{ $item->id }}">Порядок</label>
                                <input class="form-control" id="item-sort-{{ $item->id }}" name="item_sort_orders[{{ $item->id }}]" type="number" value="{{ old('item_sort_orders.' . $item->id, $item->sort_order) }}">
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" name="item_published[]" value="{{ $item->id }}" @checked($item->is_published)> Опубликовано</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" name="delete_items[]" value="{{ $item->id }}"> Удалить</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            <div class="form-group">
                <label for="items">Добавить фото в альбом</label>
                <input id="items" name="items[]" type="file" multiple>
            </div>
        </div>

        <div class="admin-actions">
            <button class="btn btn-primary" type="submit">Сохранить</button>
            <a class="btn btn-default" href="{{ route('admin.gallery.index') }}">Отменить</a>
        </div>
    </form>
@endsection
