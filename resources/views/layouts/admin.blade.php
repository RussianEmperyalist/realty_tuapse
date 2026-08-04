<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>{{ trim($__env->yieldContent('title', 'Админ-панель')) }} | {{ config('realty.company_name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('legacy/themes/dolphin/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/themes/dolphin/assets/css/fontawesome-all.min.css') }}">
    <style>
        body {
            background: #f6f7fb;
            color: #222;
        }
        .admin-shell {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: 100vh;
        }
        .admin-sidebar {
            background: #143045;
            color: #fff;
            padding: 24px 20px;
        }
        .admin-sidebar a {
            color: #fff;
        }
        .admin-sidebar .nav > li > a {
            padding: 12px 0;
        }
        .admin-content {
            padding: 30px;
        }
        .admin-page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
            margin-bottom: 20px;
        }
        .admin-table {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        .admin-form-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
            margin-bottom: 24px;
        }
        .admin-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }
        .admin-grid--full {
            grid-column: 1 / -1;
        }
        .admin-media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 16px;
        }
        .admin-media-card {
            border: 1px solid #dfe5ee;
            border-radius: 10px;
            padding: 12px;
        }
        .admin-media-card img {
            width: 100%;
            height: 130px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 12px;
        }
        .admin-table table {
            margin: 0;
        }
        .admin-table .table > thead > tr > th,
        .admin-table .table > tbody > tr > td {
            vertical-align: middle;
        }
        .admin-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .admin-sidebar .nav > li.active > a,
        .admin-sidebar .nav > li > a:hover,
        .admin-sidebar .nav > li > a:focus {
            background: rgba(255, 255, 255, 0.12);
        }
        .admin-sidebar .form-control,
        .admin-sidebar .btn {
            margin-top: 10px;
        }
        @media (max-width: 991px) {
            .admin-shell {
                grid-template-columns: 1fr;
            }
            .admin-grid {
                grid-template-columns: 1fr;
            }
            .admin-page-header {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div style="margin-bottom: 24px;">
                <div class="h3" style="margin-top: 0;">Админ-панель</div>
                <div>{{ config('realty.company_name') }}</div>
            </div>
            <ul class="nav nav-pills nav-stacked">
                <li class="@if(request()->routeIs('admin.dashboard')) active @endif"><a href="{{ route('admin.dashboard') }}">Дашборд</a></li>
                <li class="@if(request()->routeIs('admin.properties.*')) active @endif"><a href="{{ route('admin.properties.index') }}">Объекты</a></li>
                @if (auth()->user()?->isAdmin())
                    <li class="@if(request()->routeIs('admin.employees.*')) active @endif"><a href="{{ route('admin.employees.index') }}">Сотрудники</a></li>
                    <li class="@if(request()->routeIs('admin.inquiries.*')) active @endif"><a href="{{ route('admin.inquiries.index') }}">Заявки</a></li>
                    <li class="@if(request()->routeIs('admin.news.*')) active @endif"><a href="{{ route('admin.news.index') }}">Новости</a></li>
                    <li class="@if(request()->routeIs('admin.gallery.*')) active @endif"><a href="{{ route('admin.gallery.index') }}">Галерея</a></li>
                @endif
                <li><a href="{{ route('home') }}" target="_blank">Открыть сайт</a></li>
            </ul>
            <hr>
            <p style="opacity: .8;">Вы вошли как <strong>{{ auth()->user()?->name }}</strong></p>
            <p style="opacity: .8;">Роль: {{ auth()->user()?->role === 'admin' ? 'Администратор' : 'Сотрудник' }}</p>
            @if (auth()->user()?->isAdmin())
                <form method="post" action="{{ route('admin.employee-mode') }}">
                    @csrf
                    <label for="employee-mode" style="display:block; opacity:.8;">Режим сотрудника</label>
                    <select class="form-control" id="employee-mode" name="employee_id" onchange="this.form.submit()">
                        <option value="">Показывать все объявления</option>
                        @foreach (\App\Models\Employee::query()->where('is_active', true)->orderBy('sort_order')->get() as $employeeModeEmployee)
                            <option value="{{ $employeeModeEmployee->id }}" @selected((int) session('admin_employee_mode') === $employeeModeEmployee->id)>{{ $employeeModeEmployee->full_name }}</option>
                        @endforeach
                    </select>
                </form>
            @endif
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-default btn-block">Выйти</button>
            </form>
        </aside>
        <main class="admin-content">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>
