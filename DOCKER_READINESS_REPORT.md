# Docker Readiness Report

## Итоговая оценка после исправлений: **✅ Готово к развертыванию**

---

## Исправленные проблемы

| Проблема | Статус | Исправление |
|---|---|---|
| **Нет `.dockerignore`** | ✅ Исправлен | Создан `.dockerignore` — исключены vendor/, node_modules/, .env, логи, кеши и др. |
| **Не собирается фронтенд** | ✅ Исправлен | В Dockerfile добавлены `npm ci && npm run build` + установка Node.js 22 |
| **`composer install` без `--no-dev`** | ✅ Исправлен | Заменен на `composer install --no-dev --optimize-autoloader` |
| **APP_KEY пустой** | ✅ Исправлен | В Dockerfile добавлен `php artisan key:generate --force` |
| **`storage:link` в build-time** | ✅ Исправлен | Вынесен в `docker/entrypoint.sh` — выполняется при старте контейнера |
| **Volume `.:/var/www` перезаписывал vendor/** | ✅ Исправлен | Заменен на точечный mount только `./storage:/var/www/storage` |
| **DB_HOST=127.0.0.1 в .env.production.example** | ✅ Исправлен | Добавлен комментарий о замене на `mysql` для Docker |
| **Нет `php artisan optimize`** | ✅ Исправлен | Добавлены `config:cache`, `route:cache`, `view:cache` в Dockerfile |
| **Не было Node.js в образе** | ✅ Исправлен | Установлен Node.js 22 через nodesource |

---

## Текущее состояние файлов

### `.dockerignore`
Исключает: `.env`, `vendor/`, `node_modules/`, `public/build/`, `storage/**`, логи, кеши, `bootstrap/cache/*.php`, Docker-файлы, git-файлы, локальные артефакты.

### `Dockerfile`
- Базовый образ `php:8.3-fpm`
- Все PHP-расширения: pdo_mysql, mbstring, exif, pcntl, bcmath, gd, zip, intl
- Composer из отдельного образа
- **Node.js 22** для сборки Vite-фронтенда
- `npm ci && npm run build` с кешированием слоев (package.json копируется до COPY .)
- `composer install --no-dev --optimize-autoloader`
- `php artisan key:generate && config:cache && route:cache && view:cache`
- `chown` и `chmod` для storage и bootstrap/cache
- Entrypoint: `docker/entrypoint.sh`

### `docker/entrypoint.sh`
Выполняет `php artisan storage:link --force --quiet` при каждом старте контейнера, затем запускает основной процесс (`php-fpm`).

### `docker-compose.yml`
- **app**: сборка из Dockerfile, mount только `./storage:/var/www/storage`, зависит от mysql (healthcheck)
- **nginx**: nginx:alpine, mount `./public:/var/www/public` + `./docker/nginx/default.conf`, порт 8080
- **mysql**: mysql:8.0, healthcheck, порт 3307
- **mailpit**: для тестирования почты, порты 8025/1025
- Сеть: `realty-network` bridge

### `.env.docker`
Уже корректный для Docker: `DB_HOST=mysql`, `MAIL_HOST=mailpit`.

### `.env.production.example`
Добавлен комментарий о необходимости заменить `DB_HOST=127.0.0.1` на `mysql` для Docker.

---

## Команда для развертывания

```bash
# 1. Создать .env из шаблона для Docker
cp .env.docker .env

# 2. Собрать и запустить
docker compose up -d --build

# 3. Выполнить миграции
docker compose exec app php artisan migrate --force

# 4 (опционально). Наполнить демо-данными
docker compose exec app php artisan db:seed --force

# 5. Проверить
curl http://localhost:8080
```

---

**Сгенерировано:** 26.05.2026  
**Статус:** ✅ Все критические и средние проблемы исправлены