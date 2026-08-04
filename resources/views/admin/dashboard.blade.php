@extends('layouts.admin')

@section('title', 'Дашборд')

@section('content')
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="text-muted">Объекты</div>
                <div class="h2" style="margin: 10px 0 0;">{{ $stats['properties'] }}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="text-muted">Сотрудники</div>
                <div class="h2" style="margin: 10px 0 0;">{{ $stats['employees'] }}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="text-muted">Новости</div>
                <div class="h2" style="margin: 10px 0 0;">{{ $stats['news'] }}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="text-muted">Альбомы галереи</div>
                <div class="h2" style="margin: 10px 0 0;">{{ $stats['gallery'] }}</div>
            </div>
        </div>
    </div>

    @if ($user?->isAdmin())
        <div class="stat-card">
            <div class="row">
                <div class="col-md-7">
                    <h2 style="margin-top: 0;">Почтовая отправка</h2>
                    <p style="margin-bottom: 18px;">
                        Здесь проверяется именно боевой сценарий. Если профиль не готов к внешней SMTP-отправке,
                        тестовое письмо не будет отправлено.
                    </p>
                    <div class="row">
                        <div class="col-sm-6">
                            <p><strong>Mailer:</strong> {{ $mailSummary['default_mailer'] }}</p>
                            <p><strong>Transport:</strong> {{ $mailSummary['transport'] }}</p>
                            <p><strong>From:</strong> {{ $mailSummary['from_address'] }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p><strong>SMTP host:</strong> {{ $mailSummary['host'] ?: 'не задан' }}</p>
                            <p><strong>SMTP port:</strong> {{ $mailSummary['port'] ?: 'не задан' }}</p>
                            <p><strong>Получатель заявок:</strong> {{ $mailSummary['recipient_email'] }}</p>
                        </div>
                    </div>

                    @if ($mailSummary['ready'])
                        <div class="alert alert-success" style="margin-bottom: 0;">
                            Конфигурация готова к внешней SMTP-отправке.
                        </div>
                    @else
                        <div class="alert alert-warning" style="margin-bottom: 0;">
                            {{ $mailSummary['issue'] }}
                        </div>
                    @endif
                </div>
                <div class="col-md-5">
                    <form method="post" action="{{ route('admin.mail-test') }}" class="admin-form-card" style="margin-bottom: 0;">
                        @csrf
                        <h3 style="margin-top: 0;">Тестовое письмо</h3>
                        <div class="form-group">
                            <label for="mail-test-email">Куда отправить</label>
                            <input
                                type="email"
                                class="form-control"
                                id="mail-test-email"
                                name="email"
                                value="{{ old('email', $mailSummary['test_email'] ?: $mailSummary['recipient_email']) }}"
                                required
                            >
                        </div>
                        <button type="submit" class="btn btn-primary">Отправить тест</button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <div class="stat-card">
        <div class="row">
            <div class="col-md-8">
                <h2 style="margin-top: 0;">Последние объекты</h2>
                <p>На этом этапе уже работает разграничение по ролям: администратор видит все объекты, сотрудник только свои.</p>
                @if (!empty($isEmployeeModeActive) && $employeeModeEmployee)
                    <div class="alert alert-info" style="margin-bottom: 0;">
                        Активен режим сотрудника:
                        <strong>{{ $employeeModeEmployee->full_name }}</strong>.
                        Чтобы вернуться к полному просмотру, выберите пункт
                        <strong>«Показывать все объявления»</strong>.
                    </div>
                @endif
            </div>
            @if ($user?->isAdmin())
                <div class="col-md-4">
                    <form method="post" action="{{ route('admin.employee-mode') }}">
                        @csrf
                        <div class="form-group">
                            <label for="employee">Режим сотрудника</label>
                            <select class="form-control" id="employee" name="employee_id" onchange="this.form.submit()">
                                <option value="">Показывать все объявления</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" @selected($employeeFilter === $employee->id)>{{ $employee->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Заголовок</th>
                        <th>Тип</th>
                        <th>Сделка</th>
                        <th>Сотрудник</th>
                        <th>Цена</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($properties as $property)
                        <tr>
                            <td>{{ $property->legacy_id }}</td>
                            <td>{{ $property->title }}</td>
                            <td>{{ config('realty.property_type_options.' . $property->property_type, $property->property_type) }}</td>
                            <td>{{ config('realty.deal_type_options.' . $property->deal_type, $property->deal_type) }}</td>
                            <td>{{ $property->employee?->full_name }}</td>
                            <td>{{ $property->price_label ?? number_format((int) $property->price, 0, ',', ' ') . ' ' . $property->currency }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">Объекты пока не найдены.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
