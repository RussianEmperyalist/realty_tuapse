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
        }

        .home-directions__banner {
            background: #fff;
            border: 1px solid #e3ebf3;
            border-radius: 12px;
            padding: 28px 30px 32px;
            margin-top: 26px;
            box-shadow: 0 16px 32px rgba(20, 48, 69, 0.08);
            text-align: center;
            max-width: 100%;
            width: 100%;
            box-sizing: border-box;
            overflow-wrap: anywhere;
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
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .home-directions__banner-text {
            color: #333;
            font-size: 16px;
            line-height: 1.7;
            margin: 0;
            text-align: justify;
            word-break: break-word;
            overflow-wrap: anywhere;
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
            margin: 30px 0px;
            overflow: hidden;
        }

        .city-slick {
            overflow: hidden;
            position: relative;
            max-width: 100%;
        }

        .city-slick .slick-slide {
            transition: width .5s;
            float: left;
            padding: 0;
        }

        .city-slick .slick-list {
            margin: 0;
            padding: 0;
            max-width: 100%;
            overflow: hidden;
        }

        .city-slick .slick-track {
            transition: transform 1s;
        }

        .item-city {
            position: relative;
            width: 100%;
            max-width: 444px;
            height: auto;
            aspect-ratio: 444 / 400;
            overflow: hidden;
            box-sizing: border-box;
            margin: 0 auto;
        }

        .item-city.slick-center {
            max-width: 640px;
        }

        .item-city img {
            height: 100%;
            width: 100%;
            display: block;
        }

        .item-city.slick-center {
            overflow: hidden;
        }

        .item-city:after, .item_obj:after {
            display: inline-block;
            content: '';
            position: absolute;
            width: 100%;
            left: 0px;
            top: 0px;
            height: 100%;
            background: rgba(45, 66, 107, .4);
            -webkit-animation-duration: 1s;
            animation-duration: 1s;
            -webkit-animation-fill-mode: both;
            animation-fill-mode: both;
        }

        .item-city.slick-center:after {
            background: rgba(45, 66, 107, 0);
        }

        .item-city .text {
            position: absolute;
            width: 100%;
            color: #fff;
            bottom: 0px;
            left: 0px;
            padding: 30px;
        }

        .item-city .text a {
            color: #fff;
            font-size: 18px;
            text-decoration: none;
            border-bottom: 1px solid;
            font-weight: 400;
            text-shadow: 0px 0px 40px #000;
        }

        .item-city .text a:hover {
            border-color: transparent;
        }

        .item-city.slick-center .text {
            bottom: 30px;
            z-index: 9;
        }

        .item-city .h3 {
            font-size: 32px;
            font-weight: 900;
            margin: 0 0 10px;
        }

        .item-city.slick-center .h3 {
            font-size: 45px;
            text-shadow: 0px 0px 40px #000;
        }

        .city-slick .slick-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, .88);
            color: #143045;
            font-size: 22px;
            border: 0px;
            padding: 0;
            border-radius: 50%;
            z-index: 20;
            cursor: pointer;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(0,0,0,.3);
            transition: background .2s, transform .15s;
        }

        .city-slick .slick-arrow:hover {
            background: #fff;
            transform: translateY(-50%) scale(1.07);
        }

        .city-slick .slick-prev {
            left: 12px;
        }

        .city-slick .slick-next {
            right: 12px;
        }

        .home-direction-card .inactive-obj-type-url { opacity: 0.72; }
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

        /* Слайдер направлений — защита от обрезки на всех экранах */
        @media (max-width: 1280px) {
            .city-slick .slick-arrow {
                display: none !important;
            }

            .item-city {
                max-width: 330px;
            }

            .item-city.slick-center {
                max-width: 500px;
            }
        }

        @media (max-width: 767px) {
            .item-city {
                max-width: 100%;
                aspect-ratio: 16 / 9;
            }

            .item-city .text {
                padding: 12px;
            }

            .item-city .text a {
                font-size: 15px;
            }

            .item-city .h3 {
                font-size: 22px;
            }

            .home-directions__banner {
                padding: 18px 16px 20px;
            }

            .home-directions__banner-title {
                font-size: 22px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        jQuery(document).ready(function ($) {
            var $slider = $('.city-slick');
            if (!$slider.length) return;

            $slider.slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                dots: false,
                arrows: true,
                infinite: true,
                adaptiveHeight: false,
                variableWidth: true,
                centerMode: false,
                centerPadding: '0',
                prevArrow: '<button type="button" class="slick-prev slick-arrow"><i class="fas fa-chevron-left"></i></button>',
                nextArrow: '<button type="button" class="slick-next slick-arrow"><i class="fas fa-chevron-right"></i></button>',
                responsive: [
                    {
                        breakpoint: 1280,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1,
                            centerMode: true,
                            centerPadding: '20px',
                            arrows: false
                        }
                    },
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            centerMode: false,
                            centerPadding: '0',
                            arrows: false
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            centerMode: false,
                            centerPadding: '0',
                            arrows: false
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
