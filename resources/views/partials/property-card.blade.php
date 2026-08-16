@php
    $coverImage = $property->images->firstWhere('is_cover', true) ?? $property->images->first();
    $imageUrl = \App\Support\MediaPath::url(
        $coverImage?->thumb_path ?: $coverImage?->path,
        'legacy/themes/dolphin/assets/images/no_photo_entry.png',
    );
    $propertyImages = $property->images
        ->filter(fn ($image) => $image->path || $image->thumb_path)
        ->values();

    [$coverImages, $otherImages] = $propertyImages->partition(fn ($image) => (bool) $image->is_cover);
    $galleryItems = $coverImages
        ->concat($otherImages)
        ->map(fn ($image) => [
            'src' => \App\Support\MediaPath::url($image->path ?: $image->thumb_path, 'legacy/themes/dolphin/assets/images/no_photo_entry.png'),
            'thumb' => \App\Support\MediaPath::url($image->thumb_path ?: $image->path, 'legacy/themes/dolphin/assets/images/no_photo_entry.png'),
            'alt' => $image->alt ?: $property->title,
        ])
        ->values();

    if ($galleryItems->isEmpty()) {
        $galleryItems = collect([[
            'src' => $imageUrl,
            'thumb' => $imageUrl,
            'alt' => $property->title,
        ]]);
    }

    $employeePhoto = \App\Support\MediaPath::url(
        $property->employee?->photo_path,
        'legacy/themes/dolphin/assets/images/no_photo_entry.png',
    );
    $favoritePayload = [
        'slug' => $property->slug,
        'title' => $property->title,
        'url' => route('properties.show', $property->slug),
        'image' => $imageUrl,
        'price' => $property->price_label ?? number_format((int) $property->price, 0, ',', ' ') . ' ' . $property->currency,
    ];
