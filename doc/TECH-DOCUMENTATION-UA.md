# Технічна документація: bootstrap-admin-panel

Документ описує **поточний стан** репозиторію: адмін-панель на **Laravel 10** з UI на **Bootstrap 5** та темою **Start Bootstrap SB Admin**.

---

## 1. Призначення проєкту

- Веб-застосунок на Laravel з окремою адміністративною зоною за префіксом `/admin`.
- Включає:
  - demo-сторінки layout,
  - CRUD статичних сторінок з деревоподібною структурою через `parent_id`,
  - WYSIWYG-редактор Jodit,
  - дерево категорій з drag-and-drop,
  - секцію Settings у sidebar:
    - Main Menu,
    - 301 Redirects,
    - Users,
  - таблицю `employees` як приклад серверної пагінації для Bootstrap Table.

Публічні GET 301-редиректи з `seo_redirects` обробляються глобальним middleware.

---

## 2. Технологічний стек

### Backend

| Компонент | Версія |
|---|---|
| PHP | `^8.1` |
| Laravel | `^10.10` |
| Sanctum | `^3.3` |
| Guzzle | `^7.2` |

### Frontend

| Компонент | Призначення |
|---|---|
| Vite | Збірка assets |
| Bootstrap 5 | UI |
| Sass | Стилізація |
| Axios | HTTP-клієнт |
| Dripicons | Іконки |

### UI Theme

Основа:
- Start Bootstrap — SB Admin

---

## 3. Структура проєкту

```text
app/
  Http/
  Models/
  Services/
  Helpers/
resources/
  views/admin/
routes/
  web.php
  admin-web.php
  admin-api.php
tests/
```

---

## 4. Маршрутизація

### Публічні маршрути

| Метод | Шлях |
|---|---|
| GET | `/` |

### Admin-маршрути

Усі admin-маршрути використовують:
- префікс: `/admin`
- name: `admin.*`

Включають:
- авторизацію,
- dashboard,
- static pages,
- category tree,
- main menu,
- redirects,
- administrators.

---

## 5. Автентифікація

### Guards

| Guard | Модель |
|---|---|
| `web` | `User` |
| `admin` | `Administrator` |

### Можливості

- Підтримка скидання пароля
- Окрема admin-автентифікація
- Захист через `auth:admin`

---

## 6. Моделі бази даних

### employees

Demo-таблиця для Bootstrap Table.

### static_pages

Містить:
- ієрархію через `parent_id`,
- `slug`,
- `content`,
- статус активності.

### category_trees

Деревоподібна структура з:
- drag-and-drop сортуванням,
- рекурсивною ієрархією,
- редактором Jodit.

### main_menu_items

Динамічне дерево меню з необмеженою глибиною вкладеності.

### seo_redirects

Зберігає 301-редиректи:
- `slug_from`
- `slug_to`
- `is_active`

### administrators

Адміністратори з:
- хешуванням паролів,
- статусом активності,
- захистом від видалення самого себе.

---

## 7. Архітектура та патерни

### Controllers

Thin controllers:
- валідація через FormRequest,
- бізнес-логіка винесена в services.

### Services

Містять:
- tree services,
- listing services,
- dashboard service.

### API Resources

Використовуються для JSON Bootstrap Table.

### Helpers

Містять:
- helpers пагінації,
- форматування salary,
- helpers для theme assets.

---

## 8. Blade Views та Frontend

### Layouts

Основний layout:
- `sb-admin.blade.php`

Включає:
- sidebar,
- top navigation,
- flash notifications,
- Vite assets.

### Модулі

Модулі:
- Static Pages
- Category Tree
- Main Menu
- Redirects
- Administrators

### Editors та JS

Використовуються:
- Jodit Editor
- SortableJS
- Bootstrap
- Vite

---

## 9. Тестування

Фреймворк:
- PHPUnit 10

Включає:
- Feature tests
- Unit tests

База даних:
- SQLite in-memory

---

## 10. Локалізація

Переклади знаходяться у:
- `lang/en/admin.php`

---

## 11. Deployment

Типовий setup:
1. Скопіювати `.env`
2. Налаштувати базу даних
3. Запустити migrations
4. Встановити npm dependencies
5. Зібрати assets

---

## 12. Пов’язана документація

| Шлях | Опис |
|---|---|
| `.cursor/skills/STACK.md` | Опис стеку |
| `.cursor/skills/ARCHITECTURE.md` | Архітектура |
| `.cursor/skills/PATTERNS.md` | CRUD та патерни |
| `README.md` | Встановлення |
| `docs/admin-bootstrap-table.md` | Документація Bootstrap Table |

---

## 13. Версія документа

Репозиторій:
- `bootstrap-admin-panel`
- Laravel 10
- PHP `^8.1`

Останні оновлення включають:
- Settings module
- рефакторинг tree services
- інтеграцію Jodit
- окремі admin route files
