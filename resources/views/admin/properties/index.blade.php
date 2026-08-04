@extends('layouts.admin')

@section('title', 'Объекты')

@section('content')
    <div class="admin-page-header">
        <div>
            <h1 style="margin: 0 0 8px;">Объекты недвижимости</h1>
            <p style="margin: 0; color: #667085;">Управление объявлениями, их параметрами и изображениями.</p>
        </div>
        <div class="admin-actions">
            <a class="btn btn-primary" href="{{ route('admin.properties.create') }}">Добавить объект</a>
        </div>
    </div>

    <div class="admin-form-card">
        <form class="row" method="get" action="{{ route('admin.properties.index') }}">
            <div class="col-md-5">
                <label for="properties-term">Поиск</label>
                <input class="form-control" id="properties-term" name="term" type="text" value="{{ request('term') }}" placeholder="Заголовок, адрес или ID">
            </div>
            @if (auth()->user()?->isAdmin() && !$isEmployeeModeActive)
                <div class="col-md-4">
                    <label for="properties-employee">Сотрудник</label>
                    <select class="form-control" id="properties-employee" name="employee">
                        <option value="">Все сотрудники</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" @selected($selectedEmployeeId === $employee->id)>{{ $employee->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-3" style="padding-top: 25px;">
                <button class="btn btn-default" type="submit">Фильтровать</button>
                <a class="btn btn-link" href="{{ route('admin.properties.index') }}">Сбросить</a>
            </div>
        </form>
    </div>

    <div class="admin-table">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Объект</th>
                    <th>Сотрудник</th>
                    <th>Сделка</th>
                    <th>Тип</th>
                    <th>Цена</th>
                    <th>Статус</th>
                    <th style="width: 220px;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($properties as $property)
                    <tr>
                        <td>{{ $property->legacy_id }}</td>
                        <td>
                            <strong>{{ $property->title }}</strong><br>
                            <span style="color:#667085;">{{ $property->address }}</span>
                        </td>
                        <td>{{ $property->employee?->full_name ?? 'Не назначен' }}</td>
                        <td>{{ config('realty.deal_type_options.' . $property->deal_type, $property->deal_type) }}</td>
                        <td>{{ config('realty.property_type_options.' . $property->property_type, $property->property_type) }}</td>
                        <td>{{ $property->price_label ?? number_format((int) $property->price, 0, ',', ' ') . ' ' . $property->currency }}</td>
                        <td>
                            @if ($property->is_published)
                                <span class="label label-success">Опубликован</span>
                            @else
                                <span class="label label-default">Скрыт</span>
                            @endif
                        </td>
                        <td>
                            <div class="admin-actions">
                                <a class="btn btn-xs btn-default" href="{{ route('properties.show', $property->slug) }}" target="_blank">Открыть</a>
                                <a class="btn btn-xs btn-primary" href="{{ route('admin.properties.edit', $property) }}">Редактировать</a>
                                <form method="post" action="{{ route('admin.properties.destroy', $property) }}" onsubmit="return confirm('Удалить объект?');">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-xs btn-danger" type="submit">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">Объекты пока не найдены.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $properties->links() }}
    </div>
@endsection
