## 10:26 (Europe/Kyiv) refactor[routes.admin-web,routes.admin-api,routes.web.admin] — Вынос admin-роутов в отдельные файлы, группировка по контексту

**Entry ID:** 01JR20260428ROUTEREFACTOR
**Agent:** Sonnet 4.6
**Дата:** 2026-04-28
**Ветка:** (локально)

### Файлы
- `routes/admin-web.php` (+110 −0) — создан
- `routes/admin-api.php` (+22 −0) — создан
- `routes/web.php` (−78) — удалены admin-роуты
- `app/Providers/RouteServiceProvider.php` (+6 −0)

### Что сделано
Все admin-роуты вынесены из `routes/web.php` в два специализированных файла: `routes/admin-web.php` (HTML-страницы) и `routes/admin-api.php` (AJAX/JSON-эндпоинты для bootstrap-table). Оба файла зарегистрированы в `RouteServiceProvider` под `web`-миддлвар (для поддержки сессионной авторизации). В `admin-web.php` применена группировка через `Route::controller()` для контроллеров с несколькими методами и логическая структура: auth-гостевые, auth-аутентифицированные → дашборд, UI-демо, ошибки, контент, деревья, администрирование.

### Почему
Архитектура проекта (ARCHITECTURE.md) предписывает разделение по контексту: `web.php` — shop, `admin-web.php` — admin web, `admin-api.php` — admin AJAX/API. Это улучшает навигацию, снижает когнитивную нагрузку и соответствует паттерну reference-проекта.

### Влияние
- **БД:** N/A
- **API:** N/A (имена и URL всех роутов сохранены без изменений)
- **Производительность:** N/A

### Проверено
- Тесты: N/A (изменения только в роутинге, route:list подтвердил корректность)
- Линтер: ok (pint --dirty)

### Follow-up
- [ ] N/A
