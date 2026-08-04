# Realty Tuapse Platform

Новый управляемый сайт для `realty-tuapse.ru`, собранный на Laravel на основе локальной копии старого публичного сайта.

Этот файл нужен не как “общий README Laravel”, а как практическая инструкция по выкладке именно этого проекта.

Короткий предрелизный список действий вынесен отдельно в [PRE_RELEASE_CHECKLIST.md](/C:/Users/thetr/Desktop/Realty_Tuapse/platform/PRE_RELEASE_CHECKLIST.md).

## 1. Что это за проект

- Backend: Laravel 13
- PHP: `^8.3`
- База данных для production: `MySQL` или `MariaDB`
- Публичная часть: серверный рендер, legacy-структура URL сохранена
- Админка: `/admin`
- Почта: SMTP
- Локально проект может работать на `sqlite`, но для сервера рекомендуется `mysql`

## 2. Что должно быть на сервере

Минимально:

- PHP `8.3+`
- Composer `2+`
- MySQL `8+` или MariaDB `10.6+`
- веб-сервер `Apache` или `Nginx`

Рекомендуемые PHP-расширения:

- `ctype`
- `fileinfo`
- `json`
- `mbstring`
- `openssl`
- `PDO`
- `pdo_mysql`
- `tokenizer`
- `xml`
- `gd` (recommended for automatic thumbnail generation on new uploads)

## 3. Что именно заливать на сервер

Заливается весь проект из папки `platform`, кроме локального мусора.

### Что нужно на сервере

- `app/`
- `bootstrap/`
- `config/`
- `database/`
- `public/`
- `resources/`
- `routes/`
- `storage/`
- `vendor/` если на сервере нет Composer
- `artisan`
- `composer.json`
- `composer.lock`
- `.env` после настройки production

### Что не нужно переносить как есть

- локальный `.env`, если он уже настроен под ноутбук
- `database/database.sqlite`, если production будет на MySQL
- локальные логи из `storage/logs`
- временные кэши из `bootstrap/cache`, кроме служебных файлов фреймворка
- `node_modules`, если они есть локально
- `start-local.ps1`
- `start-local.bat`

## 4. Рекомендуемая структура на сервере

Лучший вариант:

- проект лежит вне публичной web-корневой папки
- document root сайта указывает на папку `public`

Пример:

```text
/var/www/realty-tuapse/platform
/var/www/realty-tuapse/platform/public
```

Document root:

```text
/var/www/realty-tuapse/platform/public
```

Это самый правильный и безопасный вариант.

## 5. Если хостинг не дает указать document root на `public`

Запасной вариант для shared hosting:

1. Залить весь Laravel-проект вне `public_html`, например:

```text
/home/account/realty-platform
```

2. Содержимое папки `public/` выложить в `public_html/`.

3. В `public_html/index.php` поправить пути:

```php
require __DIR__.'/../realty-platform/vendor/autoload.php';
$app = require_once __DIR__.'/../realty-platform/bootstrap/app.php';
```

4. Убедиться, что `public_html/storage` указывает на `../realty-platform/storage/app/public`.

Если хостинг запрещает symbolic links, это нужно отдельно уточнить у поддержки хостинга.

## 6. Первичная выкладка на чистый сервер

Пример для Linux-сервера:

```bash
cd /var/www/realty-tuapse/platform
composer install --no-dev --optimize-autoloader
cp .env.production.example .env
php artisan key:generate --force
php artisan storage:link
php artisan migrate --seed --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Если вы не можете запускать Composer на сервере:

1. выполните `composer install --no-dev --optimize-autoloader` локально;
2. загрузите на сервер уже готовую папку `vendor`.

## 7. Production `.env`

Ниже минимальный шаблон для production:

```env
APP_NAME="АН Туапсе"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://realty-tuapse.ru

APP_LOCALE=ru
APP_FALLBACK_LOCALE=ru
APP_FAKER_LOCALE=ru_RU

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=realty_tuapse
DB_USERNAME=realty_user
DB_PASSWORD=change-me

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=.realty-tuapse.ru

FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
CACHE_STORE=file

MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_TIMEOUT=15
MAIL_EHLO_DOMAIN=realty-tuapse.ru
MAIL_USERNAME=tuapse-tuapse@mail.ru
MAIL_PASSWORD=change-me
MAIL_FROM_ADDRESS="tuapse-tuapse@mail.ru"
MAIL_FROM_NAME="${APP_NAME}"
MAIL_TEST_TO="tuapse-tuapse@mail.ru"

