@extends('layouts.site')

@php
    $galleryImages = $property->images->isNotEmpty() ? $property->images : collect([(object) [
        'path' => 'legacy/themes/dolphin/assets/images/no_photo_entry.png',
        'thumb_path' => 'legacy/themes/dolphin/assets/images/no_photo_entry.png',
        'alt' => $property->title,
    ]]);

    $primaryImage = $galleryImages->first();
    $favoritePayload = [
        'slug' => $property->slug,
        'title' => $property->title,
        'url' => route('properties.show', $property->slug),
        'image' => \App\Support\MediaPath::url(
            $primaryImage->path,
            'legacy/themes/dolphin/assets/images/no_photo_entry.png',
        ),
        'price' => $property->price_label ?? number_format((int) $property->price, 0, ',', ' ') . ' ' . $property->currency,
        'address' => $property->address,
        'lat' => $property->latitude,
        'lng' => $property->longitude,
    ];

    $ownerPhoto = \App\Support\MediaPath::url(
        $property->employee?->photo_path,
        'legacy/themes/dolphin/assets/images/no_photo_entry.png',
    );
    $primaryImageUrl = \App\Support\MediaPath::url(
        $primaryImage->path,
        'legacy/themes/dolphin/assets/images/no_photo_entry.png',
    );

    $mapsApiKey = config('services.yandex.maps_key');
    $hasCoords = $property->latitude && $property->longitude;
@endphp

@section('title', $property->title)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($property->description), 150))

