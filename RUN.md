# Быстрый запуск (портативная папка)

Папка `platform` самодостаточна: код, фото, база и настройки лежат внутри неё.
Можно переносить папку на любой диск/устройство и запускать через Podman или Docker.

## Требования
- Установленный **Podman** (или Docker).
- Интернет для первой сборки образа (качаются PHP, npm, composer).

## Запуск

1. Открой терминал и перейди в папку `platform`:

```bat
cd /d "путь\до\platform"
```

2. **Первый запуск на устройстве** (собирает образ):

```bat
podman compose up -d --build
```

3. **Последующие запуски** (образ уже есть):

```bat
podman compose up -d
```

4. Открой сайт: **http://localhost:8080**

## Админка
- URL: **http://localhost:8080/admin**
- Логин: `shlyakhov@realty-tuapse.local`
- Пароль: `ChangeMe2026!`

> Примечание: команда `migrate:fresh --seed --force` сбрасывает пароль админа на `password`.
> После неё нужно восстановить пароль (см. ниже).

## Остановка
```bat
podman compose stop
```

## Что хранится в папке (и не теряется)
| Что | Где |
|---|---|
| Фото сайта | `public/legacy/` |
| База данных | `database/database.sqlite` |
| Настройки (включая APP_KEY) | `.env` |
| Legacy-контент для сидера | `legacy-source/` |
| Загруженные через админку фото | `storage/app/public/` |

## Важно
- Контейнер хранит пути относительно расположения папки. Всегда запускай `podman compose` **из папки `platform`**.
- При переносе папки контейнер пересоздаётся сам командой `podman compose up -d` — данные сохраняются, т.к. они внутри папки.
- На новом устройстве нужен `--build` (образ собирается локально, в папке его нет).

## Docker вместо Podman
Если установлен Docker, команды те же:

```bat
docker compose up -d --build
docker compose up -d
docker compose stop
```

## Восстановить пароль админа (после migrate:fresh --seed)
Создай файл `storage/reset_password.php`:

```php
<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$user = \App\Models\User::where('email', 'shlyakhov@realty-tuapse.local')->first();
$user->password = \Illuminate\Support\Facades\Hash::make('ChangeMe2026!');
$user->save();
echo "OK\n";
```

Выполни внутри контейнера:

```bat
podman exec realty-tuapse-app php /var/www/storage/reset_password.php
```
