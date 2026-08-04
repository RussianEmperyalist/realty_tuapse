@extends('layouts.site')

@section('title', 'Вход в личный кабинет')

@section('content')
    <div class="content_box content">
        <div class="box" style="max-width: 520px; margin: 0 auto;">
            <h1 class="fint l_fint">Вход в личный кабинет</h1>
            <p>Вход предназначен для сотрудников агентства. Если доступ еще не выдан, воспользуйтесь формой регистрации.</p>
            <form method="post" action="{{ route('login.store') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="text-danger" style="margin-top: 8px;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input class="form-control" id="password" name="password" type="password" required>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="remember" value="1"> Запомнить меня</label>
                </div>
                <button class="btn btn-primary" type="submit">Войти</button>
            </form>
        </div>
    </div>
@endsection
