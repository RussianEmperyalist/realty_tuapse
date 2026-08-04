@extends('layouts.site')

@section('title', $title)

@section('content')
    <div class="content_box content">
        <div class="box">
            <h1 class="fint l_fint">{{ $title }}</h1>
            <p>Раздел переносится на новый backend. Публичный URL сохранен, чтобы не ломать привычную навигацию сайта.</p>
        </div>
    </div>
@endsection
