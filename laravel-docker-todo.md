# laravel-docker-todo — security backlog

Список проблем по безопасности, найденных при разборе проекта 2026-09-17 (PHP 8.4 / Laravel 12 / MySQL / Redis / Elasticsearch / MoonShine).
Чиним позже по этому списку. Ссылки на код актуальны на дату составления — перед правкой перепроверить.

## 1. [HIGH] Публичный дамп PII пользователей без авторизации
Любой без токена может `GET /api/v2/users` (и v1) и получить всех пользователей из ES-индекса `users`: name, email, reserve_email, phone, telegram.
- `routes/api/v1.php:9-14`, `routes/api/v2.php:9-14` — `/users` и `/products` без auth-middleware
- `bootstrap/app.php:19-21` — `withMiddleware` пустой; Sanctum не подключен
- `app/Http/Requests/v1|v2/User/IndexRequest.php` (и Product) — `authorize()` везде `return true`
- `app/Http/Resources/User/UserResource.php` — отдаёт PII-поля
- План: auth-middleware (Sanctum/сессия) на /users, либо убрать users из публичного API; `authorize()` не blanket-true

## 2. [MED] Нет rate limiting и верхних границ пагинации
- Ни одного `throttle`/`RateLimiter` в app/routes/config/bootstrap
- `per_page`: `min:1` без `max`; `page` без верхней границы (`app/Http/Requests/v*/{User,Product}/IndexRequest.php`)
- `from = (page-1)*size` в `app/Services/Elasticsearch/PaginationRequestMapper.php:29` — огромные `per_page`/глубокий `page` грузят ES и упираются в `max_result_window`
- План: throttle на API; `'per_page' => 'max:50'`; ограничить глубину page

## 3. [MED] Утечка внутренних сообщений об ошибках в проде
- `bootstrap/app.php:22-35` — при `APP_DEBUG=false` обработчик `SearchIndexException` отдаёт клиенту сырой `$exception->getMessage()`
- План: клиенту — нейтральное сообщение, детали только в лог

## 4. [MED/LOW] Elasticsearch: http + простые креды
- `.env`: `ELASTICSEARCH_URL=http://...`, креды вида `elastic/...`; `ElasticsearchClient::baseHttpRequest()` — Basic auth поверх plain http
- План: TLS либо только внутренняя сеть; реальные креды

## 5. [LOW] Гигиена окружения/деплоя
- `.env`: `APP_ENV=local`, `APP_DEBUG=true` (в git не попадает — gitignored; для прод-деплоя менять)
- Дев-тулинг из README (phpMyAdmin:8080, Adminer:9090, Mailpit:8025, креды refactorian/refactorian) не должен быть доступен наружу на проде
- `SCRAMBLE_ENABLED=false`; если включать — держать за `RestrictedDocsAccess`

## Уже сделано правильно (не ломать)
- `.env` в `.gitignore`, секретов в репозитории нет
- `DB::prohibitDestructiveCommands()` — `app/Providers/AppServiceProvider.php:87`
- `declare(strict_types=1)` везде
- Поисковая строка уходит в ES значением в JSON-теле (`BaseElasticsearchRepository.php:105-109`) — нет инъекции в DSL; `sorted_by` валидируется enum'ом
- MoonShine за `Authenticate` middleware, отдельный guard `moonshine` (`config/moonshine.php`)
