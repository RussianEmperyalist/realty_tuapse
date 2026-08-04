@extends('layouts.site')

@section('title', 'Избранное')

@push('styles')
    <style>
        .fav-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 24px;
        }

        .fav-empty {
            color: #667085;
            font-size: 16px;
            padding: 40px 0;
            text-align: center;
        }

        .fav-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .fav-card {
            display: flex;
            gap: 0;
            background: #fff;
            border: 1px solid #e3ebf3;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(20,48,69,.07);
            overflow: hidden;
        }

        .fav-card__img {
            flex: 0 0 220px;
            width: 220px;
            min-height: 160px;
            object-fit: cover;
            display: block;
        }

        .fav-card__body {
            flex: 1;
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 0;
        }

        .fav-card__title {
            font-size: 17px;
            font-weight: 700;
            margin: 0 0 6px;
            line-height: 1.3;
        }

        .fav-card__title a { color: inherit; text-decoration: none; }
        .fav-card__title a:hover { text-decoration: underline; }

        .fav-card__address {
            font-size: 13px;
            color: #667085;
            margin-bottom: 8px;
        }

        .fav-card__price {
            font-size: 20px;
            font-weight: 700;
            color: #e31e25;
            margin-bottom: 14px;
        }

        .fav-card__actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        @media (max-width: 600px) {
            .fav-card { flex-direction: column; }
            .fav-card__img { width: 100%; flex-basis: auto; height: 200px; }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var container = document.getElementById('fav-container');
            var toolbar   = document.getElementById('fav-toolbar');
            if (!container) return;

            var items = [];
            try { items = JSON.parse(localStorage.getItem('realty-favorites') || '[]'); } catch(e) { items = []; }
            if (!Array.isArray(items)) items = [];

            function render() {
                if (items.length === 0) {
                    container.innerHTML = '<p class="fav-empty">\u041f\u043e\u043a\u0430 \u0432 \u0438\u0437\u0431\u0440\u0430\u043d\u043d\u043e\u043c \u043d\u0435\u0442 \u043e\u0431\u044a\u0435\u043a\u0442\u043e\u0432.<br>\u041d\u0430\u0436\u043c\u0438\u0442\u0435 \u00ab\u0412 \u0438\u0437\u0431\u0440\u0430\u043d\u043d\u043e\u0435\u00bb \u043d\u0430 \u043a\u0430\u0440\u0442\u043e\u0447\u043a\u0435 \u043b\u044e\u0431\u043e\u0433\u043e \u043e\u0431\u044a\u044f\u0432\u043b\u0435\u043d\u0438\u044f.</p>';
                    if (toolbar) toolbar.style.display = 'none';
                    return;
                }

                if (toolbar) toolbar.style.display = '';

                container.innerHTML = '<div class="fav-list">' + items.map(function (item) {
                    return '<div class="fav-card" data-slug="' + esc(item.slug) + '">' +
                        '<img class="fav-card__img" src="' + esc(item.image) + '" alt="' + esc(item.title) + '">' +
                        '<div class="fav-card__body">' +
                            '<div>' +
                                '<h3 class="fav-card__title"><a href="' + esc(item.url) + '">' + esc(item.title) + '</a></h3>' +
                                (item.address ? '<div class="fav-card__address"><i class="fas fa-map-marker-alt"></i> ' + esc(item.address) + '</div>' : '') +
                                '<div class="fav-card__price">' + esc(item.price) + '</div>' +
                            '</div>' +
                            '<div class="fav-card__actions">' +
                                '<a class="btn btn-primary" href="' + esc(item.url) + '">\u041f\u043e\u0434\u0440\u043e\u0431\u043d\u0435\u0435</a>' +
                                '<button class="btn btn-default" type="button" data-remove="' + esc(item.slug) + '">\u0423\u0431\u0440\u0430\u0442\u044c \u0438\u0437 \u0438\u0437\u0431\u0440\u0430\u043d\u043d\u043e\u0433\u043e</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>';
                }).join('') + '</div>';

                container.querySelectorAll('[data-remove]').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        var slug = btn.getAttribute('data-remove');
                        items = items.filter(function (i) { return i.slug !== slug; });
                        localStorage.setItem('realty-favorites', JSON.stringify(items));
                        render();
                        window.RealtyFavorites && window.RealtyFavorites.refreshButtons();
                    });
                });
            }

            function esc(str) {
                return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            }

            render();

            // --- Печать ---
            var printBtn = document.getElementById('fav-print-btn');
            if (printBtn) {
                printBtn.addEventListener('click', function () {
                    if (items.length === 0) return;

                    var YANDEX_KEY = '38c8767e-6b9b-452a-a56b-f7c8b927ea10';

                    function staticMap(lat, lng) {
                        if (!lat || !lng) return '';
                        var url = 'https://static-maps.yandex.ru/1.x/'
                            + '?apikey=' + YANDEX_KEY
                            + '&lang=ru_RU'
                            + '&ll=' + parseFloat(lng).toFixed(6) + ',' + parseFloat(lat).toFixed(6)
                            + '&z=15'
                            + '&size=580,220'
                            + '&l=map'
                            + '&pt=' + parseFloat(lng).toFixed(6) + ',' + parseFloat(lat).toFixed(6) + ',pm2rdm';
                        return '<img class="card-map" src="' + url + '" alt="\u041a\u0430\u0440\u0442\u0430">';
                    }

                    var html = '<!DOCTYPE html><html lang="ru"><head><meta charset="utf-8">'
                        + '<title>\u0418\u0437\u0431\u0440\u0430\u043d\u043d\u044b\u0435 \u043e\u0431\u044a\u0435\u043a\u0442\u044b \u2014 \u0422\u0443\u0430\u043f\u0441\u0435</title>'
                        + '<style>'
                        + '* { box-sizing: border-box; margin: 0; padding: 0; }'
                        + 'body { font-family: Arial, sans-serif; font-size: 13px; color: #111; background: #fff; }'
                        + '.page { width: 210mm; min-height: 297mm; padding: 12mm 14mm; page-break-after: always; display: flex; flex-direction: column; gap: 10px; }'
                        + '.page:last-child { page-break-after: avoid; }'
                        + '.page-header { font-size: 11px; color: #888; border-bottom: 1px solid #ddd; padding-bottom: 7px; display: flex; justify-content: space-between; }'
                        + '.card-img { width: 100%; height: 180px; object-fit: cover; border-radius: 6px; display: block; }'
                        + '.card-map { width: 100%; height: 180px; object-fit: cover; border-radius: 6px; display: block; border: 1px solid #ddd; }'
                        + '.card-no-map { width: 100%; height: 60px; background: #f5f5f5; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #aaa; font-size: 12px; }'
                        + '.card-title { font-size: 17px; font-weight: 700; line-height: 1.3; }'
                        + '.card-price { font-size: 20px; font-weight: 700; color: #c0392b; }'
                        + '.card-address { font-size: 12px; color: #555; }'
                        + '.card-url { font-size: 11px; color: #555; word-break: break-all; margin-top: auto; padding-top: 10px; border-top: 1px solid #eee; }'
                        + '.card-num { font-size: 11px; color: #999; }'
                        + '.two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }'
                        + '@media print {'
                        + '  body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }'
                        + '  .page { page-break-after: always; }'
                        + '  .page:last-child { page-break-after: avoid; }'
                        + '}'
                        + '</style></head><body>';

                    items.forEach(function (item, i) {
                        var mapHtml = item.lat && item.lng
                            ? staticMap(item.lat, item.lng)
                            : '<div class="card-no-map">\u041a\u043e\u043e\u0440\u0434\u0438\u043d\u0430\u0442\u044b \u043d\u0435 \u0443\u043a\u0430\u0437\u0430\u043d\u044b</div>';

                        html += '<div class="page">'
                            + '<div class="page-header">'
                            +   '<span>\u0410\u0433\u0435\u043d\u0442\u0441\u0442\u0432\u043e \u043d\u0435\u0434\u0432\u0438\u0436\u0438\u043c\u043e\u0441\u0442\u0438 \u00ab\u0422\u0443\u0430\u043f\u0441\u0435\u00bb \u2014 \u0418\u0437\u0431\u0440\u0430\u043d\u043d\u044b\u0435 \u043e\u0431\u044a\u0435\u043a\u0442\u044b</span>'
                            +   '<span>' + (i + 1) + ' / ' + items.length + '</span>'
                            + '</div>'
                            + '<div class="card-num">\u041e\u0431\u044a\u0435\u043a\u0442 \u2116 ' + (i + 1) + '</div>'
                            + '<div class="card-title">' + (item.title || '') + '</div>'
                            + '<div class="card-price">' + (item.price || '') + '</div>'
                            + (item.address ? '<div class="card-address">\u0410\u0434\u0440\u0435\u0441: ' + item.address + '</div>' : '')
                            + '<div class="two-col">'
                            +   '<img class="card-img" src="' + (item.image || '') + '" alt="">'
                            +   mapHtml
                            + '</div>'
                            + '<div class="card-url">\u0421\u0441\u044b\u043b\u043a\u0430: ' + (item.url || '') + '</div>'
                            + '</div>';
                    });

                    html += '<script>window.onload=function(){window.print();}<\/script></body></html>';

                    var win = window.open('', '_blank');
                    if (win) { win.document.write(html); win.document.close(); }
                });
            }
        });
    </script>
@endpush

@section('content')
    <div class="content_box content">
        <div class="box">
            <h1 class="fint l_fint">Избранное</h1>
            <p style="color:#667085; margin-bottom: 20px;">Объекты, которые вы отметили в каталоге.</p>

            <div id="fav-toolbar" class="fav-toolbar" style="display:none;">
                <span></span>
                <button id="fav-print-btn" class="btn btn-primary" type="button">
                    <i class="fas fa-print"></i> Распечатать избранное
                </button>
            </div>

            <div id="fav-container"></div>
        </div>
    </div>
@endsection
