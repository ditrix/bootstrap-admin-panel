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

## 23:45 (Europe/Kyiv) feat[api.admin.bootstrap-table,routes.web.admin,views.admin,controller.admin.api,service.admin.listing] — Bootstrap-table для Users, Redirects, Main menu

**Entry ID:** 01JBTCFGSETTINGSBT20260426
**Agent:** Claude (Cursor)
**Дата:** 2026-04-26
**Ветка:** confiure_module

### Файлы
- `app/Services/Admin/AdministratorListingService.php`, `SeoRedirectListingService.php`, `MainMenuItemListingService.php`
- `app/Http/Resources/Admin/AdministratorResource.php`, `SeoRedirectResource.php`, `MainMenuItemResource.php`
- `app/Http/Controllers/Admin/Api/AdministratorTableDataController.php`, `SeoRedirectTableDataController.php`, `MainMenuItemTableDataController.php`
- `app/Http/Controllers/Admin/AdministratorController.php`, `SeoRedirectController.php`, `MainMenuItemController.php`
- `routes/web.php`
- `resources/views/admin/pages/administrators/index.blade.php`, `seo-redirects/index.blade.php`, `main-menu/index.blade.php`
- `tests/Feature/Admin/AdministratorAdminTest.php`, `SeoRedirectAdminTest.php`, `MainMenuItemAdminTest.php`

### Что сделано
Списки **Administrators**, **301 Redirects** и плоский список пунктов **Main menu** переведены на `@include('admin.partials.bootstrap-table-widget')` с серверной пагинацией и поиском (формат `{ total, rows }`). Для main menu дерево DnD сохранено отдельной карточкой ниже таблицы; кнопка «Add» и модалки общие. JSON-эндпоинты: `api/administrators/table`, `api/seo-redirects/table`, `api/main-menu/table`.

### Почему
Единый UI и поведение таблиц с Static pages и виджетом bootstrap-table.

### Влияние
- **БД:** N/A
- **API:** три новых GET JSON для bootstrap-table (auth:admin)
- **Производительность:** N/A

### Проверено
- Тесты: обновлены/добавлены сценарии index + JSON table для трёх модулей
- Линтер: Pint (ok)

### Follow-up
- N/A

## 12:10 (Europe/Kyiv) fix[views.admin,routes.web.admin,api.admin.bootstrap-table,controller.admin.main-menu] — Main menu снова только дерево (без bootstrap-table)

**Entry ID:** 01JMAINMENUREVERTBT20260426
**Agent:** Claude (Cursor)
**Дата:** 2026-04-26
**Ветка:** confiure_module

### Файлы
- Удалены: `app/Services/Admin/MainMenuItemListingService.php`, `app/Http/Resources/Admin/MainMenuItemResource.php`, `app/Http/Controllers/Admin/Api/MainMenuItemTableDataController.php`
- `routes/web.php` (−1 маршрут `api/main-menu/table`)
- `app/Http/Controllers/Admin/MainMenuItemController.php`, `resources/views/admin/pages/main-menu/index.blade.php`
- `tests/Feature/Admin/MainMenuItemAdminTest.php`

### Что сделано
По уточнению задачи bootstrap-table оставлен только для **Administrators** и **301 Redirects**. Страница **Main menu** возвращена к одной карточке с DnD-деревом и кнопкой «Add» в шапке; удалены JSON-эндпоинт и клиентская таблица для пунктов меню.

### Почему
В постановке требовалось менять таблицы только для редиректов и администраторов.

### Влияние
- **БД:** N/A
- **API:** удалён `GET api/main-menu/table`
- **Производительность:** N/A

### Проверено
- Тесты: `MainMenuItemAdminTest` (без сценария table API)
- Линтер: N/A

### Follow-up
- N/A

## 21:45 (Europe/Kyiv) fix[service.main-menu-item,views.admin.main-menu] — DnD дерева главного меню после перетаскивания веток

**Entry ID:** 01JMMSAVEORDDND20260427
**Agent:** Claude (Cursor)
**Дата:** 2026-04-27
**Ветка:** confiure_module

### Файлы
- `app/Services/Admin/MainMenuItemService.php`
- `resources/views/admin/pages/main-menu/index.blade.php`

### Что сделано
После перетаскивания узла с потомками Sortable держал устаревшие экземпляры на вложенных `<ol>` — следующие drag переставали работать. Добавлены полное `destroy` всех экземпляров и повторное монтирование после `onEnd` (через `setTimeout(0)`). При ошибке сохранения (422 и др.) разбирается JSON Laravel, показывается текст валидации и выполняется `reload`, чтобы DOM снова совпадал с БД. `saveOrder` обёрнут в DB-транзакцию.

### Почему
Сообщение пользователя: после ошибки сохранения при переносе ветки перетаскивание ломалось.

### Влияние
- **БД:** атомарное применение порядка при успешном запросе
- **API:** N/A
- **Производительность:** N/A

### Проверено
- Тесты: `MainMenuItemAdminTest` (Sail)
- Линтер: Pint (ok)

### Follow-up
- N/A

## 22:00 (Europe/Kyiv) feat[request.admin.main-menu] — Main menu: без лимита глубины дерева

**Entry ID:** 01JMMUNLIMITDEPTH20260427
**Agent:** Claude (Cursor)
**Дата:** 2026-04-27
**Ветка:** confiure_module

### Файлы
- `app/Http/Requests/Admin/MainMenu/SaveMainMenuItemOrderRequest.php`, `StoreMainMenuItemRequest.php`, `UpdateMainMenuItemRequest.php`
- `tests/Feature/Admin/MainMenuItemAdminTest.php`

### Что сделано
Убрана валидация «не более 3 уровней» для главного меню: `save-order`, создание и смена родителя больше не ограничивают глубину. Проверка на циклы в `UpdateMainMenuItemRequest` сохранена. Тесты заменены на сценарии успешного создания 4-го уровня и сохранения глубокого дерева.

### Почему
Запрос пользователя снять ограничение по уровням.

### Влияние
- **БД:** N/A
- **API:** N/A
- **Производительность:** N/A

### Проверено
- Тесты: `MainMenuItemAdminTest` (Sail)
- Линтер: Pint (ok)

### Follow-up
- N/A
