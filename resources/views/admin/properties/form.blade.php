@extends('layouts.admin')

@section('title', $property->exists ? 'Редактирование объекта' : 'Новый объект')

@section('content')
    <div class="admin-page-header">
        <div>
            <h1 style="margin: 0 0 8px;">{{ $property->exists ? 'Редактирование объекта' : 'Новый объект' }}</h1>
            <p style="margin: 0; color: #667085;">Карточка объекта, параметры фильтрации и фото.</p>
        </div>
        <div class="admin-actions">
            <a class="btn btn-default" href="{{ route('admin.properties.index') }}">К списку</a>
            @if ($property->exists)
                <a class="btn btn-primary" href="{{ route('properties.show', $property->slug) }}" target="_blank">Открыть на сайте</a>
            @endif
        </div>
    </div>

    <form method="post" action="{{ $formAction }}" enctype="multipart/form-data">
        @csrf
        @if ($method !== 'post')
            @method($method)
        @endif

        <div class="admin-form-card">
            <div class="admin-grid">
                <div>
                    <label for="legacy_id">Legacy ID</label>
                    <input class="form-control" id="legacy_id" name="legacy_id" type="number" value="{{ old('legacy_id', $property->legacy_id) }}">
                </div>
                <div>
                    <label for="employee_id">Сотрудник</label>
                    <select class="form-control" id="employee_id" name="employee_id">
                        <option value="">Не назначен</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" @selected((int) old('employee_id', $selectedEmployeeId) === $employee->id)>{{ $employee->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="admin-grid--full">
                    <label for="title">Заголовок</label>
                    <input class="form-control" id="title" name="title" type="text" value="{{ old('title', $property->title) }}" required>
                </div>
                <div>
                    <label for="slug">Slug</label>
                    <input class="form-control" id="slug" name="slug" type="text" value="{{ old('slug', $property->slug) }}">
                </div>
                <div>
                    <label for="address">Адрес</label>
                    <input class="form-control" id="address" name="address" type="text" value="{{ old('address', $property->address) }}">
                </div>
                <div>
                    <label for="deal_type">Сделка</label>
                    <select class="form-control" id="deal_type" name="deal_type" required>
                        @foreach (config('realty.deal_type_options') as $key => $label)
                            <option value="{{ $key }}" @selected(old('deal_type', $property->deal_type) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="property_type">Тип недвижимости</label>
                    <select class="form-control" id="property_type" name="property_type" required>
                        @foreach (config('realty.property_type_options') as $key => $label)
                            <option value="{{ $key }}" @selected(old('property_type', $property->property_type) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="city">Город / раздел</label>
                    <select class="form-control" id="city" name="city">
                        <option value="">Не указано</option>
                        @foreach (config('realty.city_options') as $key => $label)
                            <option value="{{ $key }}" @selected(old('city', $property->city) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="price">Цена</label>
                    <input class="form-control" id="price" name="price" type="number" value="{{ old('price', $property->price) }}">
                </div>
                <div>
                    <label for="price_label">Текст цены</label>
                    <input class="form-control" id="price_label" name="price_label" type="text" value="{{ old('price_label', $property->price_label) }}">
                </div>
                <div>
                    <label for="currency">Валюта</label>
                    <input class="form-control" id="currency" name="currency" type="text" value="{{ old('currency', $property->currency ?: 'руб.') }}">
                </div>
                <div>
                    <label for="rooms">Комнат</label>
                    <input class="form-control" id="rooms" name="rooms" type="number" value="{{ old('rooms', $property->rooms) }}">
                </div>
                <div>
                    <label for="floor">Этаж</label>
                    <input class="form-control" id="floor" name="floor" type="number" value="{{ old('floor', $property->floor) }}">
                </div>
                <div>
                    <label for="floors_total">Этажей в доме</label>
                    <input class="form-control" id="floors_total" name="floors_total" type="number" value="{{ old('floors_total', $property->floors_total) }}">
                </div>
                <div>
                    <label for="square">Площадь</label>
                    <input class="form-control" id="square" name="square" type="number" step="0.01" value="{{ old('square', $property->square) }}">
                </div>
                <div>
                    <label for="windows">Окна</label>
                    <input class="form-control" id="windows" name="windows" type="text" value="{{ old('windows', $property->windows) }}">
                </div>
                <div>
                    <label for="phone_override">Телефон на карточке</label>
                    <input class="form-control" id="phone_override" name="phone_override" type="text" value="{{ old('phone_override', $property->phone_override) }}">
                </div>
                <div>
                    <label for="owner_phone" style="color:#667085;">Телефон собственника (только для администраторов)</label>
                    <input class="form-control" id="owner_phone" name="owner_phone" type="text" value="{{ old('owner_phone', $property->owner_phone) }}" style="border-color:#999;">
                    <small style="color:#999;">Внутреннее поле — скрыто от посетителей сайта.</small>
                </div>
                <div class="admin-grid--full">
                    <label>Координаты на карте</label>
                    <div id="admin-map" style="width:100%;height:400px;border-radius:8px;border:1px solid #dfe5ee;margin-bottom:8px;"></div>
                    <div style="display:flex;gap:16px;flex-wrap:wrap;">
                        <div style="flex:1;min-width:200px;">
                            <label for="latitude" style="font-size:13px;color:#667085;">Широта</label>
                            <input class="form-control" id="latitude" name="latitude" type="number" step="0.0000001" value="{{ old('latitude', $property->latitude) }}">
                        </div>
                        <div style="flex:1;min-width:200px;">
                            <label for="longitude" style="font-size:13px;color:#667085;">Долгота</label>
                            <input class="form-control" id="longitude" name="longitude" type="number" step="0.0000001" value="{{ old('longitude', $property->longitude) }}">
                        </div>
                        <div style="flex:0 0 auto;display:flex;align-items:flex-end;">
                            <button type="button" id="admin-map-clear" class="btn btn-default btn-sm" style="margin-top:20px;">Очистить</button>
                        </div>
                    </div>
                    <small style="color:#667085;">Кликните по карте для установки метки, или введите координаты вручную.</small>
                </div>
                <div>
                    <label for="published_at">Дата публикации</label>
                    <input class="form-control" id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', optional($property->published_at)->format('Y-m-d\\TH:i')) }}">
                </div>
                <div class="admin-grid--full">
                    <label for="description">Описание</label>
                    <textarea class="form-control" id="description" name="description" rows="10">{{ old('description', $property->description) }}</textarea>
                </div>
            </div>

            <div class="checkbox" style="margin-top: 20px;">
                <label>
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" name="is_published" value="1" @checked((bool) old('is_published', $property->is_published))> Публиковать объект
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" value="1" @checked((bool) old('is_featured', $property->is_featured))> Показывать на главной
                </label>
            </div>
        </div>

        <div class="admin-form-card">
            <h2 style="margin-top: 0;">Изображения</h2>
            @if ($property->exists && $property->images->isNotEmpty())
                <p style="color:#667085;margin:0 0 12px;">Перетаскивайте фото за иконку ≡ чтобы изменить порядок. Кнопка ↻ поворачивает миниатюру на 90° по часовой стрелке.</p>
                <div id="admin-media-sortable" class="admin-media-grid" style="margin-bottom: 20px;">
                    @foreach ($property->images as $image)
                        @php($rotation = (int) old('image_rotations.' . $image->id, $image->rotation ?? 0))
                        <div class="admin-media-card" data-id="{{ $image->id }}" style="cursor:grab;">
                            <div style="color:#999;font-size:18px;cursor:grab;padding:0 0 4px;" title="Перетащить">☰</div>
                            <img src="{{ \App\Support\MediaPath::url($image->thumb_path ?: $image->path) }}" alt="{{ $image->alt ?: $property->title }}" data-rotation="{{ $rotation }}" style="transform:rotate({{ $rotation }}deg);">
                            <input type="hidden" name="image_order[]" value="{{ $image->id }}">
                            <input type="hidden" name="image_rotations[{{ $image->id }}]" value="{{ $rotation }}">
                            <div class="admin-media-card__actions" style="display:flex;gap:8px;align-items:center;margin-top:8px;">
                                <button type="button" class="btn btn-default btn-sm admin-rotate-btn" data-id="{{ $image->id }}" title="Повернуть на 90°">↻</button>
                                <span class="admin-media-status" style="display:none;font-size:11px;color:#667085;">готово</span>
                                <span style="font-size:11px;color:#999;">{{ $rotation }}°</span>
                            </div>
                            <div class="radio" style="margin-top: 0;">
                                <label>
                                    <input type="radio" name="cover_image_id" value="{{ $image->id }}" @checked((int) old('cover_image_id', optional($property->images->firstWhere('is_cover', true))->id) === $image->id)> Обложка
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="delete_images[]" value="{{ $image->id }}"> Удалить
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            <div class="form-group">
                <label for="images">Добавить фото</label>
                <input id="images" name="images[]" type="file" multiple accept="image/*">
            </div>
            <div id="admin-media-preview" class="admin-media-grid" style="margin-top:12px;"></div>
        </div>

        <div class="admin-actions">
            <button class="btn btn-primary" type="submit">Сохранить</button>
            <a class="btn btn-default" href="{{ route('admin.properties.index') }}">Отменить</a>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="https://api-maps.yandex.ru/2.1/?apikey=38c8767e-6b9b-452a-a56b-f7c8b927ea10&lang=ru_RU" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            ymaps.ready(initAdminMap);
        });

        var adminMapPlacemark = null;

        function initAdminMap() {
            var lat = parseFloat(document.getElementById('latitude').value);
            var lng = parseFloat(document.getElementById('longitude').value);
            var defaultCenter = [44.4136, 39.0935]; // Туапсе по умолчанию
            var center = (!isNaN(lat) && !isNaN(lng)) ? [lat, lng] : defaultCenter;

            var adminMap = new ymaps.Map('admin-map', {
                center: center,
                zoom: 14,
                controls: ['zoomControl', 'fullscreenControl']
            }, {
                searchControlProvider: 'yandex#search'
            });

            // Если есть координаты, ставим метку
            if (!isNaN(lat) && !isNaN(lng)) {
                adminMapPlacemark = new ymaps.Placemark(center, {
                    hintContent: '{{ addslashes($property->title) }}'
                }, {
                    preset: 'islands#redStretchyIcon',
                    draggable: true
                });
                adminMap.geoObjects.add(adminMapPlacemark);
            }

            // Клик по карте ставит метку
            adminMap.events.add('click', function (e) {
                var coords = e.get('coords');
                setPlacemark(coords[0], coords[1]);
            });

            // Перетаскивание метки
            if (adminMapPlacemark) {
                adminMapPlacemark.events.add('dragend', function () {
                    var coords = adminMapPlacemark.getGeometry().getCenter();
                    setPlacemark(coords[0], coords[1]);
                });
            }

            // Ручной ввод координат
            var latInput = document.getElementById('latitude');
            var lngInput = document.getElementById('longitude');

            latInput.addEventListener('change', function () {
                var newLat = parseFloat(this.value);
                var newLng = parseFloat(lngInput.value);
                if (!isNaN(newLat) && !isNaN(newLng)) {
                    setPlacemark(newLat, newLng);
                    adminMap.panTo([newLat, newLng], { flying: true });
                }
            });

            lngInput.addEventListener('change', function () {
                var newLat = parseFloat(latInput.value);
                var newLng = parseFloat(this.value);
                if (!isNaN(newLat) && !isNaN(newLng)) {
                    setPlacemark(newLat, newLng);
                    adminMap.panTo([newLat, newLng], { flying: true });
                }
            });

            // Очистить
            document.getElementById('admin-map-clear').addEventListener('click', function () {
                latInput.value = '';
                lngInput.value = '';
                if (adminMapPlacemark) {
                    adminMap.geoObjects.remove(adminMapPlacemark);
                    adminMapPlacemark = null;
                }
                adminMap.center = defaultCenter;
                adminMap.setZoom(14);
            });

            function setPlacemark(lat, lng) {
                latInput.value = lat.toFixed(7);
                lngInput.value = lng.toFixed(7);

                var coords = [lat, lng];
                if (adminMapPlacemark) {
                    adminMapPlacemark.geometry.setCoordinates(coords);
                    adminMapPlacemark.properties.set('hintContent', '{{ addslashes($property->title) }}');
                } else {
                    adminMapPlacemark = new ymaps.Placemark(coords, {
                        hintContent: '{{ addslashes($property->title) }}'
                    }, {
                        preset: 'islands#redStretchyIcon',
                        draggable: true
                    });
                    adminMap.geoObjects.add(adminMapPlacemark);
                    adminMapPlacemark.events.add('dragend', function () {
                        var c = adminMapPlacemark.getGeometry().getCenter();
                        setPlacemark(c[0], c[1]);
                    });
                }
            }
        }
    </script>
    <style>
        #admin-map {
            cursor: crosshair;
        }
        #admin-media-sortable .admin-media-card.sortable-ghost {
            opacity: 0.4;
            background: #e8f0fe;
        }
        #admin-media-sortable .admin-media-card.sortable-drag {
            box-shadow: 0 8px 24px rgba(0,0,0,0.18);
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
    <script>
        window.propertyId = {{ $property->id ?? 0 }};
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('admin-media-sortable');
            if (el) {
                Sortable.create(el, {
                    handle: '[title="Перетащить"]',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    dragClass: 'sortable-drag',
                });
            }

            var filesInput = document.getElementById('images');
            var previewBox = document.getElementById('admin-media-preview');
            if (filesInput && previewBox) {
                var uploadCounter = 0;
                var newFileIndex = 0;
                filesInput.addEventListener('change', function () {
                    var files = Array.prototype.slice.call(this.files || []);
                    if (files.length === 0) {
                        return;
                    }
                    this.value = '';
                    files.forEach(function (file) {
                        uploadCounter += 1;
                        var idx = newFileIndex++;
                        var tmpKey = 'tmp_' + Date.now() + '_' + uploadCounter;
                        var card = document.createElement('div');
                        card.className = 'admin-media-card';
                        card.dataset.tmpKey = tmpKey;
                        card.innerHTML =
                            '<div style="color:#999;font-size:18px;cursor:grab;padding:0 0 4px;" title="Перетащить">☰</div>' +
                            '<img src="" alt="' + (file.name || '') + '" data-rotation="0" style="transform:rotate(0deg);">' +
                            '<input type="hidden" name="image_rotations_file[' + idx + ']" value="0">' +
                            '<div class="admin-media-card__actions" style="display:flex;gap:8px;align-items:center;margin-top:8px;">' +
                                '<button type="button" class="btn btn-default btn-sm admin-rotate-btn" title="Повернуть на 90°">↻</button>' +
                                '<span class="admin-media-status" style="display:inline-block;font-size:11px;color:#f59e0b;">ожидает загрузки</span>' +
                            '</div>' +
                            '<div class="admin-media-card__progress" style="margin-top:8px;">' +
                                '<div class="admin-media-progress-bar" style="height:4px;background:#e5e7eb;border-radius:2px;overflow:hidden;">' +
                                    '<div class="admin-media-progress" style="height:100%;width:0%;background:#3b82f6;"></div>' +
                                '</div>' +
                            '</div>';
                        previewBox.appendChild(card);

                        var cardImg = card.querySelector('img');
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            if (e && e.target) {
                                cardImg.src = e.target.result;
                            }
                        };
                        reader.readAsDataURL(file);

                        if (window.propertyId && window.propertyId > 0) {
                            uploadFile(file, tmpKey, card);
                        } else {
                            var status = card.querySelector('.admin-media-status');
                            if (status) {
                                status.textContent = 'будет загружено при сохранении';
                            }
                            if (card.querySelector('.admin-media-progress-bar')) {
                                card.querySelector('.admin-media-progress-bar').style.display = 'none';
                            }
                        }
                    });
                });

                function uploadFile(file, tmpKey, card) {
                    var xhr = new XMLHttpRequest();
                    var formData = new FormData();
                    formData.append('file', file);

                    var token = document.querySelector('meta[name="csrf-token"]')
                        ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        : (document.querySelector('input[name="_token"]') ? document.querySelector('input[name="_token"]').value : '');

                    xhr.open('POST', '/admin/properties/' + (window.propertyId || 0) + '/image-upload', true);
                    if (token) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', token);
                    }

                    xhr.upload.addEventListener('progress', function (e) {
                        if (e.lengthComputable) {
                            var percent = Math.round((e.loaded / e.total) * 100);
                            var bar = card.querySelector('.admin-media-progress');
                            if (bar) {
                                bar.style.width = percent + '%';
                            }
                        }
                    });

                    xhr.onreadystatechange = function () {
                        if (xhr.readyState !== 4) {
                            return;
                        }
                        var status = card.querySelector('.admin-media-status');
                        var bar = card.querySelector('.admin-media-progress');
                        if (xhr.status === 200) {
                            var response = null;
                            try {
                                response = JSON.parse(xhr.responseText);
                            } catch (err) {
                                response = null;
                            }
                            if (response && response.id) {
                                card.dataset.id = response.id;
                                var imgEl = card.querySelector('img');
                                if (response.thumb) {
                                    imgEl.src = response.thumb;
                                }
                                var input = card.querySelector('input[type="hidden"]');
                                if (input) {
                                    var preserved = parseInt(imgEl.getAttribute('data-rotation') || input.value || '0', 10);
                                    input.name = 'image_rotations[' + response.id + ']';
                                    input.value = String(preserved);
                                    imgEl.setAttribute('data-rotation', preserved);
                                }
                                if (status) {
                                    status.textContent = 'загружено';
                                    status.style.color = '#16a34a';
                                }
                                if (bar) {
                                    bar.parentElement.style.display = 'none';
                                }
                            } else {
                                if (status) {
                                    status.textContent = 'ошибка загрузки';
                                    status.style.color = '#dc2626';
                                }
                            }
                        } else {
                            if (status) {
                                status.textContent = 'ошибка загрузки';
                                status.style.color = '#dc2626';
                            }
                        }
                    };

                    xhr.onerror = function () {
                        var status = card.querySelector('.admin-media-status');
                        if (status) {
                            status.textContent = 'ошибка загрузки';
                            status.style.color = '#dc2626';
                        }
                    };

                    xhr.send(formData);
                }

                previewBox.addEventListener('click', function (e) {
                    var btn = e.target.closest('.admin-rotate-btn');
                    if (!btn) {
                        return;
                    }
                    var card = btn.closest('.admin-media-card');
                    if (!card) {
                        return;
                    }
                    var img = card.querySelector('img');
                    var hidden = card.querySelector('input[type="hidden"]');
                    if (!img || !hidden) {
                        return;
                    }
                    var current = parseInt(img.getAttribute('data-rotation') || hidden.value || '0', 10);
                    var next = (current + 90) % 360;
                    img.setAttribute('data-rotation', next);
                    img.style.transform = 'rotate(' + next + 'deg)';
                    hidden.value = String(next);
                    var span = card.querySelector('.admin-media-status');
                    if (span) {
                        span.textContent = 'готово';
                        span.style.color = '#16a34a';
                    }
                });
            }
        });
    </script>
@endpush
