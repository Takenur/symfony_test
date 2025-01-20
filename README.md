# Test Project Symfony 
## _Этот проект создан с использованием Symfony Framework и предоставляет функционал управления продуктами, включая парсинг информации о продуктах с внешнего ресурса (https://world.openfoodfacts.org/)._

Перед запуском убедитесь, что у вас установлены следующие зависимости:

- PHP >= 8.1
- Composer
- MySQL или другая поддерживаемая Symfony база данных
## 🚀 Установка
- Установите зависимости: composer install
- Скопируйте и настройте .env файл
- Отредактируйте .env.local, чтобы указать ваши настройки подключения к базе данных: DATABASE_URL="mysql://username:password@127.0.0.1:3306/название_базы"
- Создайте базу данных: php bin/console doctrine:database:create
- php bin/console doctrine:migrations:migrate
## 🛠 Использование
- Чтобы запустить сервер разработки, выполните: symfony server:start  или symfony server
## Запуск тестов
- Создайте тестовую базу данных:
```sh
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test
```
- Запустите тесты:
```sh
vendor/bin/phpunit  или php bin/phpunit
```

## 🧩 Основные эндпоинты
-Парсинг продуктов:
-POST /product/parse
-Тело запроса: {"url": "https://world.openfoodfacts.org/product/..."}
-Возвращает информацию о продукте.
Получение списка продуктов (с пагинацией):

-GET /products
-CRUD для продуктов:
-Создание, редактирование, удаление и просмотр реализованы.
