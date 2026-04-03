# Админка: bootstrap-table (server-side)

Краткий справочник по таблицам с подгрузкой данных с сервера и модулю Static pages.

## Маршруты (префикс `/adm`, `auth:admin`)

| Назначение | Метод | Имя маршрута |
|------------|-------|----------------|
| JSON для сотрудников (демо) | `GET` | `admin.api.employees` |
| JSON для статических страниц | `GET` | `admin.api.static-pages.table` |
| Список Static pages (Blade + таблица) | `GET` | `admin.static-pages.index` |

Контроллеры: `App\Http\Controllers\Admin\Api\EmployeeTableDataController`, `StaticPageTableDataController`.

## Формат ответа

Ожидается клиентом **bootstrap-table** в режиме `side-pagination: server`:

```json
{
  "total": 100,
  "rows": [ { "...": "поля строки" } ]
}
```

Строки формируются через `EmployeeResource` / `StaticPageResource`. В display-полях **`created_at`** и **`updated_at`** используется формат **`d.m.Y`** (например `03.04.2026`). Сортировка на бэкенде идёт по колонкам БД, а не по этой строке.

## Параметры запроса (query)

Клиент **bootstrap-table** по умолчанию передаёт:

- `limit`, `offset` — пагинация;
- `search` — глобальный поиск;
- `sort`, `order` — сортировка (`asc` / `desc`).

Обработка: `EmployeeListingService::paginateForBootstrapTable()`, `StaticPageListingService::paginateForBootstrapTable()`.

**Поиск:** текстовые поля через `LIKE`; числа и даты дополнительно через приведение к строке (`CAST` / `TEXT` для SQLite) и точные совпадения для целочисленных id и связанных полей (см. код сервисов).

**Сортировка:** только по полям из белых списков `SORTABLE` внутри каждого сервиса (защита от произвольного `ORDER BY`).

## Blade-виджет

Файл: `resources/views/admin/partials/bootstrap-table-widget.blade.php`.

Подключается с массивом `columns` (`field`, `title`, `sortable`) и опционально `actionsFormatter` — имя глобальной JS-функции для колонки Actions (после всех колонок из массива).

Скрипты: CDN jQuery + bootstrap-table, затем Vite entry `resources/themes/admin/assets/js/admin-bootstrap-table.js` (глобальный `adminBootstrapTableDelete(url)` — POST + `_method: DELETE`, CSRF из `<meta name="csrf-token">` в `admin/partials/head.blade.php`).

## Стили темы

SCSS: `resources/themes/admin/assets/css/blocks/_bootstrap-table.scss`, подключён в `app.scss` темы админки.

## Static pages (CRUD)

- Маршрут resource: `admin.static-pages.*`
- Список в шаблоне: `resources/views/admin/pages/static-pages/view.blade.php`
- Модель: `App\Models\StaticPage` (`parent_id` по умолчанию `0`, связи `parent` / `children`)

## Тесты

- `tests/Feature/Admin/AdminPagesTest.php` — дашборд, tables, API employees
- `tests/Feature/Admin/StaticPageAdminTest.php` — CRUD и табличный API static pages

## Журнал изменений

Детали по датам и скоупам: `docs/changes/diary/2026/04/2026-04-03-development.md`, тег `docs/changes/tags/api.admin.bootstrap-table.md`.
