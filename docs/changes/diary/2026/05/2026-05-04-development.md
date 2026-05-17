## 14:00 (Europe/Kyiv) fix[db.seo_redirects.migration,model.seo-redirect,request.admin.seo-redirect] — Лимит slug_from под UNIQUE MySQL utf8mb4

**Entry ID:** 01JFIXSEO76820260504
**Agent:** Composer
**Дата:** 2026-05-04
**Ветка:** development

### Файлы
- `database/migrations/2026_04_26_200001_create_seo_redirects_table.php` (+1 −1)
- `app/Http/Requests/Admin/SeoRedirect/StoreSeoRedirectRequest.php` (+1 −1)
- `app/Http/Requests/Admin/SeoRedirect/UpdateSeoRedirectRequest.php` (+1 −1)

### Что сделано
Для колонки `slug_from` задана длина **768** символов вместо 1000, чтобы уникальный индекс укладывался в лимит ключа InnoDB **3072 байта** при `utf8mb4` (768×4). Правила валидации `max` синхронизированы с колонкой.

### Почему
Миграция падала с `SQLSTATE[42000]: Specified key was too long; max key length is 3072 bytes` на `unique(slug_from)` при длине строки 1000.

### Влияние
- **БД:** максимальная длина `seo_redirects.slug_from` — 768 символов (новые установки).
- **API:** N/A
- **Производительность:** N/A

### Проверено
- Тесты: `tests/Feature/Admin/SeoRedirectAdminTest.php`
- Линтер: pint

### Follow-up
- [ ] Если после неудачной миграции осталась «битая» таблица без нужной схемы — откатить батч/`drop table seo_redirects` и снова `migrate`.

## 16:30 (Europe/Kyiv) refactor[routes.admin-web,views.admin,provider.view-composer] — Удалены UI-демо из сайдбара (Layouts, Pages, Blank, демо ошибок)

**Entry ID:** 01JRMVSBDEMO20260504
**Agent:** Composer
**Дата:** 2026-05-04
**Ветка:** development

### Файлы
- `routes/admin-web.php` (− маршруты layouts, blank, errors demo)
- `resources/views/admin/partials/sidebar.blade.php` (без Interface / Layouts / Pages / Blank Page)
- `app/Providers/AppServiceProvider.php` (composer только для `sb-admin`)
- `app/View/Composers/AdminLayoutComposer.php` (− ключи демо)
- Удалены: `BlankPageController`, `AdminErrorDemoController`, `Layout/*`, `AdminErrorPage`, blade-демо (`blank`, `layout-static`, `layout-sidenav-light`, `layouts/sb-admin-static`, `layouts/error`, `errors/*`)

### Что сделано
Из сайдбара убраны блоки **Layouts** (Static Navigation, Light Sidenav), **Pages** (демо Authentication в меню и Error), пункт **Blank Page**. Удалены соответствующие роуты и код только для этих экранов. Рабочие гостевые маршруты **login / register / password reset** и layout **auth** сохранены. Остаются **Tables**, **Forms** и модульные страницы (Static pages, Category Tree, Settings).

### Почему
Шаблонные пункты SB Admin не нужны в продуктовой админке.

### Влияние
- **БД:** N/A
- **API:** N/A
- **Производительность:** N/A

### Проверено
- Тесты: `AdminPagesTest`, `AdminAuthTest`
- Линтер: pint (файлы задачи)

### Follow-up
- [ ] N/A

## 17:45 (Europe/Kyiv) refactor[routes.admin-web,views.admin,controller.admin.tables,service.admin.dashboard] — Удалён Forms; Tables как модуль pages + resource

**Entry ID:** 01JTBLFORMS20260504
**Agent:** Composer
**Дата:** 2026-05-04
**Ветка:** development

### Файлы
- `routes/admin-web.php` — `Route::resource('tables', ...)->only(['index'])`; удалён `/forms`
- `app/Http/Controllers/Admin/TablesController.php` → view `admin.pages.tables.index`
- `resources/views/admin/pages/tables/index.blade.php` (+новый); удалены `admin/forms.blade.php`, `admin/tables.blade.php`
- Удалён `FormsController`
- `resources/views/admin/partials/sidebar.blade.php` — только Tables (`admin.tables.index`, `__('Tables')`)
- `app/View/Composers/AdminLayoutComposer.php` — `admin.tables.*`
- `app/Services/Admin/AdminDashboardService.php` — ссылки на Forms убраны
- `tests/Feature/Admin/AdminPagesTest.php` — тест Forms удалён; Tables обновлён под новые имена

### Что сделано
Демо **Forms** удалено полностью. Раздел **Tables** приведён к паттерну как у **Static pages**: именованный resource только для index (`GET /admin/tables` → `admin.tables.index`), шаблон в `pages/tables/index.blade.php`.

### Почему
Единая архитектура модульных страниц и отказ от лишнего SB-демо.

### Влияние
- **БД:** N/A
- **API:** N/A (`admin.api.employees` без изменений)
- **Производительность:** N/A

### Проверено
- Тесты: `AdminPagesTest`
- Линтер: pint

### Follow-up
- [ ] N/A
