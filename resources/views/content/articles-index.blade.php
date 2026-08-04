@extends('layouts.site')

@section('title', 'Статьи')

@section('content')
    <div class="content_box content">
        <div class="box">
            <h1 class="fint l_fint">Статьи</h1>
            <div class="row">
                @foreach ($entries as $entry)
                    @php
                        $articleImageUrl = !empty($entry['image']) ? \App\Support\MediaPath::url($entry['image']) : null;
                    @endphp
                    <div class="col-md-6">
                        <div class="box" style="min-height: 430px;">
                            @if ($articleImageUrl)
                                <div style="margin-bottom: 15px;">
                                    <a href="{{ route('articles.show', $entry['slug']) }}">
                                        <img src="{{ $articleImageUrl }}" alt="{{ $entry['title'] }}" style="width: 100%; height: 230px; object-fit: cover; border-radius: 8px;">
                                    </a>
                                </div>
                            @endif
                            <p><strong>{{ $entry['date'] }}</strong></p>
                            <h3><a href="{{ route('articles.show', $entry['slug']) }}">{{ $entry['title'] }}</a></h3>
                            <p>{{ $entry['excerpt'] }}</p>
                            <a class="btn btn-primary" href="{{ route('articles.show', $entry['slug']) }}">Читать дальше</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
