@extends('layouts.site')

@section('title', $newsPost->title)
@section('meta_description', $newsPost->excerpt ?: 'Новости')

@section('content')
    <div class="content_box content">
        <div class="box">
            <h1 class="fint l_fint">{{ $newsPost->title }}</h1>
            @if ($newsPost->published_at)
                <p><strong>{{ $newsPost->published_at->format('d.m.Y') }}</strong></p>
            @endif
            @php
                $newsImageUrl = \App\Support\MediaPath::url($newsPost->image_path);
            @endphp
            @if ($newsImageUrl)
                <img src="{{ $newsImageUrl }}" alt="{{ $newsPost->title }}" style="width: 100%; max-height: 420px; object-fit: cover; margin-bottom: 20px;">
            @endif
            {!! $newsPost->body !!}
        </div>
    </div>
@endsection
