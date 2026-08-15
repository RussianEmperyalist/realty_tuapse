@extends('layouts.site')

@section('title', 'Добро пожаловать!')

@push('styles')
    <style>
        .home-news-card,
        .home-employee-card {
            height: 100%;
            border-radius: 14px;
            background: #fff;
            border: 1px solid #e3ebf3;
            box-shadow: 0 16px 32px rgba(20, 48, 69, 0.08);
            overflow: hidden;
        }

        .home-directions {
            margin-bottom: 28px;
        }

        .home-directions__banner {
            background: #fff;
            border: 1px solid #e3ebf3;
            border-radius: 12px;
            padding: 28px 30px 32px;
            margin-top: 26px;
            box-shadow: 0 16px 32px rgba(20, 48, 69, 0.08);
            text-align: center;
        }

        .home-directions__banner-title {
            color: #ff0000;
            font-size: 30px;
            font-weight: 400;
            text-transform: uppercase;
            text-align: center;
            margin: 0 0 20px;
            font-family: 'Bancodi', sans-serif;
            letter-spacing: 0.05em;
            line-height: 1.25;
        }

        .home-directions__banner-text {
            color: #333;
            font-size: 16px;
            line-height: 1.7;
            margin: 0;
            text-align: justify;
        }

        .home-directions > .h3 {
            margin-top: 0;
            margin-bottom: 18px;
            color: #4c78ab;
            text-align: center;
            text-transform: uppercase;
            font-weight: 700;
            font-size: clamp(20px, 2.4vw, 26px);
        }

        .home-city-slider {
            position: relative;
        }

        .home-city-track {
            display: block;
        }

        .home-city-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
            overflow: hidden;
        }

        .home-direction-card {
            position: relative;
            width: 100% !important;
            height: 320px;
            min-height: 320px;
            overflow: hidden;
            border-radius: 12px;
            background: #0f2233;
            transition: filter .45s ease;
        }

        .home-direction-card::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(5, 18, 31, 0.05), rgba(5, 18, 31, 0.55));
            pointer-events: none;
        }

        .home-direction-card img {
            width: 100%;
            height: 100%;
            min-height: 320px;
            object-fit: cover;
            object-position: center center;
            display: block;
            transition: transform 2s ease;
        }

        .home-direction-card.is-center img {
            transform: scale(1.06);
        }

        .home-direction-card:hover img {
            transform: scale(1.12);
        }

        .home-direction-card.is-center {
            border: 3px solid #fff;
            box-shadow: 0 18px 42px rgba(20, 48, 69, 0.35);
        }

        .home-direction-card.is-side {
            filter: grayscale(60%) brightness(0.75);
            transform: scale(0.94);
        }

        .home-direction-card.is-side .text {
            opacity: 0.85;
        }

        .home-direction-card.is-side .text a {
            pointer-events: none;
        }

        .home-direction-card .text {
            position: absolute;
            left: 22px;
            right: 22px;
            bottom: 28px;
            z-index: 2;
            color: #fff;
            transition: opacity .35s ease;
        }

        .home-direction-card .h3 {
            font-size: 24px;
            line-height: 1.2;
            font-weight: 900;
            margin-top: 0;
            margin-bottom: 10px;
            text-shadow: 0 2px 14px rgba(0, 0, 0, 0.55);
        }

        .home-direction-card a {
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            text-decoration: underline;
            text-decoration-color: rgba(255, 255, 255, 0.55);
            text-underline-offset: 3px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.45);
        }

        .home-direction-card .h3 a { font-size: inherit; text-decoration: none; text-shadow: inherit; }
        .home-direction-card .inactive-obj-type-url { opacity: 0.72; }
        .home-direction-card .list-inline { margin-bottom: 0; line-height: 1.55; }

        .home-city-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border: none;
            border-radius: 50%;
            background: rgba(255,255,255,.88);
            color: #172033;
            font-size: 22px;
            line-height: 1;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(0,0,0,.3);
            transition: background .2s, transform .15s;
        }

        .home-city-arrow:hover { background: #fff; transform: translateY(-50%) scale(1.07); }
        .home-city-arrow--prev { left: 12px; }
        .home-city-arrow--next { right: 12px; }

        .home-directions .slick-arrow,
        .home-directions .slick-dots,
        .home-directions #result_city,
        .home-directions .city-object-slick {
            display: none !important;
        }

        .home-news-card__body,
        .home-employee-card__body {
            padding: 18px 18px 20px;
        }

        .home-news-card__image,
        .home-employee-card__image {
            display: block;
            width: 100%;
            object-fit: cover;
        }

        .home-news-card__image {
            height: 220px;
        }

        .home-employee-card__image {
            height: 240px;
        }

        .home-news-grid,
        .home-employee-grid {
            display: grid;
            grid-template-columns: 1fr;
            align-items: start;
            gap: 18px;
        }

        .home-news-grid__item,
        .home-employee-grid__item {
            display: block;
            float: none !important;
            margin-bottom: 0;
            width: auto !important;
            padding: 0;
            min-width: 0;
        }

        @media (min-width: 768px) {
            .home-city-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .home-direction-card {
                height: 380px;
                min-height: 380px;
            }

            .home-direction-card img {
                min-height: 380px;
            }

            .home-direction-card .h3 {
                font-size: 28px;
            }

            .home-news-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .home-employee-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 1280px) {
            .home-news-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .home-employee-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var slider = document.querySelector('.home-city-slider');
            if (!slider) return;
            var cards = Array.from(slider.querySelectorAll('.home-direction-card'));
            var total = cards.length;
            if (total < 2) return;

            var current = 1; // стартуем с центральной

            function apply() {
                cards.forEach(function (card, i) {
                    card.classList.remove('is-center', 'is-side');
                    card.classList.add(i === current ? 'is-center' : 'is-side');
                });
            }

            apply();

            cards.forEach(function (card, i) {
                card.addEventListener('click', function (event) {
                    if (event.target.closest('a')) return;
                    if (i !== current) {
                        current = i;
                        apply();
                    }
                });
            });

            slider.querySelector('.home-city-arrow--prev').addEventListener('click', function () {
                current = (current - 1 + total) % total;
                apply();
            });

            slider.querySelector('.home-city-arrow--next').addEventListener('click', function () {
                current = (current + 1) % total;
                apply();
            });
        });
    </script>
