<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>@yield('title', config('realty.company_name'))</title>
    <meta name="description" content="@yield('meta_description', 'Недвижимость Туапсе')">
    <meta name="keywords" content="Недвижимость Туапсе">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('legacy/favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('legacy/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('legacy/common/js/cookiebar/jquery.cookiebar.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/themes/dolphin/css/ui/jquery-ui.multiselect.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/themes/dolphin/css/redmond/jquery-ui-1.7.1.custom.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/themes/dolphin/css/ui.slider.extras.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/themes/dolphin/js/sumoselect/sumoselect.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/themes/dolphin/css/form.css') }}" media="screen">
    <link rel="stylesheet" href="{{ asset('legacy/themes/dolphin/assets/css/jquery.accordion.menu.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/themes/dolphin/js/mscrollbar/jquery.mCustomScrollbar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/themes/dolphin/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/themes/dolphin/assets/css/fontawesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/themes/dolphin/assets/css/pretty-checkbox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/themes/dolphin/assets/js/slick/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/themes/dolphin/assets/js/slinky/slinky.min.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/themes/dolphin/assets/js/slinky/slide_menu.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/themes/dolphin/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/themes/dolphin/assets/css/media-queries.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/themes/dolphin/assets/css/style_img.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/assets/a6b31464/src/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/assets/e0d915dc/style.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/assets/8dcfd5a5/jquery.fancybox-1.3.4.css') }}">
    <style>
        :root {
            --rt-sidebar-width: clamp(300px, 21vw, 380px);
            --rt-content-max: 1720px;
            --rt-gutter: clamp(20px, 2vw, 36px);
            --rt-radius: 18px;
            --rt-shadow: 0 16px 34px rgba(20, 48, 69, 0.08);
            --rt-surface: #ffffff;
            --rt-page-bg: #f5f4f1;
            --rt-border: #e4ebf3;
            --rt-text: #1f2937;
            --rt-muted: #667085;
            --rt-brand: #4f6288;
        }

        html,
        body {
            overflow-x: hidden;
        }

        body {
            background: var(--rt-page-bg);
            color: var(--rt-text);
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: -100%;
            z-index: 1002;
            width: min(400px, 92vw);
            height: 100dvh;
            background: var(--rt-surface);
            box-shadow: 18px 0 42px rgba(20, 48, 69, 0.2);
            transition: left .28s ease;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(20, 48, 69, 0.34);
            opacity: 0;
            pointer-events: none;
            transition: opacity .28s ease;
            z-index: 1001;
        }

        body.sidebar-open::before {
            opacity: 1;
            pointer-events: auto;
        }

        body.sidebar-open .sidebar {
            left: 0;
        }

        .sidebar .logo {
            display: none;
        }

        .sidebar_inner {
            height: 100%;
            padding: 22px 16px 28px;
            overflow-y: auto;
            overflow-x: hidden;
            background: var(--rt-surface);
        }

        .sidebar .row {
            margin-left: -6px;
            margin-right: -6px;
        }

        .sidebar [class*='col-'] {
            padding-left: 6px;
            padding-right: 6px;
        }

        .search_index .h3 {
            display: block;
            margin: 0 0 14px;
        }

        .search_index .form-control,
        .search_index .btn,
        .search_index .select2-container {
            width: 100%;
        }

        .col_right {
            position: relative;
            left: 0;
            display: flex;
            flex-direction: column;
            width: 100%;
            min-height: 100dvh;
            padding: 12px;
            overflow: visible;
        }

        .rt-header-brand,
        .wrapper_main_menu,
        .footer {
            background: var(--rt-surface);
            border-radius: var(--rt-radius);
            box-shadow: var(--rt-shadow);
        }

        .rt-header-brand,
        .wrapper_top_link,
        .wrapper_main_menu,
        .main-content-wrapper,
        .footer,
        .breadcrumb {
            margin-left: auto;
            margin-right: auto;
            max-width: 100%;
        }

        .rt-header-brand {
            display: block;
            margin-bottom: 12px;
            padding: 18px 16px 14px;
            text-align: center;
        }

        .rt-header-brand a {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .rt-header-brand__logo img {
            width: min(180px, 56vw);
        }

        .rt-header-brand__text {
            display: block;
            max-width: 300px;
            color: var(--rt-brand);
            font-size: clamp(13px, 3.5vw, 16px);
            line-height: 1.15;
            font-weight: 700;
            text-align: center;
        }

        .wrapper_top_link {
            margin-bottom: 10px;
        }

        .wrapper_top_link .row.hidden-xs {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin: 0;
        }

        .wrapper_top_link .row.hidden-xs::before,
        .wrapper_top_link .row.hidden-xs::after {
            display: none;
        }

        .top_link_bl,
        .top_link_bl1 > ul,
        .top_menu > .navbar-nav,
        .main_menu .nav-pills {
            float: none;
        }

        .top_link_bl {
            padding-left: 0;
            padding-right: 0;
        }

        .top_link_bl1 > ul,
        .top_menu > .navbar-nav {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin: 0;
            padding: 0;
        }

        .top_menu .navbar-nav > li,
        .main_menu .nav-pills > li {
            float: none;
        }

        .wrapper_top_link .btn {
            min-height: 46px;
            padding: 0 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        .wrapper_main_menu {
            padding: 12px;
            margin-bottom: 12px;
        }

        .main_menu {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .mobile-button {
            display: flex !important;
            float: none;
            margin: 0;
            justify-content: center;
        }

        .mobile-button ul {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .mobile-button li {
            float: none;
        }

        .mobile-button button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-width: 110px;
            min-height: 44px;
            padding: 0 14px;
            border-radius: 12px;
        }

        #main_menu_nav_3 {
            display: none;
            width: 100%;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .main_menu.is-open #main_menu_nav_3 {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .main_menu.is-open #main_menu_nav_3 > li {
            width: 100%;
        }

        .main_menu.is-open #main_menu_nav_3 > li > a {
            display: flex;
            align-items: center;
            min-height: 44px;
            padding: 12px 14px;
            border-radius: 12px;
            white-space: normal;
        }

        .main_menu.is-open .dropdown-menu {
            position: static;
            float: none;
            width: 100%;
            border: 0;
            box-shadow: none;
            padding: 4px 0 4px 14px;
            margin: 0;
        }

        .main_menu.is-open .dropdown.open > .dropdown-menu {
            display: block;
        }

        .main-content-wrapper {
            display: flex;
            flex-direction: column;
            flex: 1 0 auto;
            gap: 12px;
        }

        .header-wrapper,
        .footer {
            width: 100%;
            flex: 0 0 auto;
        }

        .breadcrumb {
            display: flex;
            flex-wrap: wrap;
            gap: 6px 10px;
            padding: 12px 14px;
            margin-bottom: 12px;
            background: var(--rt-surface);
            border-radius: 14px;
            box-shadow: var(--rt-shadow);
            list-style: none;
        }

        .breadcrumb > li {
            float: none;
        }

        .footer {
            margin-top: 14px;
            padding: 22px 18px 16px;
        }

        .rt-footer-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 26px;
        }

        .rt-footer-panel,
        .rt-footer-about-grid,
        .rt-footer-about-text,
        .rt-footer-contacts-wrap {
            min-width: 0;
        }

        .rt-footer-about-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 18px 22px;
        }

        .rt-footer-brand .logo a {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            background: none !important;
        }

        .rt-footer-brand .logo-img img {
            width: min(180px, 58vw);
            margin: 0 auto;
        }

        .rt-footer-brand .logo-text {
            display: block;
            max-width: 280px;
            color: var(--rt-brand);
            font-size: 16px;
            line-height: 1.15;
            font-weight: 700;
            text-align: center;
        }

        .footer_bottom .row {
            display: flex;
            flex-direction: column;
            gap: 18px;
            margin: 0;
        }

        .footer .h3 {
            margin-top: 0;
            margin-bottom: 14px;
        }

        .footer-contacts {
            margin-bottom: 12px;
        }

        .footer-contacts li,
        .footer-phones li {
            line-height: 1.5;
        }

        .footer-contacts a,
        .footer-phones a,
        .footer-links a {
            color: #2386c8;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .footer-phones {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px 12px;
            margin-bottom: 0;
        }

        .rt-footer-about-text {
            color: var(--rt-text);
            line-height: 1.65;
            font-size: 15px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .rt-footer-about-text p {
            margin: 0;
            max-width: 100%;
            max-width: 58ch;
            word-break: normal;
            overflow-wrap: break-word;
            white-space: normal;
        }

        .footer-links {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            margin-bottom: 0;
        }

        .footer-links li {
            margin: 0;
        }

        .footer-links a {
            display: inline-flex;
            align-items: center;
            min-height: 20px;
        }

        .footer-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .footer-actions .footer-action {
            flex: 0 0 auto;
            min-height: 52px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 0 18px;
            border-radius: 8px;
            border: 1px solid #d8e1ec;
            font-weight: 700;
            line-height: 1.2;
            white-space: normal;
            box-shadow: 0 10px 24px rgba(20, 48, 69, 0.08);
            transition: background-color .2s ease, border-color .2s ease, color .2s ease, transform .2s ease, box-shadow .2s ease;
        }

        .footer-actions .footer-action i {
            font-size: 15px;
        }

        .footer-actions .footer-action--light {
            color: var(--rt-brand);
            background: #fff;
        }

        .footer-actions .footer-action--primary {
            color: #fff;
            border-color: #4f6288;
            background: linear-gradient(135deg, #566b94, #43567b);
        }

        .footer-actions .footer-action:hover,
        .footer-actions .footer-action:focus {
            transform: translateY(-1px);
            text-decoration: none;
            box-shadow: 0 14px 28px rgba(20, 48, 69, 0.12);
        }

        .footer-actions .footer-action--light:hover,
        .footer-actions .footer-action--light:focus {
            color: #344767;
            border-color: #b8c7da;
            background: #f7fbff;
        }

        .footer-actions .footer-action--primary:hover,
        .footer-actions .footer-action--primary:focus {
            color: #fff;
            border-color: #3e5073;
            background: linear-gradient(135deg, #60759f, #3e5073);
        }

        .footer_bottom {
            margin-top: 16px;
            padding-top: 12px;
            border-top: 1px solid var(--rt-border);
        }

        .footer_bottom .row {
            gap: 8px;
        }

        .footer_bottom [class*='col-'] {
            text-align: center;
        }

        .close_btn_slide {
            display: none;
            align-items: center;
            justify-content: center;
            position: fixed;
            top: 14px;
            right: 14px;
            z-index: 1003;
            width: 44px;
            height: 44px;
            border-radius: 999px;
            border: 0;
            background: rgba(79, 98, 136, 0.95);
            color: #fff;
            box-shadow: 0 10px 24px rgba(20, 48, 69, 0.18);
        }

        body.sidebar-open .close_btn_slide {
            display: inline-flex;
        }

        @media only screen and (min-width: 768px) {
            .col_right {
                padding: 18px;
            }

            .rt-header-brand {
                padding: 22px 18px 18px;
            }

            .rt-header-brand__logo img {
                width: 220px;
            }

            .wrapper_top_link .row.hidden-xs,
            .top_link_bl1 > ul,
            .top_menu > .navbar-nav {
                gap: 12px;
            }

            .wrapper_main_menu {
                padding: 16px;
            }

            .footer {
                padding: 26px 24px 16px;
            }

            .rt-footer-grid {
                gap: 24px;
            }

            .rt-footer-about-grid {
                grid-template-columns: minmax(170px, 210px) minmax(240px, 1fr);
                grid-template-areas:
                    "brand contacts"
                    "about about";
                align-items: start;
            }

            .rt-footer-brand {
                grid-area: brand;
            }

            .rt-footer-contacts-wrap {
                grid-area: contacts;
            }

            .rt-footer-about-text {
                grid-area: about;
            }
        }

        @media only screen and (min-width: 992px) {
            .col_right {
                padding: 20px 18px;
            }

            .rt-header-brand {
                margin-bottom: 14px;
            }

            .mobile-button {
                margin-bottom: 0;
            }

            .btn-main-menu {
                display: none !important;
            }

            #main_menu_nav_3 {
                display: flex !important;
                flex-wrap: wrap;
                justify-content: center;
                gap: 8px;
            }

            #main_menu_nav_3 > li > a {
                display: flex;
                align-items: center;
                min-height: 44px;
                padding: 10px 16px;
                white-space: nowrap;
                border-radius: 12px;
            }

            .rt-footer-grid {
                grid-template-columns: minmax(0, 1.35fr) minmax(190px, 0.72fr) minmax(220px, 0.62fr);
                align-items: start;
                gap: 28px;
            }

            .rt-footer-about-grid {
                grid-template-columns: minmax(150px, 190px) minmax(230px, 1fr);
                grid-template-areas:
                    "brand contacts"
                    "about about";
                gap: 18px 26px;
                align-items: start;
            }

            .rt-footer-brand .logo a {
                align-items: flex-start;
            }

            .rt-footer-brand .logo-img img {
                width: 180px;
                margin: 0;
            }

            .rt-footer-brand .logo-text {
                text-align: left;
            }

            .footer-links {
                gap: 12px;
            }

            .footer-actions {
                padding-top: 5px;
            }

            .footer_bottom .row {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }

            .footer_bottom [class*='col-']:last-child {
                text-align: right;
            }
        }

        @media only screen and (min-width: 1280px) {
            html,
            body {
                height: 100%;
                overflow: hidden;
            }

            body::before {
                display: none;
            }

            .rt-header-brand {
                display: none;
            }

            .sidebar {
                left: 0;
                width: var(--rt-sidebar-width);
                height: 100vh;
                background: transparent;
                box-shadow: none;
            }

            .sidebar .logo {
                display: block;
            }

            .sidebar_inner {
                height: 100vh;
                padding: 36px 24px 40px;
                background: transparent;
                overflow-y: auto;
                overflow-x: hidden;
            }

            .col_right {
                left: var(--rt-sidebar-width);
                width: calc(100% - var(--rt-sidebar-width));
                height: 100vh;
                padding-left: var(--rt-gutter);
                padding-right: var(--rt-gutter);
                overflow-y: auto;
                overflow-x: hidden;
                overscroll-behavior: contain;
                -webkit-overflow-scrolling: touch;
            }

            .wrapper_top_link,
            .wrapper_main_menu,
            .main-content-wrapper,
            .footer,
            .breadcrumb {
                max-width: calc(var(--rt-content-max) - (var(--rt-gutter) * 2));
            }

            .footer {
                margin-top: auto;
            }

            .rt-footer-grid {
                grid-template-columns: minmax(0, 1.45fr) minmax(200px, 0.65fr) minmax(220px, 0.58fr);
                gap: 28px;
            }

            .mobile-button {
                display: none !important;
            }
        }
    </style>
    @stack('styles')
    @stack('maps')
</head>
<body class="{{ $bodyClass ?? 'inner_page' }}">
    <script src="{{ asset('legacy/assets/e0622829/jquery.min.js') }}"></script>
    <script src="{{ asset('legacy/assets/e0622829/jui/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('legacy/assets/e0622829/jui/js/jquery-ui-i18n.min.js') }}"></script>
    <script src="{{ asset('legacy/themes/dolphin/js/mscrollbar/jquery.mCustomScrollbar.min.js') }}"></script>
    <script src="{{ asset('legacy/themes/dolphin/assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('legacy/themes/dolphin/assets/js/slinky/slinky.min.js') }}"></script>
    <script src="{{ asset('legacy/themes/dolphin/assets/js/slick/slick.min.js') }}"></script>
    <div class="sidebar">
        @include('partials.sidebar-search')
    </div>
    <div class="col_right">
        <div class="header-wrapper">
            @include('partials.site-header')
        </div>
        @isset($breadcrumbs)
            @include('partials.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
        @endisset
        <div class="main-content-wrapper">
            @if (session('status'))
                <div class="flash-notice box" style="display: block; margin-bottom: 20px;">
                    {{ session('status') }}
                </div>
            @endif
            @yield('content')
        </div>
        @include('partials.site-footer')
    </div>
    <script>
        window.RealtyFavorites = {
            getItems() {
                try {
                    const items = JSON.parse(localStorage.getItem('realty-favorites') || '[]');
                    return Array.isArray(items) ? items : [];
                } catch (error) {
                    return [];
                }
            },
            saveItems(items) {
                localStorage.setItem('realty-favorites', JSON.stringify(items));
                this.refreshButtons();
            },
            isFavorite(slug) {
                return this.getItems().some(function (item) {
                    return item.slug === slug;
                });
            },
            toggle(item, button) {
                const items = this.getItems();
                const exists = items.some(function (favoriteItem) {
                    return favoriteItem.slug === item.slug;
                });

                const nextItems = exists
                    ? items.filter(function (favoriteItem) {
                        return favoriteItem.slug !== item.slug;
                    })
                    : items.concat([item]);

                this.saveItems(nextItems);

                if (button) {
                    button.textContent = exists ? 'В избранное' : 'Убрать из избранного';
                }
            },
            refreshButtons() {
                document.querySelectorAll('[data-favorite-button]').forEach((button) => {
                    const slug = button.getAttribute('data-favorite-slug');
                    button.textContent = this.isFavorite(slug) ? 'Убрать из избранного' : 'В избранное';
                });
            }
        };

        document.addEventListener('DOMContentLoaded', function () {
            const body = document.body;
            const sidebar = document.querySelector('.sidebar');
            const mainMenu = document.querySelector('.main_menu');
            const closeSidebarButton = document.querySelector('.close_btn_slide');
            const sidebarBreakpoint = 1280;
            const collapsedMenuBreakpoint = 992;

            function closeMobileChrome() {
                body.classList.remove('sidebar-open');
                mainMenu?.classList.remove('is-open');
                document.querySelectorAll('.main_menu .dropdown.open').forEach((item) => {
                    item.classList.remove('open');
                });
            }

            document.querySelectorAll('.btn-search').forEach((button) => {
                button.addEventListener('click', function () {
                    if (window.innerWidth >= sidebarBreakpoint) {
                        return;
                    }

                    body.classList.toggle('sidebar-open');
                });
            });

            document.querySelectorAll('.btn-main-menu').forEach((button) => {
                button.addEventListener('click', function () {
                    if (window.innerWidth >= collapsedMenuBreakpoint) {
                        return;
                    }

                    mainMenu?.classList.toggle('is-open');
                });
            });

            closeSidebarButton?.addEventListener('click', function () {
                body.classList.remove('sidebar-open');
            });

            document.querySelectorAll('.main_menu .dropdown-toggle').forEach((toggle) => {
                toggle.addEventListener('click', function (event) {
                    if (window.innerWidth >= collapsedMenuBreakpoint || !mainMenu?.classList.contains('is-open')) {
                        return;
                    }

                    event.preventDefault();
                    event.stopPropagation();
                    toggle.parentElement?.classList.toggle('open');
                });
            });

            document.addEventListener('click', function (event) {
                const target = event.target;
                if (!(target instanceof Element)) {
                    return;
                }

                if (window.innerWidth < sidebarBreakpoint && body.classList.contains('sidebar-open') && sidebar && !target.closest('.sidebar') && !target.closest('.btn-search')) {
                    body.classList.remove('sidebar-open');
                }

                if (window.innerWidth < collapsedMenuBreakpoint && mainMenu?.classList.contains('is-open') && !target.closest('.main_menu') && !target.closest('.btn-main-menu')) {
                    mainMenu.classList.remove('is-open');
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeMobileChrome();
                }
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth >= sidebarBreakpoint) {
                    body.classList.remove('sidebar-open');
                }

                if (window.innerWidth >= collapsedMenuBreakpoint) {
                    mainMenu?.classList.remove('is-open');
                    document.querySelectorAll('.main_menu .dropdown.open').forEach((item) => {
                        item.classList.remove('open');
                    });
                }
            });

            document.querySelectorAll('[data-favorite-button]').forEach((button) => {
                button.addEventListener('click', function () {
                    const payload = button.getAttribute('data-favorite-item');
                    if (!payload) {
                        return;
                    }

                    try {
                        window.RealtyFavorites.toggle(JSON.parse(payload), button);
                    } catch (error) {
                        console.error('Favorite payload parse error', error);
                    }
                });
            });

            window.RealtyFavorites.refreshButtons();
        });
    </script>
    @stack('scripts')
</body>
</html>
