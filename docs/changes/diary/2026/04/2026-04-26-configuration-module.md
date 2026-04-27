## 22:30 (Europe/Kyiv) feat[config.settings,routes.web.admin,views.admin,model.main-menu,model.seo-redirect,model.administrator,auth.admin,middleware.seo-redirect,tests.feature.admin] — Settings: Main menu, 301 Redirects, Administrators CRUD

**Entry ID:** 01JCFGMOD20260426
**Agent:** Claude (Cursor)
**Дата:** 2026-04-26
**Ветка:** confiure_module

### Файлы
- `database/migrations/2026_04_26_200000_create_main_menu_items_table.php`, `2026_04_26_200001_create_seo_redirects_table.php`, `2026_04_26_200002_add_is_active_to_administrators_table.php`
- `app/Models/MainMenuItem.php`, `app/Models/SeoRedirect.php`, `app/Models/Administrator.php`
- `app/Services/Admin/MainMenuItemService.php`
- `app/Http/Controllers/Admin/MainMenuItemController.php`, `SeoRedirectController.php`, `AdministratorController.php`
- `app/Http/Requests/Admin/MainMenu/*`, `SeoRedirect/*`, `Administrator/*`
- `app/Http/Middleware/ApplySeoRedirectMiddleware.php`, `app/Http/Kernel.php`
- `app/Http/Requests/Admin/Auth/LoginRequest.php`, `RegisterController`, `app/View/Composers/AdminLayoutComposer.php`
- `routes/web.php`, `resources/views/admin/partials/sidebar.blade.php`, `resources/views/admin/pages/main-menu/*`, `seo-redirects/*`, `administrators/*`, `admin/partials/admin-password-fields.blade.php`
- `resources/themes/admin/assets/css/blocks/_main-menu.scss`, `app.scss`
- `database/factories/MainMenuItemFactory.php`, `SeoRedirectFactory.php`, `AdministratorFactory.php`
- `tests/Feature/Admin/MainMenuItemAdminTest.php`, `SeoRedirectAdminTest.php`, `AdministratorAdminTest.php`, `AdminAuthTest.php`

### Что сделано
В сайдбаре добавлена группа **Settings** (выпадающий collapse) с пунктами **Redirects** (CRUD 301, глобальный middleware по `slug_from`), **Main menu** (дерево DnD по образцу Category Tree, `store` + модалки, max **3** уровня: валидация save-order и update/store), **Users** (CRUD `Administrator` с паролем, переключатель показа пароля, `is_active`). Для guard `admin` логин через `attempt` с `is_active = true` и колонка `is_active` у администраторов (миграция, регистрация с `is_active: true`).

### Почему
Задача 11: модуль configuration в админке с тремя подмодулями и навигацией по спецификации.

### Влияние
- **БД:** таблицы `main_menu_items`, `seo_redirects`; `administrators.is_active` (по умолчанию true)
- **API:** N/A; публичные GET-редиректы 301 по совпадению path с `seo_redirects.slug_from` (искл. `admin/*`)
- **Производительность:** middleware: один lookup по `slug_from` для не-admin GET

### Проверено
- Тесты: новые `MainMenuItemAdminTest`, `SeoRedirectAdminTest`, `AdministratorAdminTest`, доп. сценарий в `AdminAuthTest`; весь набор: 49 passed
- Линтер: Pint (ok)

### Follow-up
- N/A
