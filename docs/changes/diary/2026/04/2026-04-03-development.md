# Дневник изменений — 2026-04-03 (development)

## 12:30 (Europe/Riga) feat[model.static-page,routes.web.admin,controller.admin.static-page,view.admin,styles.admin.theme,api.admin.bootstrap-table] — StaticPage CRUD и bootstrap-table в админке
**Entry ID:** 01JBTABLE20260403
**Дата:** 2026-04-03
**Ветка:** development

### Файлы
- `database/migrations/2026_04_03_102446_create_static_pages_table.php`
- `app/Models/StaticPage.php`
- `database/factories/StaticPageFactory.php`
- `database/seeders/StaticPageSeeder.php`, `database/seeders/DatabaseSeeder.php`
- `app/Services/Admin/StaticPageListingService.php`, `app/Services/Admin/EmployeeListingService.php`
- `app/Http/Controllers/Admin/StaticPageController.php`
- `app/Http/Controllers/Admin/Api/EmployeeTableDataController.php`, `StaticPageTableDataController.php` (удалён `EmployeeIndexController`)
- `app/Http/Requests/Admin/StaticPage/StoreStaticPageRequest.php`, `UpdateStaticPageRequest.php`
- `app/Http/Resources/Admin/StaticPageResource.php`
- `routes/web.php`
- `resources/views/admin/partials/bootstrap-table-widget.blade.php`, `head.blade.php`, `dashboard.blade.php`, `tables.blade.php`, `pages/static-pages/*`
- `resources/themes/admin/assets/css/blocks/_bootstrap-table.scss`, `app.scss`, `assets/js/admin-bootstrap-table.js`
- `app/View/Composers/AdminLayoutComposer.php`, `resources/views/admin/partials/sidebar.blade.php`, layouts `sb-admin*`
- `tests/Feature/Admin/AdminPagesTest.php`, `tests/Feature/Admin/StaticPageAdminTest.php`
- `workflow.md`

### Что сделано
Добавлена сущность Static для статичных страниц админки (поля по ТЗ, `parent_id` с иерархией, без FK на `0`). Реализован resource CRUD, сидер и демо-данные. Список на `view.blade` использует bootstrap-table с server-side JSON (`total`/`rows`). Демо employees на dashboard/tables переведено с Simple-DataTables на тот же паттерн. В шапку админки добавлен CSRF meta; общие стили bootstrap-table вынесены в SCSS темы.

### Почему
Выполнение таска по виджету таблицы на Bootstrap 5 / bootstrap-table и модулю StaticPage.

### Влияние
- **БД:** новая таблица `static_pages`
- **API:** `GET /adm/api/employees` и `GET /adm/api/static-pages/table` отдают `{ total, rows }` для bootstrap-table
- **Производительность:** пагинация на сервере для больших списков

### Проверено
- Тесты: обновлены/добавлены Feature (PHPUnit, sqlite `:memory:`)
- Линтер: ok (`pint --dirty`)

### Follow-up
- [ ] При поднятом Sail: `sail artisan migrate`, при необходимости `sail npm run build`, полный `sail artisan test`

## 14:00 (Europe/Riga) feat[view.admin,api.admin.bootstrap-table] — Колонки дат и поиск по числам в bootstrap-table
**Entry ID:** 01JBTSORTSEARCH20260403
**Дата:** 2026-04-03
**Ветка:** development

### Файлы
- `app/Services/Admin/EmployeeListingService.php`
- `app/Services/Admin/StaticPageListingService.php`
- `resources/views/admin/tables.blade.php`
- `resources/views/admin/dashboard.blade.php`
- `resources/views/admin/pages/static-pages/view.blade.php`

### Что сделано
В таблицах employees и static pages перед колонкой Actions добавлены сортируемые `created_at` и `updated_at`. Поиск расширен: помимо текстовых полей учитываются числовые (через `CAST`/`LIKE` и точные совпадения для id, age, salary у сотрудников и id, parent_id, sort_no у страниц), а также даты/время в строковом виде.

### Почему
Запрос пользователя: сортировка по датам и работа поиска по числовым значениям.

### Влияние
- **БД:** N/A
- **API:** те же эндпоинты, изменена только фильтрация `search`
- **Производительность:** дополнительные `OR` в запросе при непустом поиске

### Проверено
- Тесты: полный PHPUnit
- Линтер: ok (`pint --dirty`)

## 15:00 (Europe/Riga) feat[api.admin.bootstrap-table] — Формат дат d.m.Y в JSON таблиц
**Entry ID:** 01JBDATEDMY20260403
**Дата:** 2026-04-03
**Ветка:** development

### Файлы
- `app/Http/Resources/Admin/EmployeeResource.php`
- `app/Http/Resources/Admin/StaticPageResource.php`

### Что сделано
Поля `created_at` и `updated_at` в ответах для bootstrap-table выводятся в формате `d.m.Y` (например `03.04.2026`) вместо ISO8601.

### Почему
Требование к отображению колонок дат в таблице.

### Влияние
- **БД:** N/A
- **API:** только строковое представление полей в JSON; сортировка по-прежнему по колонкам БД на сервере.

### Проверено
- Тесты: PHPUnit
- Линтер: ok

## 16:00 (Europe/Riga) docs[docs.admin.bootstrap-table] — Справочник по bootstrap-table в админке
**Entry ID:** 01JDOCSBT20260403
**Дата:** 2026-04-03
**Ветка:** development

### Файлы
- `docs/admin-bootstrap-table.md` (+новый)
- `workflow.md` (дополнен ссылкой на справочник и итогами доработок)

### Что сделано
Добавлен файл `docs/admin-bootstrap-table.md`: маршруты, формат JSON `{ total, rows }`, query-параметры, роль listing-сервисов и ресурсов, виджет Blade, стили, ссылки на тесты и дневник. В `workflow.md` добавлена ссылка на справочник и кратко зафиксированы последующие доработки (даты в таблице, поиск, формат `d.m.Y`).

### Почему
Запрос на документирование функционала.

### Влияние
- **БД:** N/A
- **API:** N/A (только документация)

### Проверено
- Тесты: N/A
- Линтер: N/A

## 17:45 (Europe/Riga) refactor[views.admin,docs.admin.bootstrap-table] — Vite entry для admin-bootstrap-table
**Entry ID:** 01JVITEABSBT20260403
**Дата:** 2026-04-03
**Ветка:** table_vidget

### Файлы
- `vite.config.js` (+1)
- `resources/views/admin/partials/bootstrap-table-widget.blade.php` (inline delete → `@vite`)
- `resources/views/admin/tables.blade.php` (текст карточки про Vite и путь к JS)
- `docs/admin-bootstrap-table.md` (уточнение про скрипты)

### Что сделано
В `vite.config.js` добавлен input `resources/themes/admin/assets/js/admin-bootstrap-table.js`. Виджет bootstrap-table после CDN jQuery/bootstrap-table подключает этот банл через `@vite` вместо дублирующего inline-скрипта. На странице Tables обновлена справочная карточка; в `docs/admin-bootstrap-table.md` описан порядок загрузки.

### Почему
Единый источник хелперов (в т.ч. `adminBootstrapTableDelete`) в репозитории, без расхождения с файлом темы.

### Влияние
- **БД:** N/A
- **API:** N/A
- **Производительность:** N/A (малый JS-chunk в манифесте)

### Проверено
- Тесты: `AdminPagesTest`, `StaticPageAdminTest`
- Линтер: N/A

### Follow-up
- [ ] После `git pull`: при ошибке Rollup optional deps выполнить чистую переустановку `node_modules` / `npm install`
