@extends('layouts.admin')

@section('title', 'Галерея')

@section('content')
    <div class="admin-page-header">
        <div>
            <h1 style="margin: 0 0 8px;">Фотогалерея</h1>
            <p style="margin: 0; color: #667085;">Альбомы и изображения публичного раздела галереи.</p>
        </div>
        <div class="admin-actions">
            <a class="btn btn-primary" href="{{ route('admin.gallery.create') }}">Добавить альбом</a>
        </div>
    </div>

    <div class="admin-table">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Порядок</th>
                    <th>Альбом</th>
                    <th>Фото</th>
                    <th>Статус</th>
                    <th style="width: 220px;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($albums as $album)
                    <tr>
                        <td>{{ $album->sort_order }}</td>
                        <td>
                            <strong>{{ $album->title }}</strong><br>
                            <span style="color:#667085;">{{ $album->description }}</span>
                        </td>
                        <td>{{ $album->items_count }}</td>
                        <td>
                            @if ($album->is_published)
                                <span class="label label-success">Опубликован</span>
                            @else
                                <span class="label label-default">Скрыт</span>
                            @endif
                        </td>
                        <td>
                            <div class="admin-actions">
                                <a class="btn btn-xs btn-default" href="{{ route('gallery.index') }}" target="_blank">Открыть</a>
                                <a class="btn btn-xs btn-primary" href="{{ route('admin.gallery.edit', $album) }}">Редактировать</a>
                                <form method="post" action="{{ route('admin.gallery.destroy', $album) }}" onsubmit="return confirm('Удалить альбом?');">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-xs btn-danger" type="submit">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Альбомы пока не добавлены.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $albums->links() }}
    </div>
@endsection
