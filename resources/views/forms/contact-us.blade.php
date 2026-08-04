@extends('layouts.site')

@section('title', 'Связаться с нами')

@section('content')
    <div class="content_box content">
        <div class="box">
            <h1 class="fint l_fint">Связаться с нами</h1>
            @include('forms.partials.feedback')
            <form method="post" action="{{ route('contact.store') }}">
                @csrf
                <div class="form-group">
                    <label for="name">Ваше имя</label>
                    <input class="form-control" id="name" name="name" type="text" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <label for="phone">Телефон</label>
                    <input class="form-control" id="phone" name="phone" type="text" value="{{ old('phone') }}">
                </div>
                <div class="form-group">
                    <label for="message">Сообщение</label>
                    <textarea class="form-control" id="message" name="message" rows="6">{{ old('message') }}</textarea>
                </div>
                <button class="btn btn-primary" type="submit">Отправить</button>
                @include('forms.partials.legal-consent')
            </form>
        </div>
    </div>
@endsection
