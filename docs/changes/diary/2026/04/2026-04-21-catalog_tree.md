## 13:44 (Europe/Kyiv) feat[model.category-tree,db.category-trees,controller.admin.category-tree,service.admin.category-tree,routes.web.admin,views.admin,styles.admin.theme] — Дерево каталога (Tree CRUD) с Drag & Drop

**Entry ID:** 01JRZ2K4TY0001CATEGORYTREE
**Agent:** claude-sonnet-4-6
**Дата:** 2026-04-21
**Ветка:** catalog_tree

### Файлы
- `database/migrations/2026_04_21_000001_create_category_trees_table.php` (+22 −0)
- `app/Models/CategoryTree.php` (+54 −0)
- `database/factories/CategoryTreeFactory.php` (+44 −0)
- `database/seeders/CategoryTreeSeeder.php` (+60 −0)
- `app/Services/Admin/CategoryTreeService.php` (+44 −0)
- `app/Http/Requests/Admin/CategoryTree/SaveCategoryTreeOrderRequest.php` (+60 −0)
- `app/Http/Controllers/Admin/CategoryTreeController.php` (+33 −0)
- `routes/web.php` (+3 −0)
- `resources/views/admin/pages/category-tree/index.blade.php` (+80 −0)
- `resources/views/admin/pages/category-tree/partials/tree-node.blade.php` (+17 −0)
- `resources/themes/admin/assets/css/blocks/_category-tree.scss` (+66 −0)
- `resources/themes/admin/assets/css/app.scss` (+1 −0)
- `resources/views/admin/partials/sidebar.blade.php` (+5 −0)
- `app/View/Composers/AdminLayoutComposer.php` (+1 −0)
- `tests/Feature/Admin/CategoryTreeAdminTest.php` (+100 −0)

### Что сделано
Реализован модуль дерева каталога: миграция `category_trees` (поля `parent_id`, `sort_no`, `title`, `is_active`, timestamps), модель `CategoryTree` со связями `parent()`/`children()` и scope `ordered()`, фабрика и сидер с 20 записями глубиной 1–4 уровня. Создан `CategoryTreeService` с методами `buildGroupedTree()` и рекурсивным `saveOrder()`. Контроллер `CategoryTreeController` (index + saveOrder), FormRequest с валидацией существования id в БД, два маршрута в `auth:admin`-группе. Blade-шаблоны с рекурсивным partial, SortableJS (CDN 1.15.3) с `handle: '.ct-handle'` и AJAX POST для сохранения. SCSS-блок `_category-tree` с импортом в `app.scss`. Пункт «Category Tree» добавлен в сайдбар, подсветка через `AdminLayoutComposer`.

### Почему
Новый функционал по задаче 9 — Tree CRUD для управления иерархическим каталогом с drag-and-drop сортировкой.

### Влияние
- **БД:** новая таблица `category_trees` (id, parent_id, sort_no, title, is_active, timestamps)
- **API:** новый POST эндпоинт `/admin/category-tree/save-order` (JSON, auth:admin)
- **Производительность:** одиночный SELECT при загрузке страницы; N UPDATE-запросов при сохранении порядка (по числу узлов)

### Проверено
- Тесты: новые (6 passed — index рендер, редирект гостя, сохранение структуры, валидация несуществующих id, проверка БД после сохранения)
- Линтер: ok

### Follow-up
- [ ] Добавить CRUD создания/редактирования/удаления узлов
- [x] Запустить `npm run build` для пересборки ассетов

---

## 14:16 (Europe/Kyiv) fix[views.admin,styles.admin.theme] — Исправление DnD: смена родителя (вправо) и инициализация JS без jQuery

**Entry ID:** 01JRZ4QCAT002DNDFIX
**Agent:** claude-sonnet-4-6
**Дата:** 2026-04-21
**Ветка:** catalog_tree

### Файлы
- `resources/views/admin/pages/category-tree/index.blade.php` (~40 строк перезаписано)
- `resources/views/admin/pages/category-tree/partials/tree-node.blade.php` (+2 −4)
- `resources/themes/admin/assets/css/blocks/_category-tree.scss` (+10 −4)

### Что сделано
Устранены два дефекта: 1) скрипт инициализировался через `(function($){...}(jQuery))` и `$.ajax`, но jQuery подключён через Vite как `type="module"` (defer) и недоступен в момент выполнения inline-скриптов — переписано на plain JavaScript + `fetch` + `document.addEventListener('DOMContentLoaded', ...)`, как принято в остальном проекте. 2) drag вправо (смена parent_id) не работал: вложенный `<ol>` для узлов без детей не рендерился, а когда добавили пустой — был 8px и SortableJS не попадал в него мышью. Решение: вложенный `<ol class="ct-list--nested">` теперь рендерится для каждого узла всегда; добавлен `onStart`/`onEnd` для CSS-класса `ct-is-dragging` на корневом элементе; в режиме drag пустые вложенные списки раскрываются до 32px с пунктирной границей; `emptyInsertThreshold` увеличен до 20.

### Почему
Вертикальный drag работал (сортировка внутри уровня), горизонтальный — нет (смена уровня вложенности). Причина: jQuery unavailable + отсутствие видимой drop-зоны для узлов без детей.

### Влияние
- **БД:** N/A
- **API:** N/A
- **Производительность:** N/A

### Проверено
- Тесты: пройдены (6 passed, без изменений)
- Линтер: ok
- DnD влево и вправо: работает

### Follow-up
- [ ] Добавить CRUD создания/редактирования/удаления узлов
