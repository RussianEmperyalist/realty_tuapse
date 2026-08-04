@extends('layouts.admin')

@section('title', 'Заявки')

@section('content')
    <div class="stat-card">
        <div class="admin-page-header">
            <div>
                <h1 style="margin: 0 0 8px;">Журнал заявок</h1>
                <p style="margin: 0;">Здесь собраны все запросы с сайта: формы связи, заявки по объектам, регистрация и восстановление доступа.</p>
            </div>
            <form method="get" action="{{ route('admin.inquiries.index') }}" style="min-width: 280px;">
                <div class="form-group" style="margin-bottom: 8px;">
                    <label for="type">Тип заявки</label>
                    <select class="form-control" id="type" name="type" onchange="this.form.submit()">
                        <option value="">Все заявки</option>
                        @foreach ($typeOptions as $value => $label)
                            <option value="{{ $value }}" @selected($typeFilter === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-table">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Тип</th>
                        <th>Контакт</th>
                        <th>Сообщение</th>
                        <th>Контекст</th>
                        <th>Почта</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>{{ optional($item['created_at'])->format('d.m.Y H:i') }}</td>
                            <td>{{ $item['type_label'] }}</td>
                            <td>
                                @if ($item['name'])
                                    <div><strong>{{ $item['name'] }}</strong></div>
                                @endif
                                @if ($item['email'])
                                    <div>{{ $item['email'] }}</div>
                                @endif
                                @if ($item['phone'])
                                    <div>{{ $item['phone'] }}</div>
                                @endif
                            </td>
                            <td style="min-width: 240px;">
                                {{ $item['message'] ?: '—' }}
                            </td>
                            <td>{{ $item['context'] ?: '—' }}</td>
                            <td>{{ $item['recipient_email'] }}</td>
                            <td>
                                <div>{{ $item['delivery_status'] }}</div>
                                @if ($item['delivery_error'])
                                    <div class="text-danger" style="margin-top: 6px; max-width: 280px;">{{ $item['delivery_error'] }}</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">Заявки пока не поступали.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
