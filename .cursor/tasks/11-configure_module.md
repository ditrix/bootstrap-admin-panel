# Задача 11: Configuration module

## TASK

Создать в админке раздел в сайдбаре: родительский пункт **Settings** с выпадающим подменю (см. раздел «Навигация (сайдбар)» ниже) и подмодулями **Main menu**, **Redirects**, **Users**. Детальные промпты — в таблице ниже.

### Подмодули

1. **Main menu** — tree CRUD по образцу модуля `CategoryTree` (`CategoryTree` model, `admin.category-tree.*` routes, `admin.pages.category-tree` views, `CategoryTreeAdminTest`). Максимальная глубина дерева: **3**. Поля модели: `parent_id`, `sort_no`, `title`, `slug`, `is_active` (+ timestamps).

2. **Redirects** — CRUD таблица для хранения **301** редиректов. Поля: `slug_from`, `slug_to`, `is_active` (+ timestamps).

3. **Users (admin)** — CRUD пользователей админки: перевести auth на **Eloquent**-модель; **полноценный** CRUD; для полей «new password» / «confirm password» — кнопка-иконка «показать пароль»; поле `is_active`.

### Промпты для **реализации**

| Подмодуль     | Файл промпта                                               |
| ------------- | ---------------------------------------------------------- |
| Main menu     | [11-prompt-main-menu.md](./11-prompt-main-menu.md)         |
| Redirects     | [11-prompt-redirections.md](./11-prompt-redirections.md)   |
| Users (admin) | [11-prompt-admin-users.md](./11-prompt-admin-users.md)     |

### Навигация (сайдбар)

Пункт меню в боковой панели — **выпадающий список** (dropdown / collapse): заголовок **Settings**, внутри ссылки:

- **Redirects** — на CRUD редиректов 301
- **Main menu** — на tree CRUD меню
- **Users** — на CRUD пользователей админки

(Реализация — в стиле текущего `sidebar` Bootstrap в `resources/views/admin/partials/sidebar.blade.php`.)

### NOTES

- Каждый промпт включает пункт **создания тестов** и **проверки (запуск тестов)**.
- Перед реализацией читать `.cursor/skills/` (в т.ч. `PATTERNS.md`, `ADMIN_PANEL_PATTERN.md`, `SKILLS.md`).