@push('styles')
    <link rel="stylesheet" href="{{ asset('legacy/themes/dolphin/assets/js/slick/slick-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/themes/dolphin/assets/js/slick/my_slick.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/themes/dolphin/assets/js/photoswipe/photoswipe.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/themes/dolphin/assets/js/photoswipe/default-skin/default-skin.css') }}">
    <link rel="stylesheet" href="{{ asset('legacy/assets/372b502f/similarads.css') }}">
    <style>
        .property-page-grid {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(280px, 360px);
            gap: 30px;
            align-items: start;
        }

        .property-main,
        .property-sidebar,
        .full_property,
        .content_center.main-content-wrapper {
            min-width: 0;
        }

        .title_property {
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
            line-height: 1.2;
            margin-right: 0;
        }

        .property-gallery {
            margin-bottom: 30px;
            min-width: 0;
        }

        .property-gallery-main {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
            border: 1px solid #e9e9e9;
            min-height: 430px;
            overflow: hidden;
        }

        .property-gallery-main a {
            display: block;
            width: 100%;
        }

        .property-gallery-main img {
            display: block;
            width: 100%;
            max-height: 520px;
            object-fit: contain;
            margin: 0 auto;
        }

        .property-gallery-thumbs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
            gap: 12px;
            margin-top: 15px;
        }

        .property-gallery-thumb {
            border: 2px solid transparent;
            background: #fff;
            padding: 0;
            display: block;
            overflow: hidden;
            border-radius: 6px;
            transition: border-color .2s ease, transform .2s ease;
        }

        .property-gallery-thumb:hover,
        .property-gallery-thumb.is-active {
            border-color: #43b3dd;
            transform: translateY(-2px);
        }

        .property-gallery-thumb img {
            display: block;
            width: 100%;
            aspect-ratio: 4 / 3;
            object-fit: cover;
        }

        .property-sidebar .box {
            margin-bottom: 20px;
        }

        .property-owner-photo {
            width: 100%;
            max-width: 240px;
            aspect-ratio: 4 / 5;
            object-fit: cover;
            margin: 0 auto 16px;
            display: block;
            border-radius: 10px;
        }

        .property-owner-actions .btn {
            width: 100%;
        }

        .property-owner-actions li + li {
            margin-top: 10px;
        }

        .property-spec-table td:first-child {
            width: 36%;
        }

        .property-spec-table td {
            overflow-wrap: anywhere;
        }

        .similar_block .catalog .item {
            height: auto;
            min-height: 0;
        }

        .similar_block .slinky-gallery img {
            width: 100%;
            aspect-ratio: 4 / 3;
            object-fit: cover;
        }

        #property-map {
            width: 100%;
            height: 380px;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #e3ebf3;
        }

        @media (max-width: 1199px) {
            .property-page-grid {
                grid-template-columns: 1fr;
            }

            .property-sidebar {
                display: grid;
                gap: 20px;
            }

            .property-gallery-main {
                min-height: 320px;
            }
        }

        @media (max-width: 991px) {
            .property-sidebar {
                display: block;
            }
        }

        @media (max-width: 767px) {
            .content.main_content.obj-view .content_center.main-content-wrapper.box {
                padding-left: 14px;
                padding-right: 14px;
            }

            .title_property {
                font-size: 34px;
            }

            .property-gallery-main {
                min-height: 240px;
            }

            .property-gallery-thumbs {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .property-spec-table td:first-child {
                width: auto;
            }

            .property-main .table-responsive {
                overflow-x: visible;
                border: 0;
            }

            .property-spec-table,
            .property-spec-table tbody,
            .property-spec-table tr,
            .property-spec-table td {
                display: block;
                width: 100% !important;
            }

            .property-spec-table tr {
                padding: 12px 0;
                border-bottom: 1px solid #e5e7eb;
            }

            .property-spec-table td {
                border: 0 !important;
                padding: 0 8px 8px;
            }

            .property-spec-table td:last-child {
                padding-bottom: 0;
            }

            .property-sidebar {
                overflow: hidden;
            }

            .property-owner-photo {
                max-width: 200px;
            }

            #property-map {
                height: 280px;
            }
        }

        @media (max-width: 479px) {
            .title_property {
                font-size: 28px;
            }

            .property-gallery-main {
                min-height: 210px;
            }
        }

        /* --- Lightbox --- */
        .rt-property-lightbox {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(10, 15, 25, 0.9);
        }

        .rt-property-lightbox.is-open {
            display: flex;
        }

        .rt-property-lightbox-open {
            overflow: hidden !important;
        }

        .rt-property-lightbox__img {
            max-width: 94vw;
            max-height: 90vh;
            object-fit: contain;
            border-radius: 4px;
            box-shadow: 0 10px 60px rgba(0, 0, 0, 0.5);
        }

        .rt-property-lightbox__btn {
            position: absolute;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border: none;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            color: #172033;
            font-size: 28px;
            line-height: 1;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
            transition: background .2s ease, transform .15s ease;
        }

        .rt-property-lightbox__btn:hover,
        .rt-property-lightbox__btn:focus {
            background: #fff;
            transform: scale(1.05);
        }

        .rt-property-lightbox__btn:disabled {
            opacity: 0.25;
            cursor: default;
            transform: none;
        }

        .rt-property-lightbox__btn--close {
            top: 14px;
            right: 14px;
            font-size: 32px;
        }

        .rt-property-lightbox__btn--prev {
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
        }

        .rt-property-lightbox__btn--prev:hover,
        .rt-property-lightbox__btn--prev:focus {
            transform: translateY(-50%) scale(1.05);
        }

        .rt-property-lightbox__btn--next {
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
        }

        .rt-property-lightbox__btn--next:hover,
        .rt-property-lightbox__btn--next:focus {
            transform: translateY(-50%) scale(1.05);
        }

        .rt-property-lightbox__counter {
            position: absolute;
            bottom: 16px;
            left: 50%;
            transform: translateX(-50%);
            color: rgba(255, 255, 255, 0.85);
            font-size: 14px;
            font-weight: 600;
            background: rgba(0, 0, 0, 0.45);
            padding: 6px 14px;
            border-radius: 999px;
            pointer-events: none;
        }

        .rt-property-lightbox__caption {
            position: absolute;
            top: 16px;
            left: 50%;
            transform: translateX(-50%);
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            max-width: 60vw;
            text-align: center;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6);
            pointer-events: none;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media (max-width: 767px) {
            .rt-property-lightbox {
                padding: 10px;
            }

            .rt-property-lightbox__btn {
                width: 40px;
                height: 40px;
                font-size: 22px;
            }

            .rt-property-lightbox__btn--close {
                top: 8px;
                right: 8px;
            }

            .rt-property-lightbox__btn--prev {
                left: 8px;
            }

            .rt-property-lightbox__btn--next {
                right: 8px;
            }

            .rt-property-lightbox__caption {
                font-size: 13px;
                max-width: 80vw;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content main_content obj-view">
        <div class="content_center main-content-wrapper box">
            <div class="item-list">
                <div class="full_property">
                    <div class="property-page-grid">
                        <div class="property-main">
                            <div class="row">
                                <div class="title col-md-12">
                                    <h1 class="fint l_fint h3 title_property">{{ $property->title }}</h1>
                                </div>
                            </div>

                            <div class="property-gallery property__slider">
                                <div class="property-gallery-main">
                                    <a id="property-gallery-link" href="{{ $primaryImageUrl }}" title="Открыть фотографии" style="display:block;width:100%;cursor:zoom-in;">
                                        <img
                                            id="property-gallery-image"
                                            src="{{ $primaryImageUrl }}"
                                            alt="{{ $primaryImage->alt ?: $property->title }}"
                                        >
                                    </a>
                                </div>

                                @if ($galleryImages->count() > 1)
                                    <div class="property-gallery-thumbs">
                                        @foreach ($galleryImages as $image)
                                            <button
                                                type="button"
                                                class="property-gallery-thumb {{ $loop->first ? 'is-active' : '' }}"
                                                data-gallery-full="{{ \App\Support\MediaPath::url($image->path, 'legacy/themes/dolphin/assets/images/no_photo_entry.png') }}"
                                                data-gallery-alt="{{ $image->alt ?: $property->title }}"
                                                aria-label="Показать фото {{ $loop->iteration }}"
                                            >
                                                <img src="{{ \App\Support\MediaPath::url($image->thumb_path ?: $image->path, 'legacy/themes/dolphin/assets/images/no_photo_entry.png') }}" alt="{{ $image->alt ?: $property->title }}">
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="b_item_aux">
                                <div class="tabs tabs_1 resptabscont">
                                    <div class="tab_header">
                                        <ul class="nav nav-tabs object_tabs resp-tabs-list">
                                            <li class="active"><a class="slow" href="#tabs1_1">Основные</a></li>
                                        </ul>
                                    </div>
                                    <div class="tab-content resp-tabs-container">
                                        <div class="tabs1_1 tab_bl_1 tab-pane fade in active" id="tabs1_1">
                                            <br>
                                            <div class="property_info_row">
                                                <div class="table-responsive">
                                                    <table class="table main-table-general-tab property-spec-table">
                                                        <tbody>
                                                            <tr>
                                                                <td><strong>Уникальный номер объявления:</strong></td>
                                                                <td>{{ $property->legacy_id }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Тип сделки:</strong></td>
                                                                <td>{{ config('realty.deal_type_options.' . $property->deal_type, $property->deal_type) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Тип недвижимости:</strong></td>
                                                                <td>{{ config('realty.property_type_options.' . $property->property_type, $property->property_type) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Адрес:</strong></td>
                                                                <td>{{ $property->address }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Цена:</strong></td>
                                                                <td>
                                                                    <span class="price_row">
                                                                        <span>{{ $property->price_label ?? number_format((int) $property->price, 0, ',', ' ') }}</span>
                                                                        <span class="currency">{{ $property->currency }}</span>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Описание:</strong></td>
                                                                <td>{!! nl2br(e($property->description)) !!}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Количество комнат:</strong></td>
                                                                <td>{{ $property->rooms ?: '—' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Этаж:</strong></td>
                                                                <td>{{ $property->floor ?: '—' }} @if($property->floors_total) этаж {{ $property->floors_total }} этажного дома @endif</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Общая площадь:</strong></td>
                                                                <td>{{ $property->square ? number_format((float) $property->square, 1, ',', ' ') . ' м²' : '—' }}</td>
                                                            </tr>
                                                            @if ($property->windows)
                                                                <tr>
                                                                    <td><strong>Окна:</strong></td>
                                                                    <td>{{ $property->windows }}</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <aside class="property-sidebar">
                            <div class="properties_list box widget">
                                <div class="h3 fint l_fint">Общая информация</div>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-map-marker"></i> <strong>{{ config('realty.city_options.' . $property->city, $property->city) }}</strong></li>
                                    <li><strong>Общая площадь:</strong> {{ $property->square ? number_format((float) $property->square, 1, ',', ' ') . ' м²' : 'уточняется' }}</li>
                                    <li><strong>{{ config('realty.property_type_options.' . $property->property_type, $property->property_type) }}</strong>, {{ $property->rooms ?: '—' }} комнаты</li>
                                </ul>
                                <ul class="list-unstyled">
                                    <li>
                                        <i class="fas fa-building"></i>
                                        <strong>{{ config('realty.deal_type_options.' . $property->deal_type, $property->deal_type) }}</strong>
                                        —
                                        <span class="price">{{ $property->price_label ?? number_format((int) $property->price, 0, ',', ' ') }} {{ $property->currency }}</span>
                                    </li>
                                </ul>
                            </div>

                            @if ($property->employee)
                                <div class="box widget agent_info">
                                    <div class="h3 fint l_fint">Объявление опубликовал</div>
                                    <div class="text-center">
                                        <img class="message_ava property-owner-photo" src="{{ $ownerPhoto }}" alt="{{ $property->employee->full_name }}">
                                        <ul class="list-unstyled text-center">
                                            <li class="h4"><strong>{{ $property->employee->full_name }}</strong></li>
                                            <li class="li1"><a href="tel:{{ $property->phone_override ?: $property->employee->phone_primary }}">{{ $property->phone_override ?: $property->employee->phone_primary }}</a></li>
                                            <li class="li3"><a href="{{ route('employees.show', ['id' => $property->employee->legacy_id]) }}">Все объявления сотрудника</a></li>
                                        </ul>
                                    </div>
                                    <ul class="list-unstyled user_link text-center property-owner-actions">
                                        <li><a class="btn btn-primary" href="{{ route('property-message.form', ['id' => $property->legacy_id]) }}">Послать сообщение</a></li>
                                        <li>
                                            <button
                                                class="btn btn-default"
                                                type="button"
                                                data-favorite-button
                                                data-favorite-slug="{{ $property->slug }}"
                                                data-favorite-item='@json($favoritePayload)'
                                            >
                                                В избранное
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        </aside>
                    </div>
                </div>

                @if ($similarProperties->isNotEmpty())
                    <div class="similar_block padding-top-20">
                        <div class="h3 fint l_fint">Похожие объявления</div>
                        <div class="catalog">
                            <div class="row">
                                @foreach ($similarProperties as $similarProperty)
                                    @include('partials.property-card', ['property' => $similarProperty])
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Карта объекта --}}
                <div class="property-map" style="margin-top: 24px; margin-bottom: 24px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                        <div class="h3 fint l_fint" style="margin:0;">На карте</div>
                    </div>
                    <div id="property-map" style="width:100%;height:380px;border-radius:10px;overflow:hidden;border:1px solid #e3ebf3;"></div>
                </div>

                <div style="margin-bottom:8px;padding:10px;background:#fff3cd;border-radius:6px;font-size:13px;display:none;" id="map-debug-info">
                    <strong>Отладка карты:</strong> <span id="map-debug-text">проверка...</span>
                </div>

                <script src="https://api-maps.yandex.ru/2.1/?apikey=38c8767e-6b9b-452a-a56b-f7c8b927ea10&lang=ru_RU"></script>
                <script>
                    (function() {
                        var mapEl = document.getElementById('property-map');
                        var debugEl = document.getElementById('map-debug-info');
                        var debugText = document.getElementById('map-debug-text');

                        function dbg(msg) {
                            console.log('[MAP]', msg);
                            if (debugEl && debugText) {
                                debugEl.style.display = 'block';
                                debugText.textContent = msg;
                            }
                        }

                        if (!mapEl) {
                            dbg('Ошибка: контейнер #property-map не найден в DOM');
                            return;
                        }

                        dbg('Контейнер карты найден, ждём загрузки API...');

                        var checkReady = setInterval(function() {
                            if (typeof ymaps !== 'undefined') {
                                clearInterval(checkReady);
                                dbg('ymaps загружен, вызываем ymaps.ready()');

                                ymaps.ready(function() {
                                    dbg('ymaps.ready() сработал, создаём карту');

                                    var lat = {{ $property->latitude ?? 'null' }};
                                    var lng = {{ $property->longitude ?? 'null' }};
                                    var defaultCenter = [44.100, 39.080];
                                    var hasCoords = (lat !== null && lng !== null && !isNaN(lat) && !isNaN(lng));
                                    var center = hasCoords ? [parseFloat(lat), parseFloat(lng)] : defaultCenter;

                                    try {
                                        var propertyMap = new ymaps.Map('property-map', {
                                            center: center,
                                            zoom: hasCoords ? 16 : 12,
                                            controls: ['zoomControl', 'fullscreenControl']
                                        });
                                        dbg('Карта создана, центр: ' + center.join(', '));
                                    } catch (e) {
                                        dbg('Ошибка создания карты: ' + e.message);
                                        return;
                                    }

                                    if (hasCoords) {
                                        var placemark = new ymaps.Placemark([parseFloat(lat), parseFloat(lng)], {
                                            balloonContent: '{{ addslashes($property->title) }}'
                                        }, {
                                            preset: 'islands#redDotIcon'
                                        });
                                        propertyMap.geoObjects.add(placemark);
                                    }

                                    var resizeTimer;
                                    window.addEventListener('resize', function () {
                                        clearTimeout(resizeTimer);
                                        resizeTimer = setTimeout(function () {
                                            propertyMap.container.fitToViewport();
                                        }, 200);
                                    });

                                    // Скрываем отладку после успеха
                                    if (debugEl) debugEl.style.display = 'none';
                                });
                            }
                        }, 200);

                        setTimeout(function() {
                            clearInterval(checkReady);
                            dbg('Таймаут 10 сек — ymaps не загрузился. Проверьте API-ключ и подключение к интернету.');
                        }, 10000);
                    })();
                </script>
            </div>
        </div>
    </div>

    {{-- Lightbox --}}
    <div id="property-lightbox" class="rt-property-lightbox" role="dialog" aria-modal="true" aria-label="Просмотр фотографий">
        <button id="property-lightbox-close" class="rt-property-lightbox__btn rt-property-lightbox__btn--close" aria-label="Закрыть">&times;</button>
        <button id="property-lightbox-prev" class="rt-property-lightbox__btn rt-property-lightbox__btn--prev" aria-label="Предыдущее фото">&lsaquo;</button>
        <img id="property-lightbox-img" class="rt-property-lightbox__img" src="" alt="">
        <div id="property-lightbox-caption" class="rt-property-lightbox__caption"></div>
        <div id="property-lightbox-counter" class="rt-property-lightbox__counter"></div>
        <button id="property-lightbox-next" class="rt-property-lightbox__btn rt-property-lightbox__btn--next" aria-label="Следующее фото">&rsaquo;</button>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mainImage = document.getElementById('property-gallery-image');
            const galleryLink = document.getElementById('property-gallery-link');
            const thumbnails = document.querySelectorAll('.property-gallery-thumb');
            const lightbox = document.getElementById('property-lightbox');
            const lightboxImg = document.getElementById('property-lightbox-img');
            const lightboxCaption = document.getElementById('property-lightbox-caption');
            const lightboxPrev = document.getElementById('property-lightbox-prev');
            const lightboxNext = document.getElementById('property-lightbox-next');
            const lightboxClose = document.getElementById('property-lightbox-close');
            const lightboxCounter = document.getElementById('property-lightbox-counter');

            var galleryItems = [];
            var currentIndex = 0;

            function buildGallery() {
                galleryItems = [];
                var mainSrc = galleryLink ? galleryLink.getAttribute('href') : '';
                var mainAlt = mainImage ? mainImage.getAttribute('alt') : '';
                if (mainSrc) {
                    galleryItems.push({ src: mainSrc, alt: mainAlt });
                }

                thumbnails.forEach(function (thumb) {
                    var src = thumb.getAttribute('data-gallery-full');
                    var alt = thumb.getAttribute('data-gallery-alt');
                    if (src) {
                        galleryItems.push({ src: src, alt: alt });
                    }
                });
            }

            buildGallery();

            function openLightbox(index) {
                if (!lightbox || !lightboxImg || galleryItems.length === 0) return;
                currentIndex = (index + galleryItems.length) % galleryItems.length;
                var item = galleryItems[currentIndex];
                lightboxImg.setAttribute('src', item.src);
                lightboxImg.setAttribute('alt', item.alt);
                if (lightboxCaption) {
                    lightboxCaption.textContent = item.alt || '';
                }
                if (lightboxCounter) {
                    lightboxCounter.textContent = (currentIndex + 1) + ' / ' + galleryItems.length;
                }
                lightbox.classList.add('is-open');
                document.body.classList.add('rt-property-lightbox-open');
                if (lightboxPrev) {
                    lightboxPrev.style.display = galleryItems.length > 1 ? '' : 'none';
                }
                if (lightboxNext) {
                    lightboxNext.style.display = galleryItems.length > 1 ? '' : 'none';
                }
            }

            function closeLightbox() {
                if (!lightbox) return;
                lightbox.classList.remove('is-open');
                document.body.classList.remove('rt-property-lightbox-open');
            }

            if (galleryLink) {
                galleryLink.addEventListener('click', function (e) {
                    e.preventDefault();
                    buildGallery();
                    openLightbox(0);
                });
            }

            thumbnails.forEach(function (thumb, idx) {
                thumb.addEventListener('click', function () {
                    thumbnails.forEach(function (t) { t.classList.remove('is-active'); });
                    thumb.classList.add('is-active');

                    var fullSrc = thumb.getAttribute('data-gallery-full');
                    var altText = thumb.getAttribute('data-gallery-alt');
                    if (fullSrc && galleryLink) {
                        galleryLink.setAttribute('href', fullSrc);
                    }
                    if (fullSrc && mainImage) {
                        mainImage.setAttribute('src', fullSrc);
                        mainImage.setAttribute('alt', altText || '');
                    }

                    buildGallery();
                    openLightbox(idx + 1);
                });
            });

            if (lightboxPrev) {
                lightboxPrev.addEventListener('click', function () {
                    openLightbox(currentIndex - 1);
                });
            }

            if (lightboxNext) {
                lightboxNext.addEventListener('click', function () {
                    openLightbox(currentIndex + 1);
                });
            }

            if (lightboxClose) {
                lightboxClose.addEventListener('click', closeLightbox);
            }

            if (lightbox) {
                lightbox.addEventListener('click', function (e) {
                    if (e.target === lightbox) {
                        closeLightbox();
                    }
                });
            }

            document.addEventListener('keydown', function (e) {
                if (!lightbox || !lightbox.classList.contains('is-open')) return;
                if (e.key === 'Escape') closeLightbox();
                if (e.key === 'ArrowLeft') openLightbox(currentIndex - 1);
                if (e.key === 'ArrowRight') openLightbox(currentIndex + 1);
            });
        });
    </script>
@endpush