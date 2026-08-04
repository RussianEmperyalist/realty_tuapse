@extends('layouts.admin')

@section('title', 'Сотрудники')

@section('content')
    <div class="admin-page-header">
        <div>
            <h1 style="margin: 0 0 8px;">Сотрудники</h1>
            <p style="margin: 0; color: #667085;">Порядок в контактах, роли и доступы к личному кабинету.</p>
        </div>
        <div class="admin-actions">
            <a class="btn btn-primary" href="{{ route('admin.employees.create') }}">Добавить сотрудника</a>
        </div>
    </div>

    <div class="admin-table">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Порядок</th>
                    <th>Сотрудник</th>
                    <th>Должность</th>
                    <th>Контакты</th>
                    <th>Личный кабинет</th>
                    <th>Статус</th>
                    <th style="width: 220px;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $employee)
                    <tr>
                        <td>{{ $employee->sort_order }}</td>
                        <td>
                            <strong>{{ $employee->full_name }}</strong><br>
                            <span style="color:#667085;">ID: {{ $employee->legacy_id }}</span>
                        </td>
                        <td>{{ $employee->position }}</td>
                        <td>
                            {{ $employee->phone_primary }}<br>
                            <span style="color:#667085;">{{ $employee->email }}</span>
                        </td>
                        <td>
                            @if ($employee->user)
                                {{ $employee->user->email }}<br>
                                <span class="label label-info">{{ $employee->user->role === 'admin' ? 'Администратор' : 'Сотрудник' }}</span>
                            @else
                                <span class="label label-default">Нет доступа</span>
                            @endif
                        </td>
                        <td>
                            @if ($employee->is_active)
                                <span class="label label-success">Активен</span>
                            @else
                                <span class="label label-default">Скрыт</span>
                            @endif
                        </td>
                        <td>
                            <div class="admin-actions">
                                <a class="btn btn-xs btn-default" href="{{ route('employees.show', ['id' => $employee->legacy_id]) }}" target="_blank">Открыть</a>
                                <a class="btn btn-xs btn-primary" href="{{ route('admin.employees.edit', $employee) }}">Редактировать</a>
                                <form method="post" action="{{ route('admin.employees.destroy', $employee) }}" onsubmit="return confirm('Удалить сотрудника?');">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-xs btn-danger" type="submit">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Сотрудники пока не найдены.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $employees->links() }}
    </div>
@endsection
