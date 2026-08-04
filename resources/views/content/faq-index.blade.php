@extends('layouts.site')

@section('title', 'Вопросы-ответы')

@section('content')
    <div class="content_box content">
        <div class="box">
            <h1 class="fint l_fint">Вопросы-ответы</h1>
            <div class="row">
                @foreach ($entries as $entry)
                    <div class="col-md-6">
                        <div class="box" style="min-height: 260px;">
                            <p><strong>{{ $entry['date'] }}</strong></p>
                            <h3><a href="{{ route('faq.show', $entry['slug']) }}">{{ $entry['title'] }}</a></h3>
                            <p>{{ $entry['excerpt'] }}</p>
                            <a class="btn btn-primary" href="{{ route('faq.show', $entry['slug']) }}">Читать далее</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