@endphp
@once
    @push('styles')
        <style>
            .catalog .row {
                display: flex;
                flex-wrap: wrap;
            }
            .catalog .row::before,
            .catalog .row::after {
                display: none;
            }
            .apartment_item {
                display: flex;
                width: 100%;
                margin-bottom: 28px;
            }
            .apartment_item .item {
                display: flex;
                flex-direction: column;
                width: 100%;
                height: 100%;
                border: 1px solid #e3ebf3;
                border-radius: 14px;
                background: #fff;
                box-shadow: 0 16px 32px rgba(20, 48, 69, 0.08);
                overflow: hidden;
            }
            .apartment_item .photo_block {
                position: relative;
                background: linear-gradient(180deg, #e8f9ff, #f6fbff);
                aspect-ratio: 4 / 3;
                overflow: hidden;
            }
            .apartment_item .rt-property-gallery-trigger {
                position: relative;
                width: 100%;
                height: 100%;
                display: block;
                padding: 0;
                border: 0;
                background: transparent;
                cursor: zoom-in;
                text-align: left;
            }
            .apartment_item .photo_block img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
            .apartment_item .rt-property-gallery-trigger::after {
                content: 'Открыть фото';
                position: absolute;
                left: 14px;
                bottom: 14px;
                z-index: 1;
                padding: 7px 10px;
                border-radius: 8px;
                background: rgba(20, 48, 69, 0.78);
                color: #fff;
                font-size: 12px;
                line-height: 1;
                font-weight: 700;
                opacity: 0;
                transform: translateY(6px);
                transition: opacity .2s ease, transform .2s ease;
            }
            .apartment_item .rt-property-gallery-trigger:hover::after,
            .apartment_item .rt-property-gallery-trigger:focus::after {
                opacity: 1;
                transform: translateY(0);
            }
            .apartment_item .bl_wrapper {
                position: absolute;
                top: 18px;
                left: 18px;
                right: 18px;
                pointer-events: none;
            }
            .apartment_item .item_content {
                display: flex;
                flex: 1;
                flex-direction: column;
                padding: 0 20px 20px;
            }
            .apartment_item .ava {
                display: flex;
                justify-content: flex-end;
                margin-top: -34px;
                position: relative;
                z-index: 2;
                margin-bottom: 10px;
            }
            .apartment_item .message_ava {
                width: 72px;
                height: 72px;
                border-radius: 999px;
                object-fit: cover;
                border: 4px solid #fff;
                background: #fff;
                box-shadow: 0 8px 20px rgba(20, 48, 69, 0.15);
            }
            .apartment_item .title_item {
                margin-top: 0;
                margin-bottom: 10px;
                min-height: 62px;
            }
            .apartment_item .title_item a {
                display: -webkit-box;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
                overflow: hidden;
                line-height: 1.35;
                word-break: break-word;
            }
            .apartment_item .adress {
                color: #4b5563;
                margin-bottom: 8px;
                min-height: 24px;
            }
            .apartment_item .price {
                margin-bottom: 10px;
            }
            .apartment_item .price span:first-child {
                font-size: 20px;
                font-weight: 700;
                color: #f43f5e;
            }
            .apartment_item .spec_info {
                display: flex;
                flex-wrap: wrap;
                gap: 10px 18px;
                margin-bottom: 0;
                color: #475467;
            }
            .apartment_item .spec_info > li {
                margin: 0;
            }
            .apartment_item .admin-actions {
                margin-top: auto;
                padding-top: 16px;
            }
            .apartment_item [data-favorite-button] {
                width: 100%;
                border-radius: 10px;
            }
            .rt-photo-lightbox {
                position: fixed;
                inset: 0;
                z-index: 4000;
                display: none;
                align-items: center;
                justify-content: center;
                padding: 22px;
                background: rgba(13, 24, 39, 0.82);
            }
            .rt-photo-lightbox.is-open {
                display: flex;
            }
            body.rt-photo-lightbox-open {
                overflow: hidden;
            }
            .rt-photo-lightbox__dialog {
                position: relative;
                width: min(1120px, 96vw);
                max-height: 90vh;
                display: grid;
                grid-template-rows: minmax(0, 1fr) auto;
                gap: 12px;
                padding: 18px;
                border-radius: 14px;
                background: #111827;
                box-shadow: 0 28px 70px rgba(0, 0, 0, 0.38);
            }
            .rt-photo-lightbox__figure {
                min-height: 0;
                margin: 0;
                display: grid;
                grid-template-rows: minmax(260px, 1fr) auto;
                gap: 10px;
            }
            .rt-photo-lightbox__image-wrap {
                min-height: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 10px;
                background: #0b1220;
                overflow: hidden;
            }
            .rt-photo-lightbox__image {
                max-width: 100%;
                max-height: min(70vh, 700px);
                width: auto;
                height: auto;
                object-fit: contain;
                display: block;
            }
            .rt-photo-lightbox__caption {
                display: flex;
                justify-content: space-between;
                gap: 16px;
                color: #e5edf7;
                font-size: 14px;
                line-height: 1.4;
            }
            .rt-photo-lightbox__title {
                min-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .rt-photo-lightbox__counter {
                flex: 0 0 auto;
                color: #b7c3d4;
            }
            .rt-photo-lightbox__button {
                position: absolute;
                z-index: 2;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 46px;
                height: 46px;
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 999px;
                background: rgba(255, 255, 255, 0.92);
                color: #172033;
                font-size: 30px;
                line-height: 1;
                box-shadow: 0 12px 28px rgba(0, 0, 0, 0.22);
                transition: transform .2s ease, background-color .2s ease, opacity .2s ease;
            }
            .rt-photo-lightbox__button:hover,
            .rt-photo-lightbox__button:focus {
                transform: translateY(-1px);
                background: #fff;
            }
            .rt-photo-lightbox__button:disabled {
                opacity: .35;
                cursor: default;
            }
            .rt-photo-lightbox__button--close {
                top: 14px;
                right: 14px;
                font-size: 28px;
            }
            .rt-photo-lightbox__button--prev,
            .rt-photo-lightbox__button--next {
                top: 50%;
                transform: translateY(-50%);
            }
            .rt-photo-lightbox__button--prev:hover,
            .rt-photo-lightbox__button--prev:focus,
            .rt-photo-lightbox__button--next:hover,
            .rt-photo-lightbox__button--next:focus {
                transform: translateY(calc(-50% - 1px));
            }
            .rt-photo-lightbox__button--prev {
                left: 18px;
            }
            .rt-photo-lightbox__button--next {
                right: 18px;
            }
            .rt-photo-lightbox__thumbs {
                display: flex;
                gap: 8px;
                padding: 2px;
                overflow-x: auto;
            }
            .rt-photo-lightbox__thumb {
                flex: 0 0 78px;
                height: 54px;
                padding: 0;
                border: 2px solid transparent;
                border-radius: 8px;
                background: #1f2937;
                overflow: hidden;
            }
            .rt-photo-lightbox__thumb.is-active {
                border-color: #60a5fa;
            }
            .rt-photo-lightbox__thumb img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
            @media (min-width: 768px) {
                .apartment_item { width: 50%; }
            }
            @media (min-width: 1280px) {
                .apartment_item { width: 33.33333333%; }
            }
            @media (max-width: 767px) {
                .apartment_item { float: none; margin-bottom: 20px; }
                .apartment_item .item_content { padding: 0 18px 18px; }
                .apartment_item .title_item { min-height: 0; }
                .apartment_item .photo_block { aspect-ratio: 16 / 10; }
                .rt-photo-lightbox { padding: 10px; }
                .rt-photo-lightbox__dialog { width: 96vw; max-height: 92vh; padding: 12px; gap: 10px; }
                .rt-photo-lightbox__figure { grid-template-rows: minmax(220px, 1fr) auto; }
                .rt-photo-lightbox__image { max-height: 66vh; }
                .rt-photo-lightbox__button { width: 40px; height: 40px; font-size: 25px; }
                .rt-photo-lightbox__button--close { top: 10px; right: 10px; }
                .rt-photo-lightbox__button--prev { left: 10px; }
                .rt-photo-lightbox__button--next { right: 10px; }
                .rt-photo-lightbox__caption { flex-direction: column; gap: 4px; font-size: 13px; }
                .rt-photo-lightbox__thumb { flex-basis: 62px; height: 46px; }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            /* ---------- Photo lightbox ---------- */
            var items = [];
            var index = 0;
            var lastFocusedElement = null;
            var lightbox = document.querySelector('[data-property-lightbox]');

            if (!lightbox) {
                lightbox = document.createElement('div');
                lightbox.className = 'rt-photo-lightbox';
                lightbox.setAttribute('data-property-lightbox', '');
                lightbox.setAttribute('aria-hidden', 'true');
                lightbox.innerHTML = [
                    '<div class="rt-photo-lightbox__dialog" role="dialog" aria-modal="true" aria-label="Фотографии объявления">',
                    '<button class="rt-photo-lightbox__button rt-photo-lightbox__button--close" type="button" data-lightbox-close aria-label="Закрыть окно">&times;</button>',
                    '<button class="rt-photo-lightbox__button rt-photo-lightbox__button--prev" type="button" data-lightbox-prev aria-label="Предыдущее фото">&lsaquo;</button>',
                    '<figure class="rt-photo-lightbox__figure">',
                    '<div class="rt-photo-lightbox__image-wrap"><img class="rt-photo-lightbox__image" data-lightbox-image alt=""></div>',
                    '<figcaption class="rt-photo-lightbox__caption"><span class="rt-photo-lightbox__title" data-lightbox-title></span><span class="rt-photo-lightbox__counter" data-lightbox-counter></span></figcaption>',
                    '</figure>',
                    '<button class="rt-photo-lightbox__button rt-photo-lightbox__button--next" type="button" data-lightbox-next aria-label="Следующее фото">&rsaquo;</button>',
                    '<div class="rt-photo-lightbox__thumbs" data-lightbox-thumbs></div>',
                    '</div>'
                ].join('');
                document.body.appendChild(lightbox);
            }

            var imageEl = lightbox.querySelector('[data-lightbox-image]');
            var titleEl = lightbox.querySelector('[data-lightbox-title]');
            var counterEl = lightbox.querySelector('[data-lightbox-counter]');
            var thumbsEl = lightbox.querySelector('[data-lightbox-thumbs]');
            var closeBtn = lightbox.querySelector('[data-lightbox-close]');
            var prevBtn = lightbox.querySelector('[data-lightbox-prev]');
            var nextBtn = lightbox.querySelector('[data-lightbox-next]');

            function render() {
                var item = items[index];
                if (!item || !imageEl || !titleEl || !counterEl || !thumbsEl || !prevBtn || !nextBtn) return;

                imageEl.src = item.src;
                imageEl.alt = item.alt || '';
                titleEl.textContent = item.alt || '';
                counterEl.textContent = (index + 1) + ' / ' + items.length;
                prevBtn.disabled = items.length < 2;
                nextBtn.disabled = items.length < 2;

                thumbsEl.innerHTML = '';
                items.forEach(function (thumbItem, thumbIndex) {
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'rt-photo-lightbox__thumb' + (thumbIndex === index ? ' is-active' : '');
                    btn.setAttribute('aria-label', 'Открыть фото ' + (thumbIndex + 1));
                    var img = document.createElement('img');
                    img.src = thumbItem.thumb || thumbItem.src;
                    img.alt = '';
                    btn.appendChild(img);
                    btn.addEventListener('click', function () { index = thumbIndex; render(); });
                    thumbsEl.appendChild(btn);
                });
            }

            function openLightbox(nextItems) {
                items = nextItems;
                index = 0;
                lastFocusedElement = document.activeElement;
                render();
                lightbox.classList.add('is-open');
                lightbox.setAttribute('aria-hidden', 'false');
                document.body.classList.add('rt-photo-lightbox-open');
                if (closeBtn) closeBtn.focus();
            }

            function closeLightbox() {
                lightbox.classList.remove('is-open');
                lightbox.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('rt-photo-lightbox-open');
                if (lastFocusedElement instanceof HTMLElement) lastFocusedElement.focus();
            }

            function showPrev() {
                if (items.length < 2) return;
                index = (index - 1 + items.length) % items.length;
                render();
            }

            function showNext() {
                if (items.length < 2) return;
                index = (index + 1) % items.length;
                render();
            }

            document.addEventListener('click', function (event) {
                var trigger = event.target instanceof Element ? event.target.closest('[data-property-gallery-trigger]') : null;
                if (!trigger) return;
                event.preventDefault();
                var payload = trigger.getAttribute('data-property-gallery');
                if (!payload) return;
                try {
                    var parsed = JSON.parse(payload);
                    var arr = Array.isArray(parsed) ? parsed.filter(function (x) { return x && x.src; }) : [];
                    if (arr.length > 0) openLightbox(arr);
                } catch (e) { console.error('Property gallery parse error', e); }
            });

            if (closeBtn) closeBtn.addEventListener('click', closeLightbox);
            if (prevBtn) prevBtn.addEventListener('click', showPrev);
            if (nextBtn) nextBtn.addEventListener('click', showNext);

            lightbox.addEventListener('click', function (event) {
                if (event.target === lightbox) closeLightbox();
            });

            document.addEventListener('keydown', function (event) {
                if (!lightbox.classList.contains('is-open')) return;
                if (event.key === 'Escape') closeLightbox();
                if (event.key === 'ArrowLeft') showPrev();
                if (event.key === 'ArrowRight') showNext();
            });
        });
        </script>
    @endpush
@endonce
<div class="apartment_item block col-md-4 col-sm-6">
    <div class="item slow">
        <div class="photo_block">
            <div class="slinky-gallery">
                <button
                    class="rt-property-gallery-trigger"
                    type="button"
                    data-property-gallery-trigger
                    data-property-gallery='@json($galleryItems->all())'
                    aria-label="Открыть фотографии объявления {{ $property->title }}"
                >
                    <img src="{{ $imageUrl }}" alt="{{ $property->title }}">
                </button>
            </div>
            <div class="bl_wrapper">
                <div class="bl bl_type">{{ config('realty.deal_type_options.' . $property->deal_type, $property->deal_type) }}</div>
            </div>
        </div>
        <div class="item_content">
            <div class="ava">
                <img class="message_ava" src="{{ $employeePhoto }}" alt="{{ $property->employee?->full_name }}">
            </div>
            <div class="title_item h4">
                <a href="{{ route('properties.show', $property->slug) }}" class="slow" title="{{ $property->title }}">{{ $property->title }}</a>
            </div>
            <hr>
            <div class="adress">{{ $property->city ? config('realty.city_options.' . $property->city, $property->city) : 'Туапсе' }}</div>
            <div class="price">
                <span>{{ $property->price_label ?? number_format((int) $property->price, 0, ',', ' ') }}</span> <span class="currency">{{ $property->currency }}</span>
                <hr>
            </div>
            <ul class="list-inline spec_info">
                <li>{{ $property->rooms ?: '—' }} комн.</li>
                <li>{{ $property->square ? number_format((float) $property->square, 1, ',', ' ') . ' м²' : 'Площадь уточняется' }}</li>
            </ul>
            <div class="admin-actions">
                @if ($property->latitude && $property->longitude)
                <a href="{{ route('properties.show', $property->slug) }}#map" class="btn btn-sm" style="background:#43b3dd;color:#fff;width:100%;margin-bottom:6px;">
                    <i class="fas fa-map-marker-alt"></i> Подробнее на карте
                </a>
                @endif
                <button
                    class="btn btn-default btn-sm"
                    type="button"
                    data-favorite-button
                    data-favorite-slug="{{ $property->slug }}"
                    data-favorite-item='@json($favoritePayload)'
                >
                    В избранное
                </button>
            </div>
        </div>
    </div>
</div>