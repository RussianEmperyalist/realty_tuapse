@extends('layouts.admin')

@section('title', $employee->exists ? 'Редактирование сотрудника' : 'Новый сотрудник')

@section('content')
    <div class="admin-page-header">
        <div>
            <h1 style="margin: 0 0 8px;">{{ $employee->exists ? 'Редактирование сотрудника' : 'Новый сотрудник' }}</h1>
            <p style="margin: 0; color: #667085;">Карточка сотрудника и доступ к кабинету.</p>
        </div>
        <div class="admin-actions">
            <a class="btn btn-default" href="{{ route('admin.employees.index') }}">К списку</a>
        </div>
    </div>

    <form method="post" action="{{ $formAction }}" enctype="multipart/form-data">
        @csrf
        @if ($method !== 'post')
            @method($method)
        @endif

        <div class="admin-form-card">
            <div class="admin-grid">
                <div>
                    <label for="legacy_id">Legacy ID</label>
                    <input class="form-control" id="legacy_id" name="legacy_id" type="number" value="{{ old('legacy_id', $employee->legacy_id) }}">
                </div>
                <div>
                    <label for="sort_order">Порядок в списке</label>
                    <input class="form-control" id="sort_order" name="sort_order" type="number" value="{{ old('sort_order', $employee->sort_order) }}">
                </div>
                <div class="admin-grid--full">
                    <label for="full_name">ФИО</label>
                    <input class="form-control" id="full_name" name="full_name" type="text" value="{{ old('full_name', $employee->full_name) }}" required>
                </div>
                <div>
                    <label for="slug">Slug</label>
                    <input class="form-control" id="slug" name="slug" type="text" value="{{ old('slug', $employee->slug) }}">
                </div>
                <div>
                    <label for="position">Должность</label>
                    <input class="form-control" id="position" name="position" type="text" value="{{ old('position', $employee->position) }}" required>
                </div>
                <div>
                    <label for="phone_primary">Основной телефон</label>
                    <input class="form-control" id="phone_primary" name="phone_primary" type="text" value="{{ old('phone_primary', $employee->phone_primary) }}">
                </div>
                <div>
                    <label for="phone_secondary">Дополнительный телефон</label>
                    <input class="form-control" id="phone_secondary" name="phone_secondary" type="text" value="{{ old('phone_secondary', $employee->phone_secondary) }}">
                </div>
                <div>
                    <label for="email">Публичный email</label>
                    <input class="form-control" id="email" name="email" type="email" value="{{ old('email', $employee->email) }}">
                </div>
                <div class="admin-grid--full">
                    <label for="bio">Описание</label>
                    <textarea class="form-control" id="bio" name="bio" rows="6">{{ old('bio', $employee->bio) }}</textarea>
                </div>
            </div>

            <div class="checkbox" style="margin-top: 20px;">
                <label>
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked((bool) old('is_active', $employee->is_active))> Показывать сотрудника на сайте
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="hidden" name="is_admin" value="0">
                    <input type="checkbox" name="is_admin" value="1" @checked((bool) old('is_admin', $employee->is_admin))> Администратор компании
                </label>
            </div>
        </div>

        <div class="admin-form-card">
            <h2 style="margin-top: 0;">Фото</h2>
            @if ($employee->photo_path)
                <div style="margin-bottom: 20px; max-width: 240px;">
                    <img src="{{ asset($employee->photo_path) }}" alt="{{ $employee->full_name }}" style="width: 100%; border-radius: 8px;">
                    <div class="checkbox" style="margin-top: 12px;">
                        <label><input type="checkbox" name="delete_photo" value="1"> Удалить текущее фото</label>
                    </div>
                </div>
            @endif
            <div class="form-group">
                <label for="photo">Загрузить новое фото</label>
                <input id="photo" name="photo" type="file">
            </div>
        </div>

        <div class="admin-form-card">
            <h2 style="margin-top: 0;">Доступ в кабинет</h2>
            <div class="admin-grid">
                <div>
                    <label for="login_email">Email для входа</label>
                    <input class="form-control" id="login_email" name="login_email" type="email" value="{{ old('login_email', $employee->user?->email) }}">
                </div>
                <div>
                    <label for="login_role">Роль в системе</label>
                    <select class="form-control" id="login_role" name="login_role">
                        <option value="">Не создавать доступ</option>
                        <option value="employee" @selected(old('login_role', $employee->user?->role) === 'employee')>Сотрудник</option>
                        <option value="admin" @selected(old('login_role', $employee->user?->role) === 'admin')>Администратор</option>
                    </select>
                </div>
                <div class="admin-grid--full">
                    <label for="login_password">Пароль</label>
                    <input class="form-control" id="login_password" name="login_password" type="text" value="">
                    <p style="margin-top: 8px; color: #667085;">Оставьте пустым, если менять пароль не нужно.</p>
                </div>
            </div>
        </div>

        <div class="admin-actions">
            <button class="btn btn-primary" type="submit">Сохранить</button>
            <a class="btn btn-default" href="{{ route('admin.employees.index') }}">Отменить</a>
        </div>
    </form>
@endsection
