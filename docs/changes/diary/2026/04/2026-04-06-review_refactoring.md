# Дневник — 2026-04-06 (review_refactoring)

## 16:00 (Europe/Kyiv) refactor[views.admin,frontend.vite] — Code review: bootstrap-table i18n и защита formatter
**Entry ID:** 01JCRREFVIEW20260406
**Agent:** Composer
**Дата:** 2026-04-06
**Ветка:** review_refactoring

### Файлы
- `resources/views/admin/partials/bootstrap-table-widget.blade.php`
- `resources/themes/admin/assets/js/admin-bootstrap-table.js`
- `resources/views/admin/pages/static-pages/view.blade.php`
- `refactoring_workflow.mdc` (+запись процесса review)

### Что сделано
Полный обзор недавних зон (static pages destroy + JSON, регистрация админа, admin-ui, bootstrap-table): логика и тесты согласованы, пароль при регистрации хешируется через cast модели. Добавлены data-атрибуты с `__()` для строк `adminBootstrapTableDelete`, переводимый заголовок колонки действий, проверка целочисленного `id` в `adminStaticPageRowActions` перед сборкой URL. Процесс review задокументирован в `refactoring_workflow.mdc`.

### Почему
Локализация клиентских строк удаления таблицы и защита от некорректного `row.id` в inline `onclick` (defense in depth).

### Влияние
- **БД:** N/A
- **API:** N/A
- **Производительность:** N/A

### Проверено
- Тесты: `php artisan test` — 22 passed
- Линтер: N/A (PHP-классы не менялись)

### Follow-up
- [ ] При изменении JS — `npm run build`
