# Configuration → Main menu (tree CRUD)

## Цель

Добавить подраздел **Main menu** в группе **Configuration** админ-панели: древовидный CRUD с максимальной глубиной **3** уровня, по паттернам существующего модуля **Category tree**.

## Ориентиры в проекте (изучить до кода)

- Модель `App\Models\CategoryTree`, `App\Http\Controllers\Admin\CategoryTreeController`, маршруты `admin.category-tree.*` в `routes/web.php`, представления `resources/views/admin/pages/category-tree/`.
- Тесты: `tests/Feature/Admin/CategoryTreeAdminTest.php` — структура и покрытие взять за образец для нового модуля.
- Сайдбар: `resources/views/admin/partials/sidebar.blade.php`; активный пункт — `App\View\Composers\AdminLayoutComposer` (`activeSidebar` + `request()->routeIs('admin.<модуль>.*')`).
- Навыки: `.cursor/skills/PATTERNS.md`, `ADMIN_PANEL_PATTERN.md` (tree, reorder, валидация `parent_id`).

## Модель (имя согласовать с доменом, например `MenuNode` / `MainMenuItem`)

```php
protected $fillable = [
    'parent_id',
    'sort_no',
    'title',
    'slug',
    'is_active',
];
```

## Требования к поведению

- Создание/редактирование/удаление узлов, смена порядка (как в Category tree — save-order и т.д., если применимо).
- **Запрет** вложенности глубже 3 уровней: валидация в Form Request и/или сервисе; при нарушении — понятные ошибки.
- Маршруты, контроллер, Form Request, при необходимости Service — в стиле существующих admin-модулей проекта.
- Представления: Blade, стилизация согласно текущим админ-формам/дереву.

## Тесты и проверка (обязательно)

- Добавить Feature-тесты: доступ под admin guard, индекс, CRUD, reorder (если есть), **валидация глубины > 3**, граничные случаи (корень, 2-й, 3-й уровень — OK; 4-й — отказ).
- Запустить тесты модуля (`php artisan test` / фильтр по имени теста или файла — по соглашению проекта; в Docker — `./vendor/bin/sail artisan test`).
- Убедиться, что существующие тесты не сломлены.

## Ограничения

- Не делать рефакторинг Category tree без необходимости; смена архитектуры (namespaces, слои) не из задачи.
