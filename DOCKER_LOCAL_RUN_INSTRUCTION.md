# 🚀 Инструкция по запуску проекта в Docker локально

## Предварительные требования

Убедитесь, что установлены:
- ✅ **Docker Desktop** (версия 4.20 или выше)
- ✅ **Docker Compose** (включён в Docker Desktop)

**Проверка:**
```powershell
docker --version
docker-compose --version
```

---

## 1️⃣ Запуск проекта

### Шаг 1: Откройте PowerShell в папке проекта
```powershell
cd C:\Users\YourUser\Desktop\Realty_Tuapse\platform
```

### Шаг 2: Остановите и пересоберите контейнеры (если нужно)
```powershell
docker-compose down
```

### Шаг 3: Запустите проект
```powershell
docker-compose up --build -d
```

### Шаг 4: Дождитесь запуска (60-90 секунд)
```powershell
Start-Sleep -Seconds 60
docker-compose ps
```

**Ожидаемый результат:**
```
NAME                    STATUS
realty-tuapse-app       Up (healthy)
realty-tuapse-nginx     Up (healthy)
realty-tuapse-mysql     Up (healthy)
realty-tuapse-mailpit   Up (healthy)
```

---

## 2️⃣ Доступы к проекту

| Ресурс | URL/Данные |
|--------|------------|
| **Главный сайт** | `http://localhost:8080` |
| **Админ-панель** | `http://localhost:8080/admin` |
| **Mailpit (письма)** | `http://localhost:8025` |
| **MySQL (локально)** | `localhost:3307` |

### 🔐 Логин в админку:

**Доступные аккаунты:**

| Пользователь | Email | Роль | Пароль |
|--------------|-------|------|--------|
| Шляхов Александр В. | `shlyakhov@realty-tuapse.local` | admin | `admin123` |
| Скрипник Ирина А. | `skrypnik@realty-tuapse.local` | admin | `admin123` |
| Евсеева Наталья В. | `evseeva@realty-tuapse.local` | employee | `admin123` |

> ⚠️ **Если пароль не подходит, сбросьте его командой:**
> ```powershell
> echo "App\Models\User::where('email', 'shlyakhov@realty-tuapse.local')->first()->update(['password' => bcrypt('admin123')]);" | docker-compose exec -T app php artisan tinker
> ```

---

## 3️⃣ Проверка работы

### Проверка главной страницы:
```powershell
(Invoke-WebRequest -Uri "http://localhost:8080" -UseBasicParsing).StatusCode
# Ожидаем: 200
```

### Проверка админки:
```powershell
(Invoke-WebRequest -Uri "http://localhost:8080/admin" -UseBasicParsing).StatusCode
# Ожидаем: 302 (редирект на логин) или 200 (если уже авторизованы)
```

### Просмотр логов:
```powershell
# Все контейнеры
docker-compose logs -f --tail=50

# Только nginx
docker-compose logs -f nginx

# Только PHP
docker-compose logs -f app

# Только база данных
docker-compose logs -f mysql
```

---

## 4️⃣ Управление контейнерами

| Действие | Команда |
|----------|---------|
| **Остановить** | `docker-compose down` |
| **Перезапустить** | `docker-compose restart` |
| **Остановить и удалить тома** | `docker-compose down -v` |
| **Посмотреть статус** | `docker-compose ps` |
| **Посмотреть ресурсы** | `docker stats` |

---

## 5️⃣ Админ-панель: что проверить

После входа в `http://localhost:8080/admin`:

1. **Dashboard** (`/admin`) — сводка по объектам
2. **Объекты недвижимости** (`/admin/properties`) — список, создание, редактирование
3. **Новости** (`/admin/news`) — управление новостными постами
4. **Сотрудники** (`/admin/employees`) — управление менеджерами
5. **Фотогалерея** (`/admin/gallery`) — управление галереей
6. **Входящие заявки** (`/admin/inquiries`) — формы обратной связи

---

## 6️⃣ Распространённые проблемы и решения

### ❌ Контейнеры не запускаются
```powershell
docker-compose logs app
# Проверьте ошибки в логах
```

### ❌ Ошибка подключения к БД
```powershell
docker-compose restart mysql
Start-Sleep -Seconds 10
docker-compose restart app
```

### ❌ Сайт не загружается (404)
```powershell
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear
```

### ❌ Проблемы с правами доступа
```powershell
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### ❌ Ошибки в CSS/JS
Очистите кэш браузера (Ctrl+Shift+R) или откройте в режиме инкогнито.

---

## 7️⃣ Полезные команды для отладки

### Проверка базы данных:
```powershell
docker-compose exec mysql mysql -urealty_user -prealty_password realty_tuapse
```

### Перезапуск PHP-FPM:
```powershell
docker-compose restart app
```

### Проверка .env внутри контейнера:
```powershell
docker-compose exec app cat .env
```

### Доступ к PHP-консоли:
```powershell
docker-compose exec app php artisan tinker
```

### Проверка дискового пространства:
```powershell
docker system df
```

---

## 8️⃣ Чеклист перед демонстрацией заказчику

- [ ] `docker-compose ps` — все контейнеры Up
- [ ] `http://localhost:8080` — главная страница загружается
- [ ] `http://localhost:8080/search` — поиск работает
- [ ] Объекты с фото отображаются
- [ ] `http://localhost:8080/admin` — вход в админку работает (admin@realty-tuapse.ru / admin123)
- [ ] `http://localhost:8025` — Mailpit доступен (если нужны тесты почты)
- [ ] Нет ошибок 404 в консоли браузера (F12 → Console)
- [ ] CSS загружается корректно (F12 → Network → статус 200)

---

## 9️⃣ Быстрый старт (копипаст-версия)

```powershell
# 1. Перейдите в папку проекта
cd C:\Users\YourUser\Desktop\Realty_Tuapse\platform

# 2. Остановите предыдущие контейнеры
docker-compose down

# 3. Запустите проект
docker-compose up --build -d

# 4. Подождите 60 секунд
Start-Sleep -Seconds 60

# 5. Проверьте статус
docker-compose ps

# 6. Откройте сайт
Start-Process "http://localhost:8080"

# 7. Откройте админку
Start-Process "http://localhost:8080/admin"
```

**Логин в админку:** `admin@realty-tuapse.ru` / `admin123`

---

## 🔟 Поддержка во время демонстрации

Если что-то сломалось во время показа:

```powershell
# Перезапустите всё
docker-compose restart

# Посмотрите последние 100 строк логов
docker-compose logs --tail=100

# Очистите кэш
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear
```

---

## 📊 Сводная информация

| Параметр | Значение |
|----------|----------|
| **Время запуска** | ~2 минуты с нуля |
| **Требования к ПК** | 4+ ГБ ОЗУ, 20+ ГБ свободного места |
| **Порт сайта** | 8080 |
| **Порт админки** | 8080 (тот же) |
| **Порт Mailpit** | 8025 |
| **Порт MySQL** | 3307 |

---

## 📝 Примечания

### Исправленные проблемы:
1. ✅ **Политика конфиденциальности** — добавлен дефолтный текст
2. ✅ **Пользовательское соглашение** — добавлен дефолтный текст
3. ✅ **Текст в футере "О нас"** — исправлен CSS, добавлен текст в SiteSetting

### После первого запуска проверьте:
- [x] Страницы `/politika-konfidencialnosti` и `/polzovatelskoe-soglashenie` открываются
- [x] Текст в футере отображается корректно (не вертикально)
- [x] Все CSS загружаются без ошибок

---

**Дата создания:** 2025  
**Версия:** 1.0
