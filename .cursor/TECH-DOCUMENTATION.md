# Техническая документация: bootstrap-admin-panel

Документ описывает **текущее состояние** репозитория: админ-панель на **Laravel 10** с UI на **Bootstrap 5** и темой **Start Bootstrap SB Admin**. Дополнительные соглашения и «эталонные» паттерны см. в `.cursor/skills/` (см. раздел [Связанная документация](#связанная-документация)).

---

## 1. Назначение проекта

- Веб-приложение на Laravel с **отдельной зоной администратора** по префиксу URL `/admin`.
- Включает **демо-страницы** макета (дашборд, графики, таблицы, формы, варианты layout), **CRUD статических страниц** с древовидной связью `parent_id`, **дерево категорий (Category tree)** c DnD, группу **Settings** в сайдбаре: **Main menu** (древовидный DnD без фиксированного лимита глубины), **301 Redirects** (табличный CRUD + глобальный middleware 301), **Users** (CRUD администраторов), и **таблицу сотрудников** (`employees`) как пример серверной пагинации для Bootstrap Table.
- **Публичные GET-редиректы 301** по путям из `seo_redirects` обрабатывает глобальный middleware (исключение `admin/*`); до применения маршрута проверяется `Schema::hasTable('seo_redirects')` (например, тесты без миграций).
- Публичная часть минимальна: маршрут `/` отдаёт приветственную страницу `welcome`.

---

## 2. Стек технологий

### 2.1 Backend (composer)

| Компонент | Версия / ограничение |
|-----------|----------------------|
| PHP | `^8.1` |
| laravel/framework | `^10.10` |
| laravel/sanctum | `^3.3` (зарегистрирован; в `routes/api.php` — пример `/api/user`) |
| guzzlehttp/guzzle | `^7.2` |
| laravel/tinker | `^2.8` |

**Dev:** laravel/sail, laravel/pint, laravel/boost, phpunit/phpunit `^10.1`, и др. (см. `composer.json`).

### 2.2 Frontend (npm)

| Компонент | Назначение |
|-----------|------------|
| Vite `^5` + laravel-vite-plugin | Сборка CSS/JS, HMR |
| Bootstrap `^5.3.8` | Сетка, компоненты UI |
| sass / sass-embedded | Стили темы админки |
| axios | HTTP-клиент (подключается через `resources/js/bootstrap.js`) |
| dripicons | Иконки (зависимость проекта) |

В разметке админ-лейаута также подключается **Bootstrap 5.2.3 с CDN** (`bootstrap.bundle.min.js`) для совместимости со скриптами темы.

### 2.3 Шаблон UI

- Основа: [Start Bootstrap — SB Admin](https://github.com/StartBootstrap/startbootstrap-sb-admin) (адаптация под Blade + Vite).
- Ключевые файлы темы: `resources/themes/admin/assets/` (scss, js).

---

## 3. Структура проекта (актуальная)

```
app/
  Console/Kernel.php
  Enums/                   # например AdminErrorPage (демо-страницы ошибок)
  Exceptions/Handler.php
  Helpers/                 # SalaryHelper, AdminHelper (ассеты темы)
  Http/
    Controllers/Admin/     # веб-админка
    Controllers/Admin/Api/ # JSON для таблиц (bootstrap-table)
    Controllers/Admin/Auth/
    Controllers/Admin/Layout/
    Middleware/            # в т.ч. ApplySeoRedirectMiddleware (глобально в Kernel)
    Requests/Admin/...     # в т.ч. Auth/*, StaticPage/*, CategoryTree/*, MainMenu/*, SeoRedirect/*, Administrator/*
    Resources/Admin/...    # JsonResource для строк таблиц
  Models/                  # User, Administrator, Employee, StaticPage, CategoryTree, MainMenuItem, SeoRedirect
  Notifications/
  Providers/
  Services/Admin/          # листинги, дашборд, CategoryTreeService, MainMenuItemService
  View/Composers/          # AdminLayoutComposer (sidebar, пользователь)
bootstrap/app.php          # классическое создание приложения Laravel 10
config/
database/
  factories/
  migrations/
  seeders/                 # AdminSeeder, StaticPageSeeder, CategoryTreeSeeder, ConfigurationModuleSeeder, DatabaseSeeder
public/
resources/
  css/app.css
  js/app.js, bootstrap.js
  views/admin/             # Blade: layouts, partials, pages
  themes/admin/assets/     # scss/js темы SB Admin
routes/
  web.php                  # корень (welcome)
  admin-web.php            # вся admin web-часть (auth, страницы, CRUD)
  admin-api.php            # admin AJAX/JSON эндпоинты (bootstrap-table)
  api.php
  console.php
tests/
  Feature/, Unit/
```

**Отличие от «эталонного» описания в `.cursor/skills/ARCHITECTURE.md`:** структура файлов роутов совпадает (`web.php`, `admin-web.php`, `admin-api.php`). Представления лежат в `resources/views/admin/`, а не в `resources/admin/views/`.

---

## 4. Маршрутизация

### 4.1 Регистрация

`RouteServiceProvider` регистрирует четыре файла:

| Файл | Middleware | Описание |
|------|------------|----------|
| `routes/web.php` | `web` | Публичные web-маршруты (welcome) |
| `routes/admin-web.php` | `web` | Admin web-маршруты (HTML-страницы) |
| `routes/admin-api.php` | `web` | Admin AJAX/JSON эндпоинты для bootstrap-table |
| `routes/api.php` | `api` + prefix `api` | Публичный API |

`admin-api.php` зарегистрирован под `web`-middleware (не `api`), чтобы сохранить сессионную авторизацию `auth:admin`.

### 4.2 Публичные маршруты

| Метод | Путь | Описание |
|-------|------|----------|
| GET | `/` | `welcome` |

### 4.3 Админка: префикс `admin`, имя маршрутов `admin.*`

Оба файла `admin-web.php` и `admin-api.php` используют `Route::prefix('admin')->name('admin.')`.

**`routes/admin-web.php`** — HTML-страницы:

| Условие | Маршруты |
|---------|----------|
| Без middleware auth | `GET /admin` → `AdminEntryController`: если уже залогинен в guard `admin` — редирект на дашборд, иначе форма логина |
| `guest:admin` | `POST` логина; регистрация (`GET/POST`); запрос/сброс пароля (`GET/POST`) |
| `auth:admin` | дашборд, layout-демо, charts/tables/forms/blank, демо ошибок, resource `static-pages`, `category-tree`, группа **Settings:** `main-menu` (с `store`/`save-order`), resource `seo-redirects` (без `show`), resource `administrators` (без `show`) |

**`routes/admin-api.php`** — AJAX/JSON для bootstrap-table (все под `auth:admin`):

| Маршрут | Контроллер |
|---------|------------|
| `GET /admin/api/employees` | `EmployeeTableDataController` |
| `GET /admin/api/static-pages/table` | `StaticPageTableDataController` |
| `GET /admin/api/administrators/table` | `AdministratorTableDataController` |
| `GET /admin/api/seo-redirects/table` | `SeoRedirectTableDataController` |

Роуты с несколькими методами сгруппированы через `Route::controller()` (RegisterController, PasswordResetLinkController, NewPasswordController, AdminErrorDemoController, CategoryTreeController, MainMenuItemController).

Имена важных маршрутов:

- `admin.entry` — входная точка `/admin`
- `admin.dashboard` — `/admin/dashboard`
- `admin.api.employees` — `GET /admin/api/employees` (JSON для таблицы)
- `admin.api.static-pages.table` — `GET /admin/api/static-pages/table`
- `admin.static-pages.*` — стандартный `Route::resource` для CRUD статических страниц
- `admin.category-tree.index` — `GET /admin/category-tree`
- `admin.category-tree.save-order` — `POST /admin/category-tree/save-order`
- `admin.layouts.static`, `admin.layouts.sidenav-light` — варианты демо-layout
- `admin.errors.401`, `admin.errors.404-demo`, `admin.errors.500-demo` — демо ошибок (Blade)
- `admin.main-menu.*` — `GET/POST /admin/main-menu`, `POST /admin/main-menu/save-order`, `PUT/DELETE /admin/main-menu/{main_menu_item}`
- `admin.seo-redirects.*` — CRUD 301-редиректов
- `admin.administrators.*` — CRUD администраторов

**Глобальный HTTP-middleware** (массив `$middleware` в `app/Http/Kernel.php`, не в группе `web`): `ApplySeoRedirectMiddleware` — на GET-запросы вне `admin` ищет активный `SeoRedirect` по пути, отдаёт 301; если таблицы `seo_redirects` ещё нет — `Schema::hasTable` пропускает обработку.

Полный список — в `routes/admin-web.php` и `routes/admin-api.php`.

---

## 5. Аутентификация и авторизация

### 5.1 Guards и модели

| Guard | Provider | Модель | Таблица |
|-------|----------|--------|---------|
| `web` (default) | `users` | `App\Models\User` | `users` |
| `admin` | `administrators` | `App\Models\Administrator` | `administrators` |

Настройка: `config/auth.php`.

### 5.2 Администратор

- Модель `Administrator` расширяет `Authenticatable`, использует `HasFactory`, `Notifiable`, `CanResetPassword`.
- Пароль в `$casts` как `hashed`.
- Сброс пароля: отдельная таблица токенов `administrator_password_reset_tokens`, уведомление `App\Notifications\AdminResetPassword`.

### 5.3 Поведение middleware

- `auth:admin` для защищённых маршрутов под `/admin/...`.
- `guest:admin` для страниц входа/регистрации/сброса пароля.
- `Authenticate::redirectTo`: для путей `admin` / `admin/*` редирект на `route('admin.entry')`, для JSON-запросов — без редиректа.
- `RedirectIfAuthenticated`: при уже выполненном входе под `admin` — редирект на `admin.dashboard`.

### 5.4 Тестовый администратор

`Database\Seeders\AdminSeeder` создаёт/обновляет запись с email `admin@mail.com` и паролем `password` (хэш через `Hash::make`). `Database\Seeders\ConfigurationModuleSeeder` — по **10** демо-записей через фабрики: `MainMenuItem`, `SeoRedirect`, `Administrator` (после сидеров Static/Category, до массовой фабрики `Employee` в `DatabaseSeeder`). Подключение — в `DatabaseSeeder`.

---

## 6. Доменные модели и БД

### 6.1 `employees`

Миграция: `id`, `name`, `position`, `office`, `age`, `start_date`, `salary` (decimal 14,2), timestamps.

Использование: демо-данные для таблиц на дашборде и странице «Tables»; форматирование зарплаты через `App\Helpers\SalaryHelper` в `EmployeeResource`.

### 6.2 `static_pages`

Поля: `parent_id` (по умолчанию `0`, индекс), `code`, `title`, `description`, `content`, `sort_no`, `slug` (unique), `is_active`, timestamps.

Модель `StaticPage`:

- связи `parent()` / `children()`
- scope `ordered()` — сортировка по `sort_no`, затем `id`

Удаление: в `StaticPageController::destroy` запрещено, если есть дочерние страницы (`parent_id` указывает на текущую запись).

### 6.3 `category_trees`

Поля: `parent_id` (по умолчанию `0`, индекс), `title`, `slug` (nullable, unique), `description` (nullable, text), `sort_no`, `is_active`, timestamps.

Модель `CategoryTree`:

- связи `parent()` / `children()`
- scope `ordered()` — сортировка по `sort_no`, затем `id`

Дерево формируется группировкой всех записей по `parent_id`; `parent_id = 0` — корневые узлы. Сервис `CategoryTreeService` содержит `buildGroupedTree()` и рекурсивный `saveOrder()` для пересчёта `parent_id` + `sort_no` после drag-and-drop, а также `allNodesOrderedForMeta()` (данные для UI) и `deleteNodeReparentingChildren()`: операция выполняется в одной транзакции БД; прямые дочерние узлы при удалении переносятся к родителю удаляемой записи (с новым `sort_no` в конце списка среди соседей, порядок детей относительно друг друга сохраняется), затем удаляемая запись удаляется.

Редактирование узла — в модальном окне (Bootstrap) на странице дерева; валидация в `UpdateCategoryTreeRequest` (в т.ч. запрет циклов в иерархии по `parent_id`). Удаление — `adminBootstrapTableDelete` (подтверждение через `admin-ui`), тост `adminNotify` по результату.

Маршруты:
- `admin.category-tree.index` — `GET /admin/category-tree`
- `admin.category-tree.save-order` — `POST /admin/category-tree/save-order` (JSON body `{ nodes: [...] }`)
- `admin.category-tree.update` — `PUT /admin/category-tree/{category_tree}` (form/json; ответ JSON с `message` при `Accept: application/json`)
- `admin.category-tree.destroy` — `DELETE /admin/category-tree/{category_tree}` (JSON `message` при `Accept: application/json`, иначе редирект)

### 6.4 `main_menu_items`

Поля: `parent_id` (0 — корень), `sort_no`, `title`, `slug` (nullable, unique), `is_active`, timestamps.

Поведение: как у **Category tree** по DnD и сохранению порядка. Ограничение глубины снято. Сервис `MainMenuItemService` — `buildGroupedTree()`, `saveOrder()`, `deleteNodeReparentingChildren()`, `allNodesOrderedForMeta()`. UI: `resources/views/admin/pages/main-menu/`, SortableJS, префикс классов `mm-`, SCSS `blocks/_main-menu.scss`, создание узла — форма/модалка `POST` `admin.main-menu.store`.

### 6.5 `seo_redirects`

Поля: `slug_from` (unique, путь без ведущего слеша), `slug_to` (до 2000 символов), `is_active`, timestamps. Список в админке: Blade-таблица, удаление — `adminBootstrapTableDelete` + JSON-ответ контроллера при `Accept: application/json` (как у `StaticPage`).

### 6.6 `administrators`

Стандартные поля пользователя админки (в т.ч. `remember_token`); колонка **`is_active`** (boolean, default true). **Вход** в guard `admin`: `LoginRequest` передаёт в `attempt` также `is_active => true` (Eloquent-фильтр). **CRUD** в разделе Settings → Users: пароли в формах с partial `admin/partials/admin-password-fields.blade.php` (переключатель видимости), удаление «себя» отклоняется. Для `destroy` при AJAX возвращается JSON (`422` при запрете удаления), иначе редирект + flash; flash успеха/ошибки выводятся через `admin-ui-flash` + `adminNotify`, без дублирующих Bootstrap `alert` на index-страницах.

### 6.7 Прочее

- `users` — стандартная Laravel-таблица (для guard `web`).
- Таблицы сброса паролей: `password_reset_tokens`, `administrator_password_reset_tokens`.

---

## 7. Слои приложения и паттерны

### 7.1 Контроллеры

- **Тонкие:** валидация через `FormRequest`, ответы `View` / `RedirectResponse` / `JsonResponse`.
- **API-контроллеры таблиц** — invokable-классы в `App\Http\Controllers\Admin\Api\*`, возвращают JSON в формате Bootstrap Table (см. ниже).

### 7.2 FormRequest

Расположение: `app/Http/Requests/Admin/...`  
Примеры: `StaticPage/StoreStaticPageRequest`, `StaticPage/UpdateStaticPageRequest`, `CategoryTree/*`, `MainMenu/*`, `SeoRedirect/*`, `Administrator/*`, `Auth/*` (вход, сброс пароля).

### 7.3 Сервисы

| Класс | Роль |
|-------|------|
| `EmployeeListingService` | Пагинация, поиск, сортировка для сотрудников; метод `paginateForBootstrapTable()` |
| `StaticPageListingService` | Аналогично для `StaticPage` |
| `AdminDashboardService` | Карточки на дашборде (в т.ч. счётчик сотрудников) |
| `CategoryTreeService` | Группировка дерева, сохранение порядка DnD, перенос детей при удалении, выборка узлов для UI |
| `MainMenuItemService` | То же для главного меню, максимальная глубина дерева 3 |

Поиск реализован через `LIKE` и при необходимости `CAST` полей в строку; для SQLite используется `TEXT`, для остальных драйверов — `CHAR` (метод `stringCastType`).

### 7.4 API Resources

- `EmployeeResource`, `StaticPageResource` — нормализация полей для JSON (даты, формат salary).

### 7.5 Enum и вспомогательные классы админки

| Класс | Роль |
|-------|------|
| `App\Enums\AdminErrorPage` | Метаданные для демо-страниц ошибок 401/404/500 (`AdminErrorDemoController`) |
| `App\Helpers\AdminHelper` | `themeAssetDataUri()` — data URI для файлов из `resources/themes/admin/assets/` (иллюстрации в ошибках) |

`App\View\Composers\AdminLayoutComposer` вешается в `AppServiceProvider` на layout’ы `admin.layouts.sb-admin`, `admin.layouts.sb-admin-static`, `admin.layout-sidenav-light` и передаёт в шаблоны `adminUser` и `activeSidebar` (по `request()->routeIs()`: `static-pages`, `category-tree`, `settings-redirects`, `settings-main-menu`, `settings-administrators` и др.). В `sidebar` для вложенных разделов (Layouts, Pages, **Settings**) класс `collapsed` на триггере снимается, если активен дочерний маршрут, чтобы chevron оставался в «открытом» состоянии.

### 7.6 Формат ответа для Bootstrap Table

Эндпоинты `EmployeeTableDataController` и `StaticPageTableDataController` возвращают:

```json
{
  "total": <number>,
  "rows": [ { ... }, ... ]
}
```

Параметры запроса (сервер): `limit`, `offset`, `search`, `sort`, `order` — обрабатываются в listing-сервисах.

---

## 8. Представления (Blade) и фронтенд

### 8.1 Основной layout

- `resources/views/admin/layouts/sb-admin.blade.php` — фиксированный topnav, боковое меню, контент, подключение Vite для `sb-admin-scripts.js`, `admin.partials.admin-ui-flash` (flash `success` / `error` / `status` → `adminNotify` на `DOMContentLoaded`), `@stack('scripts')`, общий UI-оверлей из `admin.partials.ui-shell`.
- Дополнительно: `layouts/sb-admin-static.blade.php`, `layout-sidenav-light.blade.php` (корень `resources/views/admin/`), `layouts/auth.blade.php`, `layouts/error.blade.php` — варианты макетов и страниц ошибок.

### 8.2 Частичные шаблоны

`resources/views/admin/partials/` — `head`, `sidebar`, `topnav`, `footer`, `admin-ui-flash`, виджеты таблиц (`bootstrap-table-widget`, `employees-datatable` и т.д.).

### 8.3 Модули страниц

- Статические страницы: `resources/views/admin/pages/static-pages/` (`view` как index-список с таблицей, `create`, `edit`, `show`).
- Дерево каталога: `resources/views/admin/pages/category-tree/index.blade.php` + рекурсивный partial `partials/tree-node.blade.php`. Вложенный `<ol class="ct-list--nested">` рендерится для каждого узла (в т.ч. пустой), что обеспечивает корректный drop при смене уровня вложенности. SortableJS (CDN) инициализируется на всех `<ol>` через plain JS + `document.addEventListener('DOMContentLoaded')`. Во время drag класс `ct-is-dragging` на `#ct-root` раскрывает пустые drop-зоны через SCSS.
- **Main menu:** `pages/main-menu/` — то же DnD-поведение с префиксом `mm-`, `blocks/_main-menu.scss`.
- **301 Redirects / Administrators:** `pages/seo-redirects/`, `pages/administrators/` — таблицы, удаление кнопкой `adminBootstrapTableDelete(url)`; на странице — блок `#admin-bootstrap-table-i18n` с текстом подтверждения, подключение `admin-bootstrap-table.js` через Vite; подтверждение — `adminUiDialog` (fallback `window.confirm` в `admin-bootstrap-table.js`), результат — `adminNotify` + перезагрузка при успехе.
- Сайдбар: `partials/sidebar` — сворачиваемая группа **Settings** (Redirects, Main menu, Users) с иконкой шестерёнки.
- Демо: `admin/dashboard`, `charts`, `tables`, `forms`, `blank`, варианты layout (`layout-static.blade.php`, `layout-sidenav-light.blade.php` и контроллеры в `Layout/`), демо ошибок в `admin/errors/`.

### 8.4 Сборка Vite

Файл `vite.config.js` задаёт входные точки:

- `resources/css/app.css`
- `resources/js/app.js`
- `resources/themes/admin/assets/css/app.scss`
- `resources/themes/admin/assets/js/admin-bootstrap-table.js`
- `resources/themes/admin/assets/js/sb-admin-scripts.js`
- `resources/themes/admin/assets/js/admin-chart-demos.js`

Сервер разработки: `host: 0.0.0.0`, порт `5173`, HMR `localhost`, `watch.usePolling: true` (удобно для Docker/Sail).

**Важно для inline-скриптов в `@push('scripts')`:** Vite-бандлы подключаются как `type="module"` (defer), поэтому `window.jQuery` и другие глобалы из Vite-бандлов **недоступны** в момент выполнения обычных inline-скриптов. Паттерн для inline-JS: plain JavaScript + `document.addEventListener('DOMContentLoaded', ...)` + `fetch` вместо `$.ajax`. CDN-библиотеки (SortableJS, Bootstrap CDN) загружаются синхронно и доступны немедленно.

---

## 9. Тестирование

- Фреймворк: **PHPUnit 10** (`phpunit.xml`).
- Окружение тестов: `APP_ENV=testing`, БД **sqlite `:memory:`**, `SESSION_DRIVER=array`, и т.д.
- Примеры: `AdminAuthTest`, `AdminPagesTest`, `StaticPageAdminTest`, `CategoryTreeAdminTest`, `MainMenuItemAdminTest`, `SeoRedirectAdminTest`, `AdministratorAdminTest`, `tests/Unit/Services/AdminDashboardServiceTest`, `tests/Unit/Helpers/SalaryHelperTest`.

Запуск (в проектах с Sail обычно): `./vendor/bin/sail artisan test` или фильтр по имени теста — по соглашению команды.

---

## 10. Локализация

- Файлы переводов: `lang/en/admin.php` и стандартные каталоги Laravel.
- В коде встречаются вызовы `__()` для flash и сообщений.

---

## 11. Развёртывание и разработка

Кратко (детали и команды — в корневом `README.md`):

1. Копирование `.env`, настройка БД (в Docker — переменные Sail: `DB_HOST=mysql`, порты `APP_PORT`, `VITE_PORT` и т.д.).
2. Миграции, `key:generate`, при необходимости `storage:link`.
3. Установка npm-зависимостей и сборка: `npm run dev` или `npm run build` (в Sail — через `./vendor/bin/sail npm ...`).
4. Если ассеты не подхватываются — убедиться, что выполнен production build или запущен Vite dev server.

---

## 12. Связанная документация

| Путь | Содержание |
|------|------------|
| `.cursor/skills/use-cursor-skills-folder/SKILL.md` | Указатель на навыки репозитория |
| `.cursor/skills/STACK.md` | Стек (в т.ч. эталонный reference; часть пакетов может отличаться) |
| `.cursor/skills/ARCHITECTURE.md` | Слои, разделение Shop/Admin/Web/Api в эталоне |
| `.cursor/skills/PATTERNS.md` | Table CRUD, Tree CRUD, фильтры через scopes |
| `.cursor/skills/ADMIN_PANEL_PATTERN.md` | Паттерны админ-модулей, таблицы, формы |
| `.cursor/skills/SKILLS.md` | Соглашения по слоям и именованию |
| `README.md` | Установка, Sail, Vite, troubleshooting Rollup |
| `docs/admin-bootstrap-table.md` | Справка по bootstrap-table в админке (маршруты API, JSON, виджет Blade) |

---

## 13. Версия документа

- Составлено по состоянию репозитория **bootstrap-admin-panel** (Laravel **10**, PHP **^8.1**). Префикс URL админки: **`/admin`**.
- **2026-04-26 (актуализация):** модуль **Settings** (Main menu, 301 Redirects, Users/Administrators), глобальный middleware редиректов, `ConfigurationModuleSeeder`, тесты перечислены в §9; уточнены сайдбар, `admin-ui-flash`, `adminBootstrapTableDelete`.
- **2026-04-28 (актуализация):** admin-маршруты вынесены из `web.php` в `routes/admin-web.php` (HTML) и `routes/admin-api.php` (AJAX/JSON); оба зарегистрированы в `RouteServiceProvider` под `web`-middleware. Обновлены §3, §4.1, §4.3.
- При существенных изменениях маршрутов, моделей или стека имеет смысл обновить этот файл и раздел «Связанная документация».
