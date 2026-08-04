@extends('layouts.site')

@section('title', 'Поиск недвижимости')

@push('styles')
    <style>
        .search-map-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) minmax(320px, 1fr);
            gap: 24px;
            align-items: start;
        }
        .search-map-canvas {
            position: relative;
            min-height: 520px;
            border-radius: 18px;
            overflow: hidden;
            background:
                linear-gradient(180deg, rgba(227, 245, 255, 0.95), rgba(247, 250, 255, 0.98)),
                radial-gradient(circle at 20% 20%, rgba(67, 179, 221, 0.18), transparent 30%),
                radial-gradient(circle at 80% 75%, rgba(20, 48, 69, 0.10), transparent 28%);
            border: 1px solid #d7e4ef;
        }
        .search-map-canvas::before,
        .search-map-canvas::after {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
        }
        .search-map-canvas::before {
            background-image:
                linear-gradient(rgba(20, 48, 69, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(20, 48, 69, 0.05) 1px, transparent 1px);
            background-size: 80px 80px;
        }
        .search-map-canvas::after {
            background:
                linear-gradient(90deg, transparent 48%, rgba(67, 179, 221, 0.18) 50%, transparent 52%),
                linear-gradient(0deg, transparent 48%, rgba(67, 179, 221, 0.18) 50%, transparent 52%);
            opacity: .35;
        }
        .search-map-label {
            position: absolute;
            left: 18px;
            top: 14px;
            z-index: 2;
            background: rgba(255, 255, 255, 0.92);
            border-radius: 999px;
            padding: 10px 14px;
            font-size: 13px;
            color: #4b5563;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }
        .search-map-pin {
            position: absolute;
            transform: translate(-50%, -100%);
            width: 18px;
            height: 18px;
            border-radius: 999px;
            background: #d9534f;
            border: 3px solid #fff;
            box-shadow: 0 10px 18px rgba(217, 83, 79, 0.28);
        }
        .search-map-pin::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -11px;
            transform: translateX(-50%);
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 10px solid #d9534f;
        }
        .search-map-card {
            background: #fff;
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 12px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.06);
        }
        .search-map-card p:last-child {
            margin-bottom: 0;
        }
        @media (max-width: 1199px) {
            .search-map-layout {
                grid-template-columns: 1fr;
            }

            .search-map-card {
                margin-bottom: 14px;
            }
        }

        @media (max-width: 991px) {
            .search-map-canvas {
                min-height: 340px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content main_content">
        <div class="content_center main-content-wrapper box">
            <div class="title h3 fint l_fint">Поиск недвижимости</div>
            <div class="btn-group" style="margin-bottom: 20px;">
                <a class="btn btn-default @if($listMode === 'block') active @endif" href="{{ request()->fullUrlWithQuery(['ls' => 'block']) }}">Плитка</a>
                <a class="btn btn-default @if($listMode === 'table') active @endif" href="{{ request()->fullUrlWithQuery(['ls' => 'table']) }}">Таблица</a>
                <a class="btn btn-default @if($listMode === 'map') active @endif" href="{{ request()->fullUrlWithQuery(['ls' => 'map']) }}">Карта</a>
            </div>

            @php
                $mapProperties = $properties->getCollection()->filter(fn ($property) => $property->latitude !== null && $property->longitude !== null)->values();
                $latitudes = $mapProperties->pluck('latitude')->map(fn ($value) => (float) $value);
                $longitudes = $mapProperties->pluck('longitude')->map(fn ($value) => (float) $value);
                $minLat = $latitudes->min();
                $maxLat = $latitudes->max();
                $minLng = $longitudes->min();
                $maxLng = $longitudes->max();
                $latRange = max(($maxLat ?? 0) - ($minLat ?? 0), 0.0001);
                $lngRange = max(($maxLng ?? 0) - ($minLng ?? 0), 0.0001);

                $mapJson = json_encode(
                    $mapProperties->map(function ($p) {
                        return [
                            'id' => $p->id,
                            'title' => $p->title,
                            'address' => $p->address,
                            'price' => $p->price_label ?? number_format((int) $p->price, 0, ',', ' ') . ' ' . $p->currency,
                            'lat' => (float) $p->latitude,
                            'lng' => (float) $p->longitude,
                            'url' => route('properties.show', $p->slug),
                        ];
                    }),
                );
            @endphp

            @if ($listMode === 'table')
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>№</th>
                                <th>Объект</th>
                                <th>Адрес</th>
                                <th>Цена</th>
                                <th>Сотрудник</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($properties as $property)
                                <tr>
                                    <td>{{ $property->legacy_id }}</td>
                                    <td><a href="{{ route('properties.show', $property->slug) }}">{{ $property->title }}</a></td>
                                    <td>{{ $property->address }}</td>
                                    <td>{{ $property->price_label ?? number_format((int) $property->price, 0, ',', ' ') . ' ' . $property->currency }}</td>
                                    <td>{{ $property->employee?->full_name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">По вашему запросу ничего не найдено.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @elseif ($listMode === 'map')
                <div class="search-map-layout">
                    <div class="search-map-canvas" id="search-map">
                        <div class="search-map-label">Карта объектов по текущему поисковому запросу</div>
                    </div>
                    <div>
                        @forelse ($properties as $property)
                            <div class="search-map-card">
                                <p><strong><a href="{{ route('properties.show', $property->slug) }}">{{ $property->title }}</a></strong></p>
                                <p>{{ $property->address ?: config('realty.city_options.' . $property->city, $property->city) }}</p>
                                <p>{{ $property->price_label ?? number_format((int) $property->price, 0, ',', ' ') . ' ' . $property->currency }}</p>
                                <p style="color: #667085;">{{ $property->employee?->full_name }}</p>
                            </div>
                        @empty
                            <p>По вашему запросу ничего не найдено.</p>
                        @endforelse
                    </div>
                </div>
            @else
                <div class="catalog">
                    <div class="row">
                        @forelse ($properties as $property)
                            @include('partials.property-card', ['property' => $property])
                        @empty
                            <div class="col-md-12">
                                <p>По вашему запросу ничего не найдено.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            <div style="margin-top: 20px;">
                {{ $properties->links() }}
            </div>
        </div>
    </div>

    @if ($listMode === 'map' && $mapProperties->isNotEmpty())
    <script src="https://api-maps.yandex.ru/2.1/?apikey=38c8767e-6b9b-452a-a56b-f7c8b927ea10&lang=ru_RU" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof ymaps === 'undefined') {
                console.warn('Yandex Maps API not loaded');
                return;
            }
            ymaps.ready(initSearchMap);
        });

        function initSearchMap() {
            var properties = {!! $mapJson !!};

            if (properties.length === 0) return;

            var bounds = [];
            properties.forEach(function (p) { bounds.push([p.lat, p.lng]); });

            var searchMap = new ymaps.Map('search-map', {
                center: bounds[0],
                zoom: 13,
                controls: ['zoomControl', 'fullscreenControl']
            });

            properties.forEach(function (p) {
                var placemark = new ymaps.Placemark([p.lat, p.lng], {
                    balloonContentHeader: '<a href="' + p.url + '" target="_blank">' + p.title + '</a>',
                    balloonContentBody: '<p style="margin:4px 0;">' + (p.address || '') + '</p><p style="margin:4px 0;font-weight:bold;">' + p.price + '</p>'
                }, {
                    preset: 'islands#redRealEstateIcon'
                });

                placemark.events.add('click', function () {
                    window.location.href = p.url;
                });

                searchMap.geoObjects.add(placemark);
            });

            searchMap.setBounds(bounds, { checkZoomRange: true });
        }
    </script>
    @endif
@endsection
