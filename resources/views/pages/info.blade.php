@extends('layouts.site')

@section('title', $page['title'])

@section('content')
    <div class="content_box content">
        <div class="box">
            <h1 class="fint l_fint">{{ $page['title'] }}</h1>
            {!! $page['content'] !!}
        </div>
    </div>
@endsection
