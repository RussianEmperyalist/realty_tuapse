<div class="footer">
    <div class="rt-footer-grid">
        <section class="rt-footer-panel rt-footer-panel--about">
            <div class="h3 fint">О нас</div>
            <div class="rt-footer-about-grid">
                <div class="rt-footer-brand">
                    <div class="logo">
                        <a href="{{ route('home') }}">
                            <div class="logo-img">
                                <img src="{{ asset('legacy/themes/dolphin/assets/images/logo.png') }}" alt="{{ config('realty.company_display_name') }}">
                            </div>
                            <div class="logo-text">{{ config('realty.company_display_name') }}</div>
                        </a>
                    </div>
                </div>
                <div class="rt-footer-contacts-wrap">
                    <ul class="list-unstyled footer-contacts">
                        <li><i class="fas fa-envelope"></i> <a href="mailto:{{ config('realty.contact_email') }}">{{ config('realty.contact_email') }}</a></li>
                    </ul>
                    <ul class="list-unstyled footer-phones">
                        @foreach (config('realty.phones') as $phone)
                            <li><a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}"><i class="fas fa-phone"></i> {{ $phone }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="rt-footer-about-text">
                    <p>{{ \App\Models\SiteSetting::query()->where('key', 'about_company')->value('value') }}</p>
                </div>
            </div>
        </section>

        <section class="rt-footer-panel rt-footer-panel--links">
            <div class="h3 fint">Полезная информация</div>
            <ul class="list-unstyled footer-links" id="main_menu_nav">
                <li><a class="slow" href="{{ route('booking.form') }}">Оставить заявку</a></li>
                <li><a class="slow" href="{{ route('review') }}">Отзывы</a></li>
                <li><a class="slow" href="{{ route('news.index') }}">Новости</a></li>
                <li><a class="slow" href="{{ route('callback.form') }}">Заказать обратный звонок</a></li>
            </ul>
        </section>

        <section class="rt-footer-panel rt-footer-panel--actions">
            <div class="footer-actions">
                <a href="{{ route('booking.form') }}" class="slow btn footer-action footer-action--light">
                    <i class="far fa-paper-plane" aria-hidden="true"></i>
                    <span>Оставить заявку</span>
                </a>
                <a href="{{ route('contact.form') }}" class="slow btn footer-action footer-action--primary">
                    <i class="far fa-comments" aria-hidden="true"></i>
                    <span>Связаться с нами</span>
                </a>
                <a href="{{ route('login') }}" class="slow btn footer-action footer-action--light">
                    <i class="far fa-user" aria-hidden="true"></i>
                    <span>Личный кабинет</span>
                </a>
            </div>
        </section>
    </div>

    <div class="footer_bottom">
        <div class="row">
            <div class="col-md-6 col-sm-6">&copy; {{ config('realty.company_display_name') }}, 1997&mdash;{{ now()->year }}</div>
            <div class="col-md-6 col-sm-6 text-right"></div>
        </div>
    </div>
</div>
