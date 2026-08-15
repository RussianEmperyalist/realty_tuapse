@extends('layouts.site')

@section('title', 'Фотогалерея')

@push('styles')
    <style>
        .gallery-page { width: 100%; }

        .gallery-section { margin-bottom: 34px; }

        .gallery-section__description {
            max-width: 820px;
            margin-bottom: 18px;
            color: #4b5563;
            line-height: 1.65;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .gallery-grid__item {
            display: block;
            overflow: hidden;
            border-radius: 8px;
            background: #eef3f8;
            aspect-ratio: 4 / 3;
            box-shadow: 0 10px 22px rgba(20, 48, 69, 0.08);
            cursor: zoom-in;
            border: 0;
            padding: 0;
        }

        .gallery-grid__item img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .22s ease;
        }

        .gallery-grid__item:hover img,
        .gallery-grid__item:focus img { transform: scale(1.035); }

        @media (min-width: 520px) {
            .gallery-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (min-width: 768px) {
            .gallery-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
        }
        @media (min-width: 1600px) {
            .gallery-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        }

        /* Lightbox */
        .rt-gallery-lightbox {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(10, 15, 25, 0.92);
        }
        .rt-gallery-lightbox.is-open { display: flex; }
        body.rt-gallery-lightbox-open { overflow: hidden; }

        .rt-gallery-lightbox__img {
            max-width: 94vw;
            max-height: 88vh;
            object-fit: contain;
            border-radius: 4px;
            box-shadow: 0 10px 60px rgba(0,0,0,.5);
        }

        .rt-gallery-lightbox__btn {
            position: absolute;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border: none;
            border-radius: 50%;
            background: rgba(255,255,255,.9);
            color: #172033;
            font-size: 28px;
            line-height: 1;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(0,0,0,.3);
            transition: background .2s, transform .15s;
        }
        .rt-gallery-lightbox__btn:hover,
        .rt-gallery-lightbox__btn:focus { background: #fff; transform: scale(1.05); }
        .rt-gallery-lightbox__btn:disabled { opacity: .25; cursor: default; transform: none; }

        .rt-gallery-lightbox__btn--close { top: 14px; right: 14px; font-size: 32px; }
        .rt-gallery-lightbox__btn--prev { left: 14px; top: 50%; transform: translateY(-50%); }
        .rt-gallery-lightbox__btn--prev:hover,
        .rt-gallery-lightbox__btn--prev:focus { transform: translateY(-50%) scale(1.05); }
        .rt-gallery-lightbox__btn--next { right: 14px; top: 50%; transform: translateY(-50%); }
        .rt-gallery-lightbox__btn--next:hover,
        .rt-gallery-lightbox__btn--next:focus { transform: translateY(-50%) scale(1.05); }

        .rt-gallery-lightbox__counter {
            position: absolute;
            bottom: 16px;
            left: 50%;
            transform: translateX(-50%);
            color: rgba(255,255,255,.85);
            font-size: 14px;
            font-weight: 600;
            background: rgba(0,0,0,.45);
            padding: 6px 14px;
            border-radius: 999px;
            pointer-events: none;
        }

        @media (max-width: 767px) {
            .rt-gallery-lightbox { padding: 10px; }
            .rt-gallery-lightbox__btn { width: 40px; height: 40px; font-size: 22px; }
            .rt-gallery-lightbox__btn--close { top: 8px; right: 8px; }
            .rt-gallery-lightbox__btn--prev { left: 8px; }
            .rt-gallery-lightbox__btn--next { right: 8px; }
        }

        /* Скрытие лишних фото в разделе + кнопка «Показать все / Свернуть» */
        .gallery-hidden { display: none; }

        .gallery-grid__item.is-revealing {
            animation: rt-gallery-reveal .45s ease both;
        }

        @keyframes rt-gallery-reveal {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .gallery-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 20px auto 0;
            padding: 11px 26px;
            border: 1px solid #4c78ab;
            border-radius: 999px;
            background: #fff;
            color: #4c78ab;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background-color .2s ease, color .2s ease, border-color .2s ease;
        }

        .gallery-toggle:hover,
        .gallery-toggle:focus {
            background: #4c78ab;
            color: #fff;
            border-color: #4c78ab;
        }

        .gallery-toggle[aria-expanded="true"] { background: #eef3f8; color: #4c78ab; }
    </style>
@endpush

@push('scripts')
    <script>
        // Лайтбокс (существующий, работает по data-gallery-group)
        document.addEventListener('DOMContentLoaded', function () {
            const triggers = document.querySelectorAll('[data-gallery-group]');
            if (!triggers.length) return;

            let items = [], current = 0, lastFocused = null;

            const lb = document.createElement('div');
            lb.className = 'rt-gallery-lightbox';
            lb.setAttribute('role', 'dialog');
            lb.setAttribute('aria-modal', 'true');
            lb.setAttribute('aria-label', 'Просмотр фотографий');
            lb.innerHTML = [
                '<button class="rt-gallery-lightbox__btn rt-gallery-lightbox__btn--close" type="button" aria-label="Закрыть">&times;</button>',
                '<button class="rt-gallery-lightbox__btn rt-gallery-lightbox__btn--prev" type="button" aria-label="Предыдущее">&lsaquo;</button>',
                '<img class="rt-gallery-lightbox__img" src="" alt="">',
                '<button class="rt-gallery-lightbox__btn rt-gallery-lightbox__btn--next" type="button" aria-label="Следующее">&rsaquo;</button>',
                '<div class="rt-gallery-lightbox__counter"></div>'
            ].join('');
            document.body.appendChild(lb);

            const img = lb.querySelector('.rt-gallery-lightbox__img');
            const counter = lb.querySelector('.rt-gallery-lightbox__counter');
            const closeBtn = lb.querySelector('.rt-gallery-lightbox__btn--close');
            const prevBtn = lb.querySelector('.rt-gallery-lightbox__btn--prev');
            const nextBtn = lb.querySelector('.rt-gallery-lightbox__btn--next');

            function show(index) {
                current = (index + items.length) % items.length;
                img.src = items[current].src;
                img.alt = items[current].alt || '';
                counter.textContent = (current + 1) + ' / ' + items.length;
                prevBtn.disabled = items.length < 2;
                nextBtn.disabled = items.length < 2;
            }

            function open(group, startIndex) {
                items = Array.from(document.querySelectorAll('[data-gallery-group="' + group + '"]'))
                    .map(function (el) { return { src: el.dataset.gallerySrc, alt: el.dataset.galleryAlt || '' }; });
                current = startIndex;
                lastFocused = document.activeElement;
                show(current);
                lb.classList.add('is-open');
                document.body.classList.add('rt-gallery-lightbox-open');
                closeBtn.focus();
            }

            function close() {
                lb.classList.remove('is-open');
                document.body.classList.remove('rt-gallery-lightbox-open');
                if (lastFocused instanceof HTMLElement) lastFocused.focus();
            }

            triggers.forEach(function (el, i) {
                el.addEventListener('click', function (e) {
                    e.preventDefault();
                    open(el.dataset.galleryGroup, i);
                });
            });

            closeBtn.addEventListener('click', close);
            prevBtn.addEventListener('click', function () { show(current - 1); });
            nextBtn.addEventListener('click', function () { show(current + 1); });
            lb.addEventListener('click', function (e) { if (e.target === lb) close(); });
            document.addEventListener('keydown', function (e) {
                if (!lb.classList.contains('is-open')) return;
                if (e.key === 'Escape') close();
                if (e.key === 'ArrowLeft') show(current - 1);
                if (e.key === 'ArrowRight') show(current + 1);
            });
        });

        // Лимит фото в разделе + кнопка «Показать все / Свернуть» (vanilla JS)
        document.addEventListener('DOMContentLoaded', function () {
            const sections = document.querySelectorAll('[data-gallery-limit]');
            if (!sections.length) return;

            sections.forEach(function (section) {
                const hidden = section.querySelectorAll('.gallery-grid__item.gallery-hidden');
                if (!hidden.length) return;

                const toggle = document.createElement('button');
                toggle.className = 'gallery-toggle';
                toggle.type = 'button';
                toggle.setAttribute('aria-expanded', 'false');
                toggle.textContent = 'Показать все (' + hidden.length + ' фото)';

                toggle.addEventListener('click', function () {
                    const isOpen = toggle.getAttribute('aria-expanded') === 'true';
                    hidden.forEach(function (item) {
                        if (isOpen) {
                            item.classList.remove('is-revealing');
                            item.classList.add('gallery-hidden');
                        } else {
                            item.classList.remove('gallery-hidden');
                            item.classList.add('is-revealing');
                            setTimeout(function () {
                                item.classList.remove('is-revealing');
                            }, 450);
                        }
                    });
                    toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                    toggle.textContent = isOpen
                        ? 'Показать все (' + hidden.length + ' фото)'
                        : 'Свернуть';
                });

                section.insertAdjacentElement('afterend', toggle);
            });
        });
    </script>
@endpush

@section('content')
    <div class="content_box content gallery-page">
        <h1 class="fint l_fint">Фотогалерея</h1>
        @forelse ($albums as $album)
            <section class="box gallery-section">
                <div class="h3 fint l_fint">{{ $album->title }}</div>
                @if ($album->description)
                    <p class="gallery-section__description">{{ $album->description }}</p>
                @endif
                <div class="gallery-grid" data-gallery-limit="8">
                    @foreach ($album->items as $item)
                        @php
                            $fullImageUrl = \App\Support\MediaPath::url($item->image_path);
                            $thumbImageUrl = \App\Support\MediaPath::url($item->thumb_path ?: $item->image_path);
                        @endphp
                        @if ($fullImageUrl && $thumbImageUrl)
                            <button
                                class="gallery-grid__item{{ $loop->index >= 8 ? ' gallery-hidden' : '' }}"
                                type="button"
                                data-gallery-group="{{ $album->slug }}"
                                data-gallery-src="{{ $fullImageUrl }}"
                                data-gallery-alt="{{ $item->title }}"
                                aria-label="Открыть фото {{ $item->title }}"
                            >
                                <img src="{{ $thumbImageUrl }}" alt="{{ $item->title }}" loading="lazy">
                            </button>
                        @endif
                    @endforeach
                </div>
            </section>
        @empty
            <p>Галерея пока не заполнена.</p>
        @endforelse
    </div>
@endsection
