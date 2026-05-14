# Дневник изменений — 2026-05-14

## 21:37 (Europe/Kyiv) refactor[model.banner,model.static-page,db.banners.migration,db.static-pages.migration] — Удалён parent_id у Banner и StaticPage
**Entry ID:** 01JDROPPARENT20260514
**Agent:** Composer
**Дата:** 2026-05-14
**Ветка:** local

### Файлы
- `database/migrations/2026_05_14_120000_drop_parent_id_from_banners_and_static_pages_tables.php` (+44 −0, условный DROP `parent_id`)
- `app/Models/Banner.php`, `app/Models/StaticPage.php`
- `app/Http/Controllers/Admin/BannerController.php`, `app/Http/Controllers/Admin/StaticPageController.php`
- `app/Http/Requests/Admin/Banner/*`, `app/Http/Requests/Admin/StaticPage/*`
- `app/Services/Admin/BannerListingService.php`, `app/Services/Admin/StaticPageListingService.php`
- `app/Http/Resources/Admin/BannerResource.php`, `app/Http/Resources/Admin/StaticPageResource.php`
- `database/factories/BannerFactory.php`, `database/factories/StaticPageFactory.php`
- `database/seeders/StaticPageSeeder.php`
- `resources/views/admin/pages/banners/create.blade.php`, `edit.blade.php`
- `resources/views/admin/pages/static-pages/create.blade.php`, `edit.blade.php`, `show.blade.php`, `view.blade.php`
- `tests/Feature/Admin/StaticPageAdminTest.php`

### Что сделано
Удалены поле и логика `parent_id` для моделей `Banner` и `StaticPage`: связи parent/children, проверки при удалении «родителя с детьми», поля в формах и bootstrap-table, сортировка/поиск по parent_id. Добавлена миграция с `Schema::hasColumn` для безопасного DROP на уже развёрнутых БД. Сидер статических страниц создаёт только плоский набор записей.

### Почему
Задача — убрать лишнюю иерархию у баннеров и статических страниц без затронутых деревьев каталога и главного меню.

### Влияние
- **БД:** удаление `parent_id` из `banners` и `static_pages` после migrate
- **API:** JSON строк таблиц без `parent_id`
- **Производительность:** N/A

### Проверено
- Тесты: обновлён `StaticPageAdminTest`
- Линтер: pint

### Follow-up
- [ ] Выполнить миграции на всех окружениях (`sail artisan migrate` или аналог)
