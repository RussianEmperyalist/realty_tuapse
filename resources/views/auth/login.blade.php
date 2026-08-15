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
                    <div class="password-toggle-wrap">
                        <input class="form-control" id="password" name="password" type="password" required>
                        <button type="button" class="password-toggle" data-target="password" aria-label="Показать пароль">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="remember" value="1"> Запомнить меня</label>
                </div>
                <button class="btn btn-primary" type="submit">Войти</button>
            </form>
        </div>
    </div>
@push('styles')
    <style>
        .password-toggle-wrap {
            position: relative;
        }
        .password-toggle-wrap .form-control {
            padding-right: 42px;
        }
        .password-toggle {
            position: absolute;
            right: 2px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            padding: 8px 10px;
            cursor: pointer;
            color: #777;
            font-size: 16px;
            line-height: 1;
        }
        .password-toggle:hover {
            color: #333;
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            var buttons = document.querySelectorAll('.password-toggle');
            buttons.forEach(function (button) {
                button.addEventListener('click', function () {
                    var input = document.getElementById(button.getAttribute('data-target'));
                    if (!input) return;
                    var show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    button.querySelector('i').className = show ? 'fa fa-eye-slash' : 'fa fa-eye';
                    button.setAttribute('aria-label', show ? 'Скрыть пароль' : 'Показать пароль');
                });
            });
        })();
    </script>
@endpush
@endsection
