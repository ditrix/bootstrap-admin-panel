# Дневник — 2026-05-15

## 20:30 (Europe/Kyiv) feat[views.admin,service.admin.dashboard,controller.admin.dashboard,styles.admin.theme] — Дашборд: карточки со счётчиками и ссылками, без таблицы Employees

**Entry ID:** 01JDASHCARD20260515  
**Agent:** Composer  
**Дата:** 2026-05-15  
**Ветка:** dashboard_card  

### Файлы

- `app/Services/Admin/AdminDashboardService.php` (−1 модель демо, +счётчики StaticPage/Banner/Employee/CategoryTree и роуты)
- `app/Http/Controllers/Admin/DashboardController.php` (убраны параметры таблицы)
- `resources/views/admin/dashboard.blade.php` (удалён виджет bootstrap-table, класс карточек)
- `resources/themes/admin/assets/css/blocks/_admin-ui.scss` (`.admin-dashboard-summary-card`)
- `tests/Feature/Admin/AdminPagesTest.php` (assert только `cards`)

### Что сделано

На `/admin/dashboard` убрана встраиваемая таблица Employees. Четыре карточки показывают фактические числа записей и ведут на `admin.static-pages.index`, `admin.banners.index`, `admin.tables.index`, `admin.category-tree.index`. Фон карточек задан через `var(--bs-card-cap-bg)` в SCSS; текст и ссылки приведены к читабельным для светлого фона (`text-body`, стандартный stretched-link).

### Почему

Требование промпта: сводные панели по контентным разделам вместо демо-карточек и без дублирования таблицы сотрудников на главной.

### Влияние

- **БД:** N/A
- **API:** N/A
- **Производительность:** четыре простых `count()` на загрузке дашборда

### Проверено

- Тесты: обновлён feature-тест дашборда (запуск — у пользователя через Sail при необходимости)
- Линтер: ok (IDE)

### Follow-up

- [ ] N/A
