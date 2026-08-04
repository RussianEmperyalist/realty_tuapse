@extends('layouts.site')

@section('title', 'Восстановление пароля')

@section('content')
    <div class="content_box content">
        <div class="box" style="max-width: 640px; margin: 0 auto;">
            <h1 class="fint l_fint">Восстановление пароля</h1>
            <p>Если доступ к кабинету потерян, оставьте email и телефон. Мы проверим аккаунт и поможем восстановить вход.</p>
            <form method="post" action="{{ route('recover.store') }}">
                @csrf
                <div class="form-group">
                    <label for="recover-email">Email</label>
                    <input class="form-control" id="recover-email" name="email" type="email" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label for="recover-phone">Телефон</label>
                    <input class="form-control" id="recover-phone" name="phone" type="text" value="{{ old('phone') }}">
                </div>
                <div class="form-group">
                    <label for="recover-message">Комментарий</label>
                    <textarea class="form-control" id="recover-message" name="message" rows="6">{{ old('message') }}</textarea>
                </div>
                <button class="btn btn-primary" type="submit">Запросить восстановление</button>
                @include('forms.partials.legal-consent')
            </form>
        </div>
    </div>
@endsection
