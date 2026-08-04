@extends('layouts.site')

@section('title', 'Новости')

@section('content')
    <div class="content_box content">
        <h1 class="fint l_fint">Новости</h1>
        <div class="row">
            @forelse ($newsPosts as $newsPost)
                @php
                    $newsImageUrl = \App\Support\MediaPath::url($newsPost->image_path);
                @endphp
                <div class="col-md-6 col-sm-12" style="margin-bottom: 30px;">
                    <div class="box">
                        @if ($newsImageUrl)
                            <a href="{{ route('news.show', $newsPost->slug) }}"><img src="{{ $newsImageUrl }}" alt="{{ $newsPost->title }}" style="width: 100%; height: 260px; object-fit: cover;"></a>
                        @endif
                        <div style="padding: 20px;">
                            <div class="h3"><a href="{{ route('news.show', $newsPost->slug) }}">{{ $newsPost->title }}</a></div>
                            <p>{{ $newsPost->excerpt }}</p>
                            <a href="{{ route('news.show', $newsPost->slug) }}" class="btn btn-default">Подробнее</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-md-12">
                    <p>Новости пока не опубликованы.</p>
                </div>
            @endforelse
        </div>
        {{ $newsPosts->links() }}
    </div>
@endsection
