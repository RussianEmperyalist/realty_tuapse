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

        .city_module {
            position: relative;
            width: 100%;
            margin: 30px 0px;
        }

        .city-slick {
            overflow: hidden;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .city-slick .slick-track {
            transition: transform 1s;
        }

        .city-slick .slick-slide {
            transition: width .5s;
        }

        .item-city {
            position: relative;
            width: 444px;
            height: 400px;
            overflow: hidden;
        }

        .item-city.slick-center {
            width: 640px;
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

        .city-slick .slick-arrow, .city-object-slick .slick-arrow {
            position: absolute;
            top: 50%;
            background: rgba(255, 255, 255, .8);
            color: #4f6288;
            font-size: 20px;
            border: 0px;
            padding: 10px 14px;
            border-radius: 5px;
            z-index: 9;
            cursor: pointer;
        }

        .city-slick .slick-arrow:hover {
            background: rgba(255, 255, 255, 1);
        }

        .city-slick .slick-arrow.slick-next {
            right: 20px;
        }

        .city-slick .slick-arrow.slick-prev {
            left: 20px;
        }

        .home-direction-card .inactive-obj-type-url { opacity: 0.72; }
        .home-direction-card .list-inline { margin-bottom: 0; line-height: 1.55; }

        .home-directions .slick-dots { display: none !important; }
        .home-directions #result_city,
        .home-directions .city-object-slick { display: none !important; }

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
            var $slider = document.querySelector('.city-slick');
            if (!$slider) return;

            $slider.slick({
                centerMode: true,
                centerPadding: '0',
                slidesToShow: 3,
                slidesToScroll: 1,
                dots: false,
                arrows: true,
                infinite: false,
                adaptiveHeight: false,
                responsive: [
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 1,
                            centerMode: false,
                            centerPadding: '0'
                        }
                    }
                ]
            });

            $slider.css('opacity', 1);
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
