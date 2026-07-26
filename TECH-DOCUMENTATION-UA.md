# Технічна документація: bootstrap-admin-panel

Документ описує **поточний стан** репозиторію: каркас адмін-панелі на **Laravel 10** з UI на **Bootstrap 5** та темою **Start Bootstrap SB Admin**.

Швидкий старт: [README_UA.md](README_UA.md) (UA), [README.md](README.md) (EN).

---

## 1. Призначення проєкту

- Веб-застосунок на Laravel з окремою адміністративною зоною за префіксом `/admin`.
- Призначений як **базовий скелет** адмінок продуктів (контент, каталоги, меню, SEO, користувачі).
- Включає:
  - dashboard і demo-сторінки layout (charts, tables, forms),
  - CRUD статичних сторінок,
  - **дерево категорій (catalog)** з рекурсивною ієрархією та **drag-and-drop** (SortableJS),
  - **Main Menu** з необмеженою вкладеністю та тим самим DnD UX,
  - банери з вкладеннями зображень і soft deletes,
  - SEO 301-редиректи (публічні GET через глобальний middleware),
  - список employees як еталон **server-side Bootstrap Table**,
  - CRUD адміністраторів із **Spatie roles/permissions** (`admin` / `manager`).

---

## 2. Технологічний стек

### Backend

| Компонент | Версія / примітки |
|---|---|
| PHP | `^8.1` (образ Sail: 8.2) |
| Laravel | `^10.10` |
| MySQL | 8.x (Docker / Sail) |
| Redis | cache / queues (Sail) |
| Sanctum | `^3.3` |
| Spatie Laravel Permission | `^6.25` |
| arcanedev/log-viewer | `^10.1` |
| Laravel Sail | `^1.55` |
| Guzzle | `^7.2` |
| PHPUnit | `^10.1` |

### Frontend

| Компонент | Призначення |
|---|---|
| Vite 5 | Збірка assets |
| Bootstrap 5.3 | UI |
| Sass | Теми / SCSS |
| Axios | HTTP-клієнт |
| jQuery + bootstrap-table | Серверні списки в адмінці |
| SortableJS | Вкладені drag-and-drop дерева |
| Jodit | WYSIWYG-редактор |
| Dripicons / Font Awesome | Іконки |

### UI Theme

