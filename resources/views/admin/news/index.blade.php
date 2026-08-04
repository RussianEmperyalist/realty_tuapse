@extends('layouts.admin')

@section('title', 'Новости')

@section('content')
    <div class="admin-page-header">
        <div>
            <h1 style="margin: 0 0 8px;">Новости</h1>
            <p style="margin: 0; color: #667085;">Публикации с изображением для публичного раздела новостей.</p>
        </div>
        <div class="admin-actions">
            <a class="btn btn-primary" href="{{ route('admin.news.create') }}">Добавить новость</a>
        </div>
    </div>

    <div class="admin-table">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Новость</th>
                    <th>Статус</th>
                    <th style="width: 220px;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($newsPosts as $newsPost)
                    <tr>
                        <td>{{ optional($newsPost->published_at)->format('d.m.Y') ?: 'Не указана' }}</td>
                        <td>
                            <strong>{{ $newsPost->title }}</strong><br>
                            <span style="color:#667085;">{{ $newsPost->excerpt }}</span>
                        </td>
                        <td>
                            @if ($newsPost->is_published)
                                <span class="label label-success">Опубликована</span>
                            @else
                                <span class="label label-default">Скрыта</span>
                            @endif
                        </td>
                        <td>
                            <div class="admin-actions">
                                <a class="btn btn-xs btn-default" href="{{ route('news.show', $newsPost->slug) }}" target="_blank">Открыть</a>
                                <a class="btn btn-xs btn-primary" href="{{ route('admin.news.edit', $newsPost) }}">Редактировать</a>
                                <form method="post" action="{{ route('admin.news.destroy', $newsPost) }}" onsubmit="return confirm('Удалить новость?');">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-xs btn-danger" type="submit">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Новости пока не добавлены.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $newsPosts->links() }}
    </div>
@endsection
