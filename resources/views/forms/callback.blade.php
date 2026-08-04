@extends('layouts.site')

@section('title', 'Заказать обратный звонок')

@section('content')
    <div class="content_box content">
        <div class="box">
            <h1 class="fint l_fint">Заказать обратный звонок</h1>
            @include('forms.partials.feedback')
            <form method="post" action="{{ route('callback.store') }}">
                @csrf
                <div class="form-group">
                    <label for="name">Ваше имя</label>
                    <input class="form-control" id="name" name="name" type="text" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label for="phone">Телефон</label>
                    <input class="form-control" id="phone" name="phone" type="text" value="{{ old('phone') }}" required>
                </div>
                <div class="form-group">
                    <label for="message">Комментарий</label>
                    <textarea class="form-control" id="message" name="message" rows="5">{{ old('message') }}</textarea>
                </div>
                <button class="btn btn-primary" type="submit">Отправить</button>
                @include('forms.partials.legal-consent')
            </form>
        </div>
    </div>
@endsection
