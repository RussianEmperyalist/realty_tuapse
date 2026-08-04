<div class="rt-header-brand">
    <a href="{{ route('home') }}">
        <span class="rt-header-brand__logo">
            <img src="{{ asset('legacy/themes/dolphin/assets/images/logo.png') }}" alt="{{ config('realty.company_display_name') }}">
        </span>
        <span class="rt-header-brand__text">{{ config('realty.company_display_name') }}</span>
    </a>
</div>

<div class="wrapper_top_link">
    <div class="row hidden-xs">
        <div class="top_link_bl top_link_bl1">
            <ul class="list-inline">
                <li class="personal_cabinet personal_cabinet__header">
                    <div class="dropdown">
                        <button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenuButton2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user-cog"></i> Личный кабинет <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" id="main_menu_nav_2">
                            @auth
                                <li class="dropdown-item"><a class="slow" href="{{ route('admin.dashboard') }}">Перейти в админку</a></li>
                                <li class="dropdown-item">
                                    <form method="post" action="{{ route('logout') }}" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn btn-link" style="padding: 0 15px; color: inherit;">Выйти</button>
                                    </form>
                                </li>
                            @else
                                <li class="dropdown-item"><a class="slow" href="{{ route('login') }}">Войти</a></li>
                                <li class="dropdown-item"><a class="slow" href="{{ route('register') }}">Зарегистрироваться</a></li>
                                <li class="dropdown-item"><a class="slow" href="{{ route('recover') }}">Забыли пароль?</a></li>
                            @endauth
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
        <div class="top_menu top_link_bl">
            <ul class="nav navbar-nav" id="main_menu_nav_1">
                <li class="depth_zero depth_item_numb_0"><a class="slow" href="{{ route('contact.form') }}">Связаться с нами</a></li>
                <li class="depth_zero depth_item_numb_1"><a class="slow" href="{{ route('booking.form') }}">Оставить заявку</a></li>
            </ul>
        </div>
    </div>
    <div class="clear"></div>
</div>

<div class="wrapper_main_menu">
    <div class="main_menu">
        <div class="mobile-button">
            <ul class="list-inline">
                <li><button class="btn btn-primary btn-main-menu"><i class="fas fa-bars"></i><span>Меню</span></button></li>
                <li><button class="btn btn-default btn-search"><i class="fas fa-search"></i><span>Поиск</span></button></li>
            </ul>
        </div>
        <ul class="nav nav-pills" id="main_menu_nav_3">
            <li class="dropdown">
                <a class="slow dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" href="javascript: void(0);">Поиск<span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a class="slow" href="{{ route('pages.show', 'poisk-na-karte') }}">Поиск на карте</a></li>
                    <li><a class="slow" href="{{ route('pages.show', 'prodazha') }}">Продажа</a></li>
                    <li><a class="slow" href="{{ route('pages.show', 'novostrojki') }}">Новостройки</a></li>
                    <li><a class="slow" href="{{ route('pages.show', 'arenda') }}">Аренда</a></li>
                    <li><a class="slow" href="{{ route('pages.show', 'gostinicy') }}">Гостиницы</a></li>
                </ul>
            </li>
            <li class="depth_zero depth_item_numb_1"><a class="slow" href="{{ route('review') }}">Отзывы</a></li>
            <li class="depth_zero depth_item_numb_2"><a class="slow" href="{{ route('contacts') }}">Контакты</a></li>
            <li class="depth_zero depth_item_numb_3"><a class="slow" href="{{ route('news.index') }}">Новости</a></li>
            <li class="depth_zero depth_item_numb_4"><a class="slow" href="{{ route('favorites') }}">Избранное</a></li>
            <li class="dropdown">
                <a class="slow dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" href="javascript: void(0);">Информация<span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a class="slow" href="{{ route('gallery.index') }}">Фотогалерея</a></li>
                    <li><a class="slow" href="{{ route('news.index') }}">Новости</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a class="slow dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" href="javascript: void(0);">Дополнительно<span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a class="slow" href="{{ route('pages.show', 'politika-konfidencialnosti') }}">Политика конфиденциальности</a></li>
                    <li><a class="slow" href="{{ route('pages.show', 'polzovatelskoe-soglashenie') }}">Пользовательское соглашение</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
