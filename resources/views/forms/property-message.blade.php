@extends('layouts.site')

@section('title', 'Сообщение по объекту')

@section('content')
    <div class="content_box content">
        <div class="box">
            <h1 class="fint l_fint">Сообщение по объекту</h1>
            @include('forms.partials.feedback')
            <p><strong>Объект:</strong> <a href="{{ route('properties.show', $property->slug) }}">{{ $property->title }}</a></p>
            <p><strong>ID объекта:</strong> {{ $property->legacy_id }}</p>
            <form method="post" action="{{ route('property-message.store') }}">
                @csrf
                <input type="hidden" name="property_id" value="{{ $property->id }}">
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
                    <textarea class="form-control" id="message" name="message" rows="6" required>{{ old('message') }}</textarea>
                </div>
                <button class="btn btn-primary" type="submit">Отправить сообщение</button>
                @include('forms.partials.legal-consent')
            </form>
        </div>
    </div>
@endsection