REALTY_CONTACT_EMAIL="tuapse-tuapse@mail.ru"
REALTY_SUPPORT_EMAIL="tuapse-tuapse@mail.ru"
```

Готовый файл с этим шаблоном уже лежит в проекте:

- `.env.production.example`

## 8. Права доступа

Веб-сервер должен иметь право записи в:

- `storage/`
- `bootstrap/cache/`

Обычно на Linux достаточно:

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

Если у вас другой пользователь веб-сервера, замените `www-data` на своего.

## 9. Миграции и сиды

### Первый запуск на пустой базе

```bash
php artisan migrate --seed --force
```

Это создаст стартовые данные, сотрудников, объекты и контент.

### Важно после первого сидирования

В production demo-пользователи теперь не создаются по умолчанию.

Они создаются автоматически только в `local/testing`, либо если вы явно зададите:

```env
REALTY_SEED_DEMO_USERS=true
```

Если вы сознательно включили demo-пользователей, сидер создаст логины:

- `shlyakhov@realty-tuapse.local`
- `skrypnik@realty-tuapse.local`
- `evseeva@realty-tuapse.local`

Их нужно:

1. либо сразу заменить на реальные учетные записи;
2. либо сменить email и пароль;
3. либо удалить после настройки прод-аккаунтов.

### Создание реального пользователя для production

После выкладки можно создать или обновить боевой аккаунт так:

```bash
php artisan realty:create-user admin@example.com StrongPassword123 "Имя Пользователя" admin --employee=alexander-vladimirovich-shlyakhov
```

Для сотрудника:

```bash
php artisan realty:create-user employee@example.com StrongPassword123 "Имя Сотрудника" employee --employee=natalya-valentinovna-evseeva
```

### Обновление уже работающего сайта

Если сайт уже развернут и база не пустая:

```bash
php artisan migrate --force
```

На обновлениях не запускайте `--seed` без необходимости.

## 10. Символическая ссылка на загруженные файлы

Проект использует:

- `public/legacy` для перенесенных старых ассетов;
- `storage/app/public` для новых загружаемых файлов.

Нужно выполнить:

```bash
php artisan storage:link
```

Без этого новые фото из админки не будут открываться по HTTP.

## 11. Почта и проверка SMTP

Для боевого сайта используйте только:

```env
MAIL_MAILER=smtp
```

Не используйте в production:

- `MAIL_MAILER=log`
- `MAIL_MAILER=array`
- `MAIL_MAILER=failover`, если fallback ведет в `log` или `array`

### Проверка почты через CLI

```bash
php artisan realty:mail-test
```

Или на конкретный адрес:

```bash
php artisan realty:mail-test you@example.com
```

### Проверка почты через админку

- войдите в `/admin`
- откройте дашборд
- используйте блок `Почтовая отправка`
- отправьте тестовое письмо

Если SMTP не настроен, проект покажет понятную причину и не будет делать вид, что письмо ушло.

## 12. Проверка медиа перед открытием сайта

Перед запуском на production полезно проверить все пути к фотографиям и загруженным файлам:

```bash
php artisan realty:media-audit
```

Команда проверяет:

- фото сотрудников;
- изображения новостей;
- обложки и элементы галереи;
- фотографии объектов и их миниатюры.

Если команда находит `MISSING` или `BROKEN`, такие записи нужно исправить до открытия сайта.

Для быстрого предрелизного прогона можно использовать и общую команду:

```bash
composer run preflight
```

Она последовательно запускает:

- `php artisan realty:media-audit`
- `php artisan realty:smoke-check`

## 13. Кэширование и финальные команды перед открытием сайта

После настройки `.env`, БД и SMTP:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Если меняли только код и шаблоны, этого достаточно.

## 14. Быстрый smoke-check после выкладки

Проверьте:

1. `/`
2. `/contacts`
3. `/search`
4. `/news`
5. `/informaciya/fotogalereya`
6. `/property/prodam-3-komkvartiru-centr`
7. `/contact-us`
8. `/callback`
9. `/booking/request`
10. `/login`
11. `/admin`

Отдельно проверьте:

- `php artisan realty:media-audit`;
- загрузку фото через админку;
- отправку тестового письма;
- отправку формы сообщения по объекту;
- `storage`-файлы по прямым ссылкам;
- старые legacy URL, если они критичны для SEO.

## 15. Сценарий обновления уже выложенного сайта

Стандартный порядок:

```bash
cd /var/www/realty-tuapse/platform
php artisan down
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan up
```

После этого:

```bash
php artisan realty:mail-test
```

И затем короткий ручной smoke-check в браузере.

## 16. Нужен ли Node / npm

Для текущей production-версии сайта отдельная сборка front-end не является обязательной частью запуска.

Сайт использует перенесенные legacy-ассеты из `public/legacy`, а основная публичная часть рендерится сервером.

Если вы не дорабатываете отдельные Vite-ресурсы вручную, `npm install` и `npm run build` можно не делать.

## 17. Если после выкладки что-то сломалось

### Ошибка 500

Проверить:

- `.env`
- права на `storage` и `bootstrap/cache`
- логи в `storage/logs/laravel.log`

### Не открываются новые фото

Проверить:

- `php artisan storage:link`
- `php artisan realty:media-audit`
- наличие файлов в `storage/app/public`
- права на `storage`

### Не отправляется почта

Проверить:

- SMTP-логин и пароль
- `MAIL_HOST`, `MAIL_PORT`, `MAIL_SCHEME`
- `MAIL_FROM_ADDRESS`
- `php artisan realty:mail-test`

### После деплоя не видны изменения

Выполнить:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 18. Короткая версия деплоя

Если совсем кратко, то для первого запуска на сервере:

```bash
cd /var/www/realty-tuapse/platform
composer install --no-dev --optimize-autoloader
cp .env.production.example .env
php artisan key:generate --force
php artisan storage:link
php artisan migrate --seed --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan realty:media-audit
php artisan realty:mail-test
```

После этого:

- проверить `/admin`
- сменить тестовые учетные записи
- пройтись по формам и загрузке изображений

## 19. Локальные artisan-команды на Windows

Если на ноутбуке в `PATH` несколько версий PHP или не хватает нужных расширений в стандартном `php`, используйте проектный wrapper:

```powershell
.\artisan-local.ps1 realty:media-audit
.\artisan-local.ps1 realty:smoke-check
.\artisan-local.ps1 migrate --seed
```

Или через `.bat`:

```bat
artisan-local.bat realty:media-audit
```

Эти скрипты запускают `artisan` через тот же `php.exe` и `php.ini`, которые используются в `start-local.ps1`.