- [Start Bootstrap — SB Admin](https://github.com/startbootstrap/startbootstrap-sb-admin)

---

## 3. Структура проєкту

```text
app/
  Authorization/          # константи AdminPermission, AdminRole
  Http/
    Controllers/Admin/    # Web + Api (тонкі контролери)
    Requests/Admin/       # FormRequest на дію
    Resources/Admin/      # рядки Bootstrap Table / JSON
  Models/
  Services/Admin/         # бізнес-логіка (дерева, listing, uploads)
  Helpers/
resources/
  views/admin/
  themes/admin/assets/
routes/
  web.php
  admin-web.php
  admin-api.php
  api.php
database/migrations/
docs/
tests/Feature|Unit/
```

---

## 4. Маршрутизація

### Публічні маршрути

| Метод | Шлях | Примітки |
|---|---|---|
| GET | `/` | Welcome |
| GET | `/{slug}` (через middleware) | Активні SEO 301 з `seo_redirects` |

### Admin-маршрути

Усі admin-маршрути:

- префікс: `/admin`
- name: `admin.*`
- auth: `auth:admin` (+ перевірки permissions)

Модулі: автентифікація, dashboard, static pages, category tree, main menu, banners, employees/tables, SEO redirects, administrators, permissions, log viewer.

Ендпоінти переупорядкування дерев приймають вкладений JSON (`save-order`) для Category Tree та Main Menu.

---

## 5. Автентифікація та авторизація

### Guards

| Guard | Модель |
|---|---|
| `web` | `User` |
| `admin` | `Administrator` |

### Можливості

- Окремий admin login / скидання пароля
- Middleware `auth:admin`
- **Spatie Permission** на guard `admin`
- Ролі: `admin` (повний доступ), `manager` (контентний піднабір)
- Blade `@can` + middleware для модулів

Деталі: [docs/admin-roles-and-permissions.md](docs/admin-roles-and-permissions.md).

---

## 6. Моделі бази даних

| Модель / таблиця | Роль |
|---|---|
| `administrators` | Адміни (`role_id`, `is_active`, синхронізація Spatie) |
| `category_trees` | Каталог: `parent_id`, `sort_no`, slug/content, `is_active` |
| `main_menu_items` | Меню: вкладені `parent_id` / `sort_no` |
| `static_pages` | Плоскі сторінки (список через Bootstrap Table) |
| `banners` | Банери + soft deletes + шлях до зображення |
| `seo_redirects` | 301 `slug_from` → `slug_to` |
| `employees` | Демо-дані для server-side таблиць |
| Spatie `roles` / `permissions` | RBAC для guard `admin` |

Конвенція дерев: `parent_id = 0` — корінь; поле порядку — `sort_no`.

---

## 7. Архітектура та патерни

### Потік запиту

```text
Route → Middleware → FormRequest → Controller → Service → Model → Blade / JSON Resource
```

### Controllers

Тонка оркестрація; валідація в FormRequest; запити й мутації — у services.

### Патерн дерев (ключова UX-відмінність)

- Спільна база: `App\Services\Admin\AbstractTreeService`
- Підкласи: `CategoryTreeService`, `MainMenuItemService`
- Frontend: вкладені списки SortableJS → bulk API `save-order`
- При видаленні діти перепідпорядковуються; при надмірній глибині пишеться warning у лог

### Патерн таблиць

- Blade-віджет `bootstrap-table-widget`
- Invokable `*TableDataController` + `*ListingService::paginateForBootstrapTable()`
- Формат відповіді: `{ "total", "rows" }`
- Хелпер: `BootstrapTableHelper`

Деталі: [docs/admin-bootstrap-table.md](docs/admin-bootstrap-table.md).

### API Resources

Форматування рядків списків (дати зазвичай `d.m.Y`, якщо так прийнято в модулі).

---

## 8. Blade Views та Frontend

### Layout

- `resources/views/admin/layouts/sb-admin.blade.php` — sidebar, top nav, flash, Vite

### Модулі (pages)

Static Pages, Category Tree, Main Menu, Banners, SEO Redirects, Administrators, Employees/Tables, Permissions, Dashboard demos.

### Редактори / JS

Jodit, SortableJS (дерева), Bootstrap Table (списки), Vite-скрипти адмінки.

---

## 9. Тестування

| Параметр | Значення |
|---|---|
| Фреймворк | PHPUnit 10 |
| Стиль | Feature-first (+ Unit за наявності) |
| БД у тестах | SQLite in-memory (`RefreshDatabase`) |
| Запуск | `./vendor/bin/sail artisan test` |

Покриття: admin CRUD, переупорядкування дерев (`save-order`), auth, listing-сервіси.

---

## 10. Локалізація

- Рядки UI в `lang/` (наприклад `lang/en/admin.php`)
- `__()` / `@lang` у Blade

---

## 11. Deployment / локальне середовище

Типовий локальний setup (Sail):

1. `composer install` → скопіювати `.env` → виставити `WWWUSER` / `WWWGROUP`
2. `./vendor/bin/sail up -d`
3. `sail artisan key:generate` && `storage:link`
4. `sail artisan migrate --seed`
5. `npm install` && `npm run dev` (або `build`)

Демо-логіни після seed: `admin@mail.com` / `password`, `manager@mail.com` / `password`.

---

## 12. Пов’язана документація

| Шлях | Опис |
|---|---|
| `README.md` / `README_UA.md` | Встановлення та щоденна робота |
| `docs/admin-bootstrap-table.md` | Server-side Bootstrap Table |
| `docs/admin-roles-and-permissions.md` | Spatie RBAC |
| `.cursor/skills/STACK.md` | Стек |
| `.cursor/skills/ARCHITECTURE.md` | Шари та потік запиту |
| `.cursor/skills/PATTERNS.md` | Table / Tree CRUD |
| `.cursor/skills/ADMIN_PANEL_PATTERN.md` | Патерни адмінки |

---

## 13. Версія документа

| Поле | Значення |
|---|---|
| Репозиторій | `bootstrap-admin-panel` |
| Фреймворк | Laravel 10 / PHP `^8.1` |
| UI | Bootstrap 5 + SB Admin |
| Дата документа | 2026-07-26 |

Акцент поточного коду: перевикористовуваний `AbstractTreeService`, SortableJS DnD для **каталогу** та **меню**, Bootstrap Table, Spatie RBAC, Sail і однопрохідний `migrate --seed`.
