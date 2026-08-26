@extends('layouts.admin')

@section('title', 'Доступы выданы')

@section('content')
    <div class="admin-page-header">
        <div>
            <h1 style="margin: 0 0 8px;">Доступы выданы</h1>
            <p style="margin: 0; color: #667085;">
                Созданы login-only аккаунты
                ({{ $role === 'admin' ? 'Администратор' : 'Сотрудник' }}).
                Скопируйте логины и пароли и передайте сотрудникам вручную.
            </p>
        </div>
        <div class="admin-actions">
            <a class="btn btn-primary" href="{{ route('admin.employees.index') }}">К списку сотрудников</a>
        </div>
    </div>

    @if (count($results) === 0)
        <div class="admin-form-card">
            <p style="margin: 0;">Ни для кого из выбранных сотрудников не создан новый доступ (возможно, он уже есть).</p>
        </div>
    @else
        <div class="admin-table">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Сотрудник</th>
                            <th>Логин</th>
                            <th>Пароль</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($results as $row)
                            <tr>
                                <td>{{ $row['employee'] }}</td>
                                <td><code>{{ $row['login'] }}</code></td>
                                <td><code>{{ $row['password'] }}</code></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
