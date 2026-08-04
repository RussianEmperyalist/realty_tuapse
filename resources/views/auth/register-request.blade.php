@extends('layouts.site')

@section('title', 'Регистрация')

@section('content')
    <div class="content_box content">
        <div class="box" style="max-width: 640px; margin: 0 auto;">
            <h1 class="fint l_fint">Регистрация</h1>
            <p>Личный кабинет на новом сайте создается администраторами агентства. Оставьте заявку, и мы откроем доступ сотруднику или согласуем дальнейшие шаги.</p>
            <form method="post" action="{{ route('register.store') }}">
                @csrf
                <div class="form-group">
                    <label for="register-name">Имя и фамилия</label>
                    <input class="form-control" id="register-name" name="name" type="text" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label for="register-email">Email</label>
                    <input class="form-control" id="register-email" name="email" type="email" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label for="register-phone">Телефон</label>
                    <input class="form-control" id="register-phone" name="phone" type="text" value="{{ old('phone') }}">
                </div>
                <div class="form-group">
                    <label for="register-message">Комментарий</label>
                    <textarea class="form-control" id="register-message" name="message" rows="6">{{ old('message') }}</textarea>
                </div>
                <button class="btn btn-primary" type="submit">Отправить заявку</button>
                @include('forms.partials.legal-consent')
            </form>
        </div>
    </div>
@endsection
