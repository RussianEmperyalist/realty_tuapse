<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>{{ trim($__env->yieldContent('title', 'Личный кабинет')) }} | {{ config('realty.company_name') }}</title>
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
            overflow-y: auto;
        }
        .admin-sidebar a {
            color: #fff;
        }
        .admin-sidebar .nav > li > a {
            padding: 12px 0;
        }
        .admin-brand {
            text-align: center;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.10);
            border-radius: 12px;
            padding: 16px 10px 14px;
        }
        .admin-brand__label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255, 255, 255, 0.55);
            margin-bottom: 10px;
        }
        .admin-brand__inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        .admin-brand__inner img {
            width: 120px;
            max-width: 100%;
        }
        .admin-brand__name {
            display: block;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.25;
            color: #fff;
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
        .admin-topbar {
            display: none;
        }
        .admin-overlay {
            display: none;
        }
        @media (max-width: 991px) {
            .admin-shell {
                grid-template-columns: 1fr;
            }
            .admin-topbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 12px 16px;
                background: #143045;
                color: #fff;
                position: sticky;
                top: 0;
                z-index: 1010;
                gap: 12px;
            }
            .admin-topbar__brand {
                font-size: 14px;
                font-weight: 700;
                color: #fff;
            }
            .admin-topbar__toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                border: none;
                border-radius: 8px;
                background: rgba(255, 255, 255, 0.15);
                color: #fff;
                cursor: pointer;
                font-size: 20px;
            }
            .admin-topbar__toggle:hover {
                background: rgba(255, 255, 255, 0.25);
            }
            .admin-sidebar {
                position: fixed;
                top: 0;
                left: -280px;
                width: 280px;
                height: 100dvh;
                z-index: 1020;
                transition: left .25s ease;
                box-shadow: none;
            }
            .admin-sidebar.open {
                left: 0;
                box-shadow: 8px 0 30px rgba(0, 0, 0, 0.3);
            }
            .admin-overlay {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.4);
                z-index: 1015;
                opacity: 0;
                pointer-events: none;
                transition: opacity .25s ease;
            }
            .admin-overlay.visible {
                opacity: 1;
                pointer-events: auto;
            }
            .admin-content {
                padding: 16px;
            }
            .admin-grid {
                grid-template-columns: 1fr;
            }
            .admin-page-header {
                flex-direction: column;
            }
            .admin-form-card {
                padding: 16px;
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .admin-sidebar {
                transition: none;
            }
            .admin-overlay {
                transition: none;
            }
        }
    </style>
</head>
<body>
    <div class="admin-overlay" id="admin-overlay"></div>
    <div class="admin-shell">
        <header class="admin-topbar" id="admin-topbar">
            <button type="button" class="admin-topbar__toggle" id="admin-menu-btn" aria-label="Меню">
                <i class="fa fa-bars"></i>
            </button>
            <span class="admin-topbar__brand">{{ config('realty.company_display_name') }}</span>
            <div></div>
        </header>
        <aside class="admin-sidebar" id="admin-sidebar">
            <div class="admin-brand" style="margin-bottom: 24px;">
                <span class="admin-brand__label">Личный кабинет</span>
                <div class="admin-brand__inner">
                    <img src="{{ asset('legacy/themes/dolphin/assets/images/logo.png') }}" alt="{{ config('realty.company_display_name') }}">
                    <span class="admin-brand__name">{{ config('realty.company_display_name') }}</span>
                </div>
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
    <script>
        (function () {
            var btn = document.getElementById('admin-menu-btn');
            var sidebar = document.getElementById('admin-sidebar');
            var overlay = document.getElementById('admin-overlay');
            if (!btn || !sidebar || !overlay) return;

            function open() {
                sidebar.classList.add('open');
                overlay.classList.add('visible');
                document.body.style.overflow = 'hidden';
            }
            function close() {
                sidebar.classList.remove('open');
                overlay.classList.remove('visible');
                document.body.style.overflow = '';
            }

            btn.addEventListener('click', open);
            overlay.addEventListener('click', close);

            sidebar.querySelectorAll('a').forEach(function (a) {
                a.addEventListener('click', close);
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') close();
            });
        })();
    </script>
</body>
</html>
