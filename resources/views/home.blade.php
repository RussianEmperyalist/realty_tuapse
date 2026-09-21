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
            max-width: 100%;
            width: 100%;
            box-sizing: border-box;
        }

        .home-directions {
            margin-bottom: 28px;
            max-width: 100%;
            width: 100%;
            min-width: 0;
        }

        .home-directions__banner {
            background: #fff;
            border: 1px solid #e3ebf3;
            border-radius: 12px;
            padding: clamp(16px, 2.4vw, 32px);
            margin-top: 26px;
            box-shadow: 0 16px 32px rgba(20, 48, 69, 0.08);
            text-align: center;
            max-width: 100%;
            width: 100%;
            box-sizing: border-box;
            overflow: visible;
        }

        .home-directions__banner-title {
            color: #ff0000;
            font-size: clamp(18px, 2.1vw, 28px);
            font-weight: 400;
            text-transform: uppercase;
            text-align: center;
            margin: 0 0 20px;
            font-family: 'Bancodi', sans-serif;
            letter-spacing: 0.04em;
            line-height: 1.3;
            word-break: normal;
            overflow-wrap: break-word;
            max-width: 100%;
        }

        .home-directions__banner-text {
            color: #333;
            font-size: clamp(14px, 1.15vw, 16px);
            line-height: 1.7;
            margin: 0 auto;
            text-align: justify;
            word-break: normal;
            overflow-wrap: break-word;
            max-width: 100%;
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

        .city_module {
            position: relative;
            width: 100%;
            max-width: 100%;
            min-width: 0;
            margin: 30px 0;
            overflow: visible;
        }

        .city-slick-wrap {
            position: relative;
            width: 100%;
            max-width: 100%;
            min-width: 0;
            overflow: visible;
            padding: 0 52px;
        }

        .home-directions .city-slick {
            display: block;
            position: relative;
            width: 100%;
            max-width: 100%;
            min-width: 0;
            overflow: visible;
            box-sizing: border-box;
        }

        .home-directions .city-slick .slick-list {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 12px 0 16px;
            overflow: hidden;
            box-sizing: border-box;
        }

        .home-directions .city-slick .slick-slide {
            height: auto;
            padding: 0 8px;
            box-sizing: border-box;
            min-width: 0;
        }

        .home-directions .city-slick__slide {
            min-width: 0;
            max-width: 100%;
        }

        .home-directions .item-city {
            position: relative;
            width: 100%;
            max-width: 100%;
            height: auto;
            aspect-ratio: 16 / 11;
            overflow: hidden;
            box-sizing: border-box;
            margin: 0;
            border-radius: 12px;
            filter: brightness(0.5);
            opacity: 0.85;
            transition: filter 600ms cubic-bezier(0.4, 0, 0.2, 1), opacity 600ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        .home-directions .item-city img {
            height: 100%;
            width: 100%;
            max-width: none;
            display: block;
            object-fit: cover;
        }

        .home-directions .slick-center .item-city,
        .home-directions .item-city.slick-center {
            filter: brightness(1);
            opacity: 1;
            z-index: 5;
        }

        .home-directions .item-city .text {
            position: absolute;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            color: #fff;
            bottom: 0;
            left: 0;
            padding: clamp(12px, 2vw, 28px);
            z-index: 3;
            opacity: 0.82;
            transition: opacity 600ms cubic-bezier(0.4, 0, 0.2, 1), bottom 600ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        .home-directions .item-city .text a {
            color: #fff;
            font-size: clamp(13px, 1.15vw, 18px);
            text-decoration: none;
            border-bottom: 1px solid;
            font-weight: 400;
            text-shadow: 0 0 40px #000;
            overflow-wrap: break-word;
        }

        .home-directions .item-city .text a:hover {
            border-color: transparent;
        }

        .home-directions .slick-center .item-city .text,
        .home-directions .item-city.slick-center .text {
            bottom: 10px;
            opacity: 1;
        }

        .home-directions .item-city .h3 {
            font-size: clamp(20px, 2.2vw, 32px);
            font-weight: 900;
            margin: 0 0 10px;
            overflow-wrap: break-word;
            transition: font-size 600ms cubic-bezier(0.4, 0, 0.2, 1), text-shadow 600ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        .home-directions .slick-center .item-city .h3,
        .home-directions .item-city.slick-center .h3 {
            font-size: clamp(22px, 2.8vw, 40px);
            text-shadow: 0 0 40px #000;
        }

        .city-slick-wrap > .slick-arrow,
        .home-directions .city-slick .slick-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.94);
            color: #143045;
            font-size: 20px;
            line-height: 1;
            border: 0;
            padding: 0;
            border-radius: 50%;
            z-index: 50;
            cursor: pointer;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            visibility: visible;
            opacity: 1;
            box-shadow: 0 4px 16px rgba(0,0,0,.28);
            transition: background 200ms, transform 150ms;
            pointer-events: auto;
        }

        .city-slick-wrap > .slick-arrow i,
        .home-directions .slick-arrow i {
            font-size: 18px;
            color: #143045;
            display: block;
        }

        .city-slick-wrap > .slick-arrow:hover,
        .home-directions .city-slick .slick-arrow:hover {
            background: #fff;
            transform: translateY(-50%) scale(1.06);
        }

        .city-slick-wrap > .slick-prev,
        .home-directions .city-slick .slick-prev {
            left: 10px;
        }

        .city-slick-wrap > .slick-next,
        .home-directions .city-slick .slick-next {
            right: 10px;
        }

        @media (max-width: 767px) {
            .home-directions .item-city {
                aspect-ratio: 16 / 10;
            }
            .home-directions .item-city .text {
                padding: 12px;
            }
            .city-slick-wrap {
                padding: 0 42px;
            }
            .city-slick-wrap > .slick-arrow,
            .home-directions .city-slick .slick-arrow {
                width: 36px;
                height: 36px;
                font-size: 16px;
            }
            .city-slick-wrap > .slick-arrow i,
            .home-directions .slick-arrow i {
                font-size: 14px;
            }
        }

        .home-directions .item-city .list-inline {
            margin: 0;
            max-width: 100%;
        }

        .home-directions .item-city .list-inline > li {
            white-space: normal;
        }
        .home-direction-card .list-inline { margin-bottom: 0; line-height: 1.55; }

        .home-directions .slick-dots { display: none !important; }
        .home-directions #result_city,
        .home-directions .city-object-slick { display: none !important; }

        .home-news-card__body,
        .home-employee-card__body {
            padding: 18px 18px 20px;
            min-width: 0;
            overflow: hidden;
        }

        .home-news-card__body h4,
        .home-employee-card__body h4,
        .home-news-card__body p,
        .home-employee-card__body p {
            word-break: break-word;
            overflow-wrap: anywhere;
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
            width: 100%;
            max-width: 100%;
            min-width: 0;
        }

        .home-news-grid__item,
        .home-employee-grid__item {
            display: block;
            float: none !important;
            margin-bottom: 0;
            width: 100% !important;
            max-width: 100%;
            padding: 0;
            min-width: 0;
        }

        @media (min-width: 768px) {
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

        @media (max-width: 767px) {
            .home-news-grid {
                grid-template-columns: 1fr;
            }
            .home-employee-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        jQuery(document).ready(function ($) {
            var $slider = $('.home-directions .city-slick');
            if (!$slider.length) return;
            var $wrap = $slider.closest('.city-slick-wrap');

            $slider.slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                dots: false,
                arrows: true,
                infinite: true,
                adaptiveHeight: false,
                speed: 600,
                cssEase: 'cubic-bezier(0.4, 0, 0.2, 1)',
                variableWidth: false,
                centerMode: true,
                centerPadding: '27.6%',
                waitForAnimate: true,
                focusOnSelect: true,
                appendArrows: $wrap.length ? $wrap : $slider,
                prevArrow: '<button type="button" class="slick-prev slick-arrow" aria-label="Назад"><i class="fas fa-chevron-left"></i></button>',
                nextArrow: '<button type="button" class="slick-next slick-arrow" aria-label="Вперёд"><i class="fas fa-chevron-right"></i></button>',
                responsive: [
                    {
                        breakpoint: 1440,
                        settings: {
                            centerPadding: '27.6%'
                        }
                    },
                    {
                        breakpoint: 1280,
                        settings: {
                            centerPadding: '27%'
                        }
                    },
                    {
                        breakpoint: 1024,
                        settings: {
                            centerPadding: '24%'
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            centerPadding: '20%'
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            centerPadding: '36px'
                        }
                    }
                ]
            });
        });
    </script>
@endpush

@section('content')
    <div class="content main_content">
        <div class="city_module home-directions">
            <div class="h3 fint l_fint">Популярные направления</div>
            <div class="city-slick-wrap">
            <div class="city-slick">
                @foreach ($directionCards as $directionCard)
                    <div class="item-city">
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
            <div class="home-directions__banner">
                <div class="home-directions__banner-title">Если счастье, то надолго, если недвижимость, то на берегу Чёрного моря!</div>
                <div class="home-directions__banner-text">С апреля 1997 года мы успешно работаем на рынке недвижимости города Туапсе и Туапсинского района. За это время мы накопили обширный опыт в сфере любых операций с объектами самых разных категорий, в том числе элитной курортной недвижимости, земельных участков, производственных помещений. Нас выгодно отличает от конкурентов оперативность и широкая форма наших услуг. Оперативность в работе, умение понять индивидуальные запросы клиента, чистота и конфиденциальность сделок – всё это привлекает в наше агентство как жителей Черноморского побережья Краснодарского края, так и клиентов из других регионов нашей страны. В нашем офисе Вас всегда ждёт тёплый приём и радушная атмосфера. Своей главной задачей мы считаем помощь клиенту в удовлетворении его потребностей и желаний. Соблюдение интересов клиента – это основной принцип нашей работы. Для всех желающих получить бесплатную консультацию наш офис открыт для Вас!</div>
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
                                    <img class="home-employee-card__image" src="{{ \App\Support\MediaPath::url($employee->photo_path, 'legacy/themes/dolphin/assets/images/no_photo_entry.png') }}" alt="{{ $employee->full_name }}">
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
                                        <img class="home-news-card__image" src="{{ \App\Support\MediaPath::url($newsPost->image_path, 'legacy/themes/dolphin/assets/images/no_photo_entry.png') }}" alt="{{ $newsPost->title }}">
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
