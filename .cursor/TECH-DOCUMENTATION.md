# Техническая документация: bootstrap-admin-panel

Документ описывает **текущее состояние** репозитория: админ-панель на **Laravel 10** с UI на **Bootstrap 5** и темой **Start Bootstrap SB Admin**. Дополнительные соглашения и «эталонные» паттерны см. в `.cursor/skills/` (см. раздел [Связанная документация](#связанная-документация)).

---

## 1. Назначение проекта

- Веб-приложение на Laravel с **отдельной зоной администратора** по префиксу URL `/adm`.
- Включает **демо-страницы** макета (дашборд, графики, таблицы, формы, варианты layout), **CRUD статических страниц** с древовидной связью `parent_id`, и **таблицу сотрудников** (`employees`) как пример серверной пагинации для Bootstrap Table.
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
  Exceptions/Handler.php
  Helpers/                 # например SalaryHelper
  Http/
    Controllers/Admin/     # веб-админка
    Controllers/Admin/Api/ # JSON для таблиц (bootstrap-table)
    Controllers/Admin/Auth/
    Controllers/Admin/Layout/
    Middleware/
    Requests/Admin/...
    Resources/Admin/...    # JsonResource для строк таблиц
  Models/                  # User, Administrator, Employee, StaticPage
  Notifications/
  Providers/
  Services/Admin/          # листинги, дашборд
bootstrap/app.php          # классическое создание приложения Laravel 10
config/
database/
  factories/
  migrations/
  seeders/                 # AdminSeeder, StaticPageSeeder, DatabaseSeeder
public/
resources/
  css/app.css
  js/app.js, bootstrap.js
  views/admin/             # Blade: layouts, partials, pages
  themes/admin/assets/     # scss/js темы SB Admin
routes/
  web.php                  # корень + вся админка
  api.php
  console.php
tests/
  Feature/, Unit/
```

**Отличие от «эталонного» описания в `.cursor/skills/ARCHITECTURE.md`:** в этом репозитории нет разделения `routes/admin-web.php` / `admin-api.php` — админские маршруты сгруппированы в `routes/web.php` под префиксом `adm`. Представления лежат в `resources/views/admin/`, а не в `resources/admin/views/`.

---

## 4. Маршрутизация

### 4.1 Регистрация

- `RouteServiceProvider` подключает `routes/web.php` с middleware `web` и `routes/api.php` с префиксом `api` и middleware `api`.

### 4.2 Публичные маршруты

| Метод | Путь | Описание |
|-------|------|----------|
| GET | `/` | `welcome` |

### 4.3 Админка: префикс `adm`, имя маршрутов `admin.*`

Группа: `Route::prefix('adm')->name('admin.')`.

| Условие | Маршруты |
|---------|----------|
| Без middleware auth | `GET /adm` → `AdminEntryController`: если уже залогинен в guard `admin` — редирект на дашборд, иначе форма логина |
| `guest:admin` | логин, регистрация, запрос/сброс пароля |
| `auth:admin` | дашборд, демо-layout, charts/tables/forms/blank, демо ошибок, API таблиц, resource `static-pages` |

Имена важных маршрутов:

- `admin.entry` — входная точка `/adm`
- `admin.dashboard` — `/adm/dashboard`
- `admin.api.employees` — `GET /adm/api/employees` (JSON для таблицы)
- `admin.api.static-pages.table` — `GET /adm/api/static-pages/table`
- `admin.static-pages.*` — стандартный `Route::resource` для CRUD статических страниц

Полный список — в `routes/web.php`.

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

- `auth:admin` для защищённых маршрутов под `/adm/...`.
- `guest:admin` для страниц входа/регистрации/сброса пароля.
- `Authenticate::redirectTo`: для путей `adm` / `adm/*` редирект на `route('admin.entry')`, для JSON-запросов — без редиректа.
- `RedirectIfAuthenticated`: при уже выполненном входе под `admin` — редирект на `admin.dashboard`.

### 5.4 Тестовый администратор

`Database\Seeders\AdminSeeder` создаёт/обновляет запись с email `admin@mail.com` и паролем `password` (хэш через `Hash::make`). Подключение сидера — в `DatabaseSeeder` при необходимости.

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

### 6.3 `administrators`

Стандартные поля пользователя админки (в т.ч. `remember_token`); отдельная миграция под таблицу.

### 6.4 Прочее

- `users` — стандартная Laravel-таблица (для guard `web`).
- Таблицы сброса паролей: `password_reset_tokens`, `administrator_password_reset_tokens`.

---

## 7. Слои приложения и паттерны

### 7.1 Контроллеры

- **Тонкие:** валидация через `FormRequest`, ответы `View` / `RedirectResponse` / `JsonResponse`.
- **API-контроллеры таблиц** — invokable-классы в `App\Http\Controllers\Admin\Api\*`, возвращают JSON в формате Bootstrap Table (см. ниже).

### 7.2 FormRequest

Расположение: `app/Http/Requests/Admin/...`  
Примеры: `StoreStaticPageRequest`, `UpdateStaticPageRequest`, запросы для auth.

### 7.3 Сервисы

| Класс | Роль |
|-------|------|
| `EmployeeListingService` | Пагинация, поиск, сортировка для сотрудников; метод `paginateForBootstrapTable()` |
| `StaticPageListingService` | Аналогично для `StaticPage` |
| `AdminDashboardService` | Карточки на дашборде (в т.ч. счётчик сотрудников) |

Поиск реализован через `LIKE` и при необходимости `CAST` полей в строку; для SQLite используется `TEXT`, для остальных драйверов — `CHAR` (метод `stringCastType`).

### 7.4 API Resources

- `EmployeeResource`, `StaticPageResource` — нормализация полей для JSON (даты, формат salary).

### 7.5 Формат ответа для Bootstrap Table

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

- `resources/views/admin/layouts/sb-admin.blade.php` — фиксированный topnav, боковое меню, контент, подключение Vite для `sb-admin-scripts.js`, flash-сообщения, `@stack('scripts')`.

### 8.2 Частичные шаблоны

`resources/views/admin/partials/` — `head`, `sidebar`, `topnav`, `footer`, `admin-ui-flash`, виджеты таблиц (`bootstrap-table-widget`, `employees-datatable` и т.д.).

### 8.3 Модули страниц

- Статические страницы: `resources/views/admin/pages/static-pages/` (`view` как index-список с таблицей, `create`, `edit`, `show`).
- Демо: `admin/dashboard`, `charts`, `tables`, `forms`, `blank`, варианты layout в `Layout/*`.

### 8.4 Сборка Vite

Файл `vite.config.js` задаёт входные точки:

- `resources/css/app.css`
- `resources/js/app.js`
- `resources/themes/admin/assets/css/app.scss`
- `resources/themes/admin/assets/js/admin-bootstrap-table.js`
- `resources/themes/admin/assets/js/sb-admin-scripts.js`
- `resources/themes/admin/assets/js/admin-chart-demos.js`

Сервер разработки: `host: 0.0.0.0`, порт `5173`, HMR `localhost`, `watch.usePolling: true` (удобно для Docker/Sail).

---

## 9. Тестирование

- Фреймворк: **PHPUnit 10** (`phpunit.xml`).
- Окружение тестов: `APP_ENV=testing`, БД **sqlite `:memory:`**, `SESSION_DRIVER=array`, и т.д.
- Примеры: `tests/Feature/Admin/AdminAuthTest.php`, `AdminPagesTest.php`, `tests/Unit/Services/AdminDashboardServiceTest.php`, `tests/Unit/Helpers/SalaryHelperTest.php`.

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

---

## 13. Версия документа

- Составлено по состоянию репозитория **bootstrap-admin-panel** (Laravel **10**, PHP **^8.1**).
- При существенных изменениях маршрутов, моделей или стека имеет смысл обновить этот файл и раздел «Связанная документация».
