# Админка: роли и права (Spatie Permission)

Справочник по системе доступа в панели управления: пакет **`spatie/laravel-permission`**, guard **`admin`**, учётные записи в таблице **`administrators`**.

## Термины

| Термин | Значение |
|--------|----------|
| **guard `admin`** | Сессионная аутентификация администраторов (`config/auth.php`, провайдер `administrators`) |
| **Роль (Spatie)** | Запись в `roles` с полями `name`, `guard_name` (`admin`) |
| **Право (permission)** | Строковый идентификатор в `permissions` для guard `admin`; проверяется middleware и `@can` |
| **`role_id`** | Денормализованная ссылка на `roles.id` у `administrators`; при сохранении модель синхронизирует роль в пивотах Spatie |

## Роли из сидера

| `name` в БД | Описание |
|-------------|----------|
| **`admin`** | Все permissions |
| **`manager`** | **Dashboard** (`dashboard.view`) и контент: static pages, category tree, banners, employees (таблица) |

Учётные записи после полного сидинга (см. порядок в `Database\Seeders\DatabaseSeeder`):

| Email | Пароль (демо) | Роль |
|-------|---------------|------|
| `admin@mail.com` | `password` | admin |
| `manager@mail.com` | `password` | manager |

Дополнительных «демо»-администраторов сидеры не создают — остальных пользователей заводит админ через CRUD **Users** (`/admin/administrators`).

## Имена прав (permissions)

Источник констант: `App\Authorization\AdminPermission` — значения строк совпадают с полем `name` в БД и с аргументами `@can('...')`.

| Константа | `name` |
|-----------|--------|
| `DASHBOARD_VIEW` | `dashboard.view` |
| `STATIC_PAGES_MANAGE` | `static_pages.manage` |
| `CATEGORY_TREE_MANAGE` | `category_tree.manage` |
| `BANNERS_MANAGE` | `banners.manage` |
| `EMPLOYEES_MANAGE` | `employees.manage` |
| `SEO_REDIRECTS_MANAGE` | `seo_redirects.manage` |
| `MAIN_MENU_MANAGE` | `main_menu.manage` |
| `USERS_MANAGE` | `users.manage` |
| `PERMISSIONS_VIEW` | `permissions.view` |
| `LOG_VIEWER_VIEW` | `log_viewer.view` |

Назначение ролям: `Database\Seeders\RolesAndPermissionsSeeder` (после него обязательно отрабатывает `AdminSeeder` с привязкой `role_id`).

## Слой HTTP

- **Маршруты web:** `routes/admin-web.php` — на закрытых маршрутах стек `auth:admin`, `use_admin_guard`, затем **`permission:<имя>`** на группах/ресурсах.
- **Маршруты API таблиц:** `routes/admin-api.php` — те же guard + permission на каждый endpoint.
- **`use_admin_guard`:** `App\Http\Middleware\UseAdminAuthGuard` вызывает `Auth::shouldUse('admin')`, чтобы `auth()` и Blade **`@can`** проверяли права у текущего администратора.

Алиасы middleware (см. `App\Http\Kernel.php`): `permission`, `role`, `role_or_permission` (Spatie).

## Модель администратора

`App\Models\Administrator`:

- Трейт **`Spatie\Permission\Traits\HasRoles`**
- **`protected $guard_name = 'admin'`**
- При **`saved`** синхронизация Spatie-ролей с полем **`role_id`** (см. `booted()`)

## Редирект после входа

`App\Support\AdminHomeRedirect::url()` выбирает первый доступный маршрут по приоритету (dashboard → static pages → …). У **manager** нет `dashboard.view`, поэтому домашней страницей часто становится список статических страниц.

Используется в: `LoginController`, `AdminEntryController`, `RedirectIfAuthenticated`, `AdminLayoutComposer` (`adminHomeUrl`), ссылка-бренд в `topnav`.

## Представления (Blade)

- **Композер:** `App\View\Composers\AdminLayoutComposer` зарегистрирован для `admin.layouts.sb-admin` и **`admin.pages.*`** (`App\Providers\AppServiceProvider`), чтобы во всех страницах были `adminUser`, `adminHomeUrl`, `activeSidebar`.
- **Сайдбар:** `resources/views/admin/partials/sidebar.blade.php` — обёртки **`@can`** / **`@canany`** по строкам permission (как в таблице выше).

## Модуль Permissions

- Страница: **`GET /admin/permissions`**, имя маршрута `admin.permissions.index`.
- JSON для клиентской таблицы: `GET /admin/api/permissions/simple`, право **`permissions.view`** (только роль **admin**).

## Log Viewer

`config/log-viewer.php` — маршруты пакета защищены: `web`, `auth:admin`, `use_admin_guard`, `permission:log_viewer.view`.

## База данных

- Миграции Spatie: `database/migrations/*_create_permission_tables.php` (таблицы `permissions`, `roles`, `model_has_*`, `role_has_permissions`).
- Дополнительно: `database/migrations/*_add_role_id_to_administrators_table.php`.

Команды в проекте обычно через Sail: `./vendor/bin/sail artisan migrate`, `db:seed`.

## Расширение (новый раздел админки)

1. Добавить константу в `AdminPermission` и строку в `AdminPermission::all()`.
2. В `RolesAndPermissionsSeeder`: выдать право роли **admin** (и при необходимости **manager**); вызвать `forgetCachedPermissions` уже есть в сидере.
3. Повесить на маршруты middleware `permission:...`.
4. В сайдбаре обернуть пункт в `@can('...')`.
5. При необходимости добавить URL в приоритет `AdminHomeRedirect`.

## Связанные файлы

| Назначение | Путь |
|------------|------|
| Константы прав / ролей | `app/Authorization/AdminPermission.php`, `AdminRole.php` |
| Сидеры | `database/seeders/RolesAndPermissionsSeeder.php`, `AdminSeeder.php` |
| Контроллер списка прав | `app/Http/Controllers/Admin/PermissionController.php` |
| JSON списков без пагинации | `AdministratorSimpleTableDataController`, `PermissionSimpleTableDataController` |
| Виджет таблицы (режим client) | `resources/views/admin/partials/bootstrap-table-widget.blade.php` (`serverSidePagination`) |

## Тесты

Feature-тесты создают администратора с полным доступом через `Tests\TestCase::adminWithFullAccess()` (внутри вызывается `RolesAndPermissionsSeeder`). См. `tests/Feature/Admin/*`.