@endpush

@section('content')
    <div class="content main_content">
        <div class="city_module home-directions">
            <div class="h3 fint l_fint">Популярные направления</div>
            <div class="home-city-slider">
                <button class="home-city-arrow home-city-arrow--prev" type="button" aria-label="Назад">&lsaquo;</button>
                <button class="home-city-arrow home-city-arrow--next" type="button" aria-label="Вперёд">&rsaquo;</button>
                <div class="home-city-track">
                    <div class="home-city-grid">
                        @foreach ($directionCards as $directionCard)
                            <div class="item-city home-direction-card">
                                <img src="{{ \App\Support\MediaPath::url($directionCard['image'], 'legacy/themes/dolphin/assets/images/no_photo_entry.png') }}" alt="{{ $directionCard['title'] }}">
                                <div class="text" data-id="{{ $directionCard['legacy_city_id'] }}">
                                    <div class="h3"><a href="{{ $directionCard['url'] }}">{{ $directionCard['title'] }}</a></div>
                                    <ul class="list-inline">
                                        @foreach ($directionCard['types'] as $type)
                                            <li>
                                                <a class="{{ $type['is_active'] ? 'active-obj-type-url' : 'inactive-obj-type-url' }}" href="{{ $type['url'] }}">
                                                    {{ $type['label'] }}
                                                    @if ($type['count'] > 0)
                                                        <span class="obj-type-count">({{ $type['count'] }})</span>
                                                    @endif
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="home-directions__banner">
                <div class="home-directions__banner-title">Если счастье, то надолго, если недвижимость, то на берегу Чёрного моря!</div>
                <div class="home-directions__banner-text">С апреля 1997 года мы успешно работаем на рынке недвижимости города Туапсе и Туапсинского района. За это время мы накопили обширный опыт в сфере любых операций с объектами самых разных категорий, в том числе элитной курортной недвижимости, земельных участков, производственных помещений. Нас выгодно отличает от конкурентов оперативность и широкая форма наших услуг. Оперативность в работе, умение понять индивидуальные запросы клиента, чистота и конфиденциальность сделок – всё это привлекает в наше агентство как жителей Черноморского побережья Краснодарского края, так и клиентов из других регионов нашей страны. В нашем офисе Вас всегда ждёт тёплый приём и радушная атмосфера. Своей главной задачей мы считаем помощь клиенту в удовлетворении его потребностей и желаний. Соблюдение интересов клиента – это основной принцип нашей работы. Для всех желающих получить бесплатную консультацию наш офис открыт ежедневно.</div>
            </div>
        </div>

        <div class="content_center">
            <div class="box">
                <div class="h3 fint l_fint">Актуальные объявления</div>
                <div class="catalog">
                    <div class="row">
                        @forelse ($featuredProperties as $property)
                            @include('partials.property-card', ['property' => $property])
                        @empty
                            <div class="col-md-12">
                                <p>Каталог наполняется. Скоро здесь появятся объекты из локальной копии сайта.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="box" style="margin-top: 30px;">
                <div class="h3 fint l_fint">Наши сотрудники</div>
                <div class="home-employee-grid">
                    @foreach ($employees as $employee)
                        <div class="home-employee-grid__item">
                            <div class="home-employee-card text-center">
                                <a href="{{ route('employees.show', ['id' => $employee->legacy_id]) }}">
                                    <img class="home-employee-card__image" src="{{ asset($employee->photo_path) }}" alt="{{ $employee->full_name }}">
                                </a>
                                <div class="home-employee-card__body">
                                    <h4 style="margin-top: 0;">{{ $employee->full_name }}</h4>
                                    <p>{{ $employee->position }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="box" style="margin-top: 30px;">
                <div class="h3 fint l_fint">Новости</div>
                <div class="home-news-grid">
                    @foreach ($latestNews as $newsPost)
                        <div class="home-news-grid__item">
                            <div class="home-news-card">
                                @if ($newsPost->image_path)
                                    <a href="{{ route('news.show', $newsPost->slug) }}">
                                        <img class="home-news-card__image" src="{{ asset($newsPost->image_path) }}" alt="{{ $newsPost->title }}">
                                    </a>
                                @endif
                                <div class="home-news-card__body">
                                    <h4><a href="{{ route('news.show', $newsPost->slug) }}">{{ $newsPost->title }}</a></h4>
                                    <p>{{ $newsPost->excerpt }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
