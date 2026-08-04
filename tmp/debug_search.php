# Перезапустите контейнеры
docker-compose restart

# Посмотрите логи
docker-compose logs -f --tail=100

# Восстановите кэш
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear