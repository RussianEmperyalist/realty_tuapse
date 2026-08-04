@extends('layouts.site')

@section('title', $entry['title'])

@section('content')
    <div class="content_box content">
        <div class="box">
            <p><a href="{{ $backRoute }}">&larr; {{ $sectionTitle }}</a></p>
            <h1 class="fint l_fint">{{ $entry['title'] }}</h1>
            <p><strong>{{ $entry['date'] }}</strong></p>
            @php
                $articleImageUrl = !empty($entry['image']) ? \App\Support\MediaPath::url($entry['image']) : null;
            @endphp
            @if ($articleImageUrl)
                <img src="{{ $articleImageUrl }}" alt="{{ $entry['title'] }}" style="width: 100%; max-height: 420px; object-fit: cover; border-radius: 8px; margin-bottom: 20px;">
            @endif
            {!! $entry['body'] !!}
        </div>
    </div>
@endsection
