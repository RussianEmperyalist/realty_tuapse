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
                    <small style="color:#667085;">Этот номер видят посетители сайта.</small>
                </div>
                @if (auth()->user()?->isStaff())
                    <div class="admin-grid--full">
                        <div class="admin-internal-field">
                            <label for="owner_phone">Телефон собственника</label>
                            <p>Внутреннее поле для администраторов и менеджеров. На сайте, в карточке объявления и в публичном JSON не отображается.</p>
                            <input class="form-control" id="owner_phone" name="owner_phone" type="tel" autocomplete="off" value="{{ old('owner_phone', $property->owner_phone) }}" placeholder="+7 …">
                        </div>
                    </div>
                @endif
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

        <div class="admin-form-card" id="admin-property-images">
            <h2 style="margin-top: 0;">Изображения</h2>
            <p style="color:#667085;margin:0 0 12px;">Перетаскивайте фото за иконку ☰, чтобы изменить порядок. Кнопка ↻ на миниатюре поворачивает снимок на 90° по часовой стрелке. Статус под фото показывает, ушёл ли файл на сервер.</p>
            @if ($property->exists)
                <div id="admin-media-sortable" class="admin-media-grid" style="margin-bottom: 20px;">
                    @foreach ($property->images as $image)
                        @php($rotation = (int) old('image_rotations.' . $image->id, $image->rotationDegrees()))
                        <div class="admin-media-card is-ready" data-id="{{ $image->id }}">
                            <div class="admin-media-card__drag" title="Перетащить">☰</div>
                            <div class="admin-media-card__thumb">
                                <img src="{{ \App\Support\MediaPath::url($image->thumb_path ?: $image->path) }}" alt="{{ $image->alt ?: $property->title }}" data-rotation="{{ $rotation }}" style="transform:rotate({{ $rotation }}deg);">
                                <button type="button" class="admin-rotate-btn" title="Повернуть на 90° по часовой стрелке" aria-label="Повернуть на 90 градусов">↻</button>
                                <div class="admin-media-card__overlay" aria-hidden="true">
                                    <span class="admin-media-spinner"></span>
                                    <span class="admin-media-overlay-text">Загрузка…</span>
                                </div>
                                <div class="admin-media-progress-bar"><div class="admin-media-progress"></div></div>
                            </div>
                            <input type="hidden" name="image_order[]" value="{{ $image->id }}">
                            <input type="hidden" name="image_rotations[{{ $image->id }}]" value="{{ $rotation }}">
                            <div class="admin-media-card__meta">
                                <span class="admin-media-status-badge is-done">Загружено</span>
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
        window.propertyImageUploadUrl = @json($property->exists ? route('admin.properties.image-upload', $property) : '');
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var mediaRoot = document.getElementById('admin-property-images');
            var el = document.getElementById('admin-media-sortable');
            if (el) {
                Sortable.create(el, {
                    handle: '.admin-media-card__drag',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    dragClass: 'sortable-drag',
                });
            }

            var filesInput = document.getElementById('images');
            var previewBox = document.getElementById('admin-media-preview');
            var form = mediaRoot ? mediaRoot.closest('form') : null;
            var pendingFiles = [];
            var activeUploads = 0;
            var newFileIndex = 0;
            var canUploadNow = !!(window.propertyId && window.propertyId > 0 && window.propertyImageUploadUrl);
            var isSyncingFiles = false;

            function setStatus(card, text, state) {
                var badge = card.querySelector('.admin-media-status-badge');
                if (!badge) {
                    return;
                }
                badge.textContent = text;
                badge.className = 'admin-media-status-badge is-' + state;
            }

            function setProgress(card, percent) {
                var bar = card.querySelector('.admin-media-progress');
                var overlayText = card.querySelector('.admin-media-overlay-text');
                if (bar) {
                    bar.style.width = Math.max(0, Math.min(100, percent)) + '%';
                }
                if (overlayText) {
                    overlayText.textContent = percent >= 100 ? 'Обработка…' : ('Загрузка… ' + percent + '%');
                }
            }

            function applyRotation(card, angle) {
                var img = card.querySelector('.admin-media-card__thumb img');
                var hidden = card.querySelector('input[name^="image_rotations"]');
                var next = ((parseInt(angle, 10) || 0) % 360 + 360) % 360;
                if (img) {
                    img.setAttribute('data-rotation', String(next));
                    img.style.transform = 'rotate(' + next + 'deg)';
                }
                if (hidden) {
                    hidden.value = String(next);
                }
            }

            function syncPendingFiles() {
                if (!filesInput || typeof DataTransfer === 'undefined') {
                    return;
                }
                var dt = new DataTransfer();
                pendingFiles.forEach(function (file) {
                    dt.items.add(file);
                });
                isSyncingFiles = true;
                filesInput.files = dt.files;
                isSyncingFiles = false;
            }

            function csrfToken() {
                var meta = document.querySelector('meta[name="csrf-token"]');
                if (meta) {
                    return meta.getAttribute('content') || '';
                }
                var input = document.querySelector('input[name="_token"]');
                return input ? input.value : '';
            }

            function attachServerControls(card, imageId) {
                card.dataset.id = imageId;
                var order = document.createElement('input');
                order.type = 'hidden';
                order.name = 'image_order[]';
                order.value = String(imageId);
                card.appendChild(order);

                var coverWrap = document.createElement('div');
                coverWrap.className = 'radio';
                coverWrap.style.marginTop = '0';
                coverWrap.innerHTML = '<label><input type="radio" name="cover_image_id" value="' + imageId + '"> Обложка</label>';
                card.appendChild(coverWrap);

                var deleteWrap = document.createElement('div');
                deleteWrap.className = 'checkbox';
                deleteWrap.innerHTML = '<label><input type="checkbox" name="delete_images[]" value="' + imageId + '"> Удалить</label>';
                card.appendChild(deleteWrap);
            }

            function uploadFile(file, card) {
                var xhr = new XMLHttpRequest();
                var formData = new FormData();
                formData.append('file', file);
                var finished = false;

                activeUploads += 1;
                card.classList.add('is-uploading');
                card.classList.remove('is-ready', 'is-error', 'is-pending');
                setStatus(card, 'Загрузка… 0%', 'uploading');
                setProgress(card, 0);

                function finishUpload() {
                    if (finished) {
                        return;
                    }
                    finished = true;
                    activeUploads = Math.max(0, activeUploads - 1);
                    card.classList.remove('is-uploading');
                }

                xhr.open('POST', window.propertyImageUploadUrl, true);
                var token = csrfToken();
                if (token) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', token);
                }

                xhr.upload.addEventListener('progress', function (e) {
                    if (!e.lengthComputable) {
                        return;
                    }
                    var percent = Math.round((e.loaded / e.total) * 100);
                    setProgress(card, percent);
                    setStatus(card, 'Загрузка… ' + percent + '%', 'uploading');
                });

                xhr.onreadystatechange = function () {
                    if (xhr.readyState !== 4) {
                        return;
                    }
                    finishUpload();

                    var response = null;
                    try {
                        response = JSON.parse(xhr.responseText);
                    } catch (err) {
                        response = null;
                    }

                    if (xhr.status === 200 && response && response.id) {
                        var imgEl = card.querySelector('.admin-media-card__thumb img');
                        var hidden = card.querySelector('input[name^="image_rotations"]');
                        var preserved = parseInt((imgEl && imgEl.getAttribute('data-rotation')) || (hidden && hidden.value) || '0', 10);
                        if (response.thumb && imgEl) {
                            imgEl.src = response.thumb;
                        }
                        if (hidden) {
                            hidden.name = 'image_rotations[' + response.id + ']';
                            hidden.value = String(preserved || 0);
                        }
                        applyRotation(card, preserved || 0);
                        attachServerControls(card, response.id);
                        card.classList.add('is-ready');
                        setStatus(card, 'Загружено', 'done');
                        setProgress(card, 100);

                        if (el && card.parentNode !== el) {
                            el.appendChild(card);
                        }
                    } else {
                        card.classList.add('is-error');
                        setStatus(card, 'Ошибка загрузки', 'error');
                    }
                };

                xhr.onerror = function () {
                    finishUpload();
                    card.classList.add('is-error');
                    setStatus(card, 'Ошибка загрузки', 'error');
                };

                xhr.send(formData);
            }

            if (filesInput && previewBox) {
                filesInput.addEventListener('change', function () {
                    if (isSyncingFiles) {
                        return;
                    }
                    var files = Array.prototype.slice.call(this.files || []);
                    if (files.length === 0) {
                        return;
                    }

                    files.forEach(function (file) {
                        var idx = newFileIndex++;
                        var card = document.createElement('div');
                        card.className = 'admin-media-card is-pending';
                        card.innerHTML =
                            '<div class="admin-media-card__drag" title="Перетащить">☰</div>' +
                            '<div class="admin-media-card__thumb">' +
                                '<img src="" alt="">' +
                                '<button type="button" class="admin-rotate-btn" title="Повернуть на 90° по часовой стрелке" aria-label="Повернуть на 90 градусов">↻</button>' +
                                '<div class="admin-media-card__overlay" aria-hidden="true">' +
                                    '<span class="admin-media-spinner"></span>' +
                                    '<span class="admin-media-overlay-text">Загрузка…</span>' +
                                '</div>' +
                                '<div class="admin-media-progress-bar"><div class="admin-media-progress"></div></div>' +
                            '</div>' +
                            '<input type="hidden" name="image_rotations_file[' + idx + ']" value="0">' +
                            '<div class="admin-media-card__meta">' +
                                '<span class="admin-media-status-badge is-pending">Ожидает загрузки</span>' +
                            '</div>';
                        previewBox.appendChild(card);

                        var cardImg = card.querySelector('img');
                        if (cardImg) {
                            cardImg.alt = file.name || '';
                        }
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            if (e && e.target && cardImg) {
                                cardImg.src = e.target.result;
                            }
                        };
                        reader.readAsDataURL(file);

                        if (canUploadNow) {
                            uploadFile(file, card);
                        } else {
                            pendingFiles.push(file);
                            setStatus(card, 'Будет загружено при сохранении', 'pending');
                        }
                    });

                    this.value = '';
                    if (!canUploadNow) {
                        syncPendingFiles();
                    }
                });
            }

            if (mediaRoot) {
                mediaRoot.addEventListener('click', function (e) {
                    var btn = e.target.closest('.admin-rotate-btn');
                    if (!btn || btn.disabled) {
                        return;
                    }
                    var card = btn.closest('.admin-media-card');
                    if (!card) {
                        return;
                    }
                    var img = card.querySelector('.admin-media-card__thumb img');
                    var current = parseInt((img && img.getAttribute('data-rotation')) || '0', 10);
                    applyRotation(card, current + 90);
                });
            }

            if (form) {
                form.addEventListener('submit', function (e) {
                    if (activeUploads > 0) {
                        e.preventDefault();
                        window.alert('Дождитесь окончания загрузки фотографий.');
                        return;
                    }
                    if (!canUploadNow) {
                        syncPendingFiles();
                    }
                });
            }
        });
    </script>
@endpush
