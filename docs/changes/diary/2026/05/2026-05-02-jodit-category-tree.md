# 2026-05-02 — Jodit Editor + CategoryTree Create-модал + поле content

## 20:04 (Europe/Kyiv) feat[model.category-tree,db.category-trees.migration,controller.admin.category-tree,service.admin.category-tree,request.admin.category-tree,routes.admin-web,views.admin] — Jodit Editor, поле content для CategoryTree, Create-модал
**Entry ID:** 01JCAT20260502JODIT
**Agent:** Sonnet 4.6
**Дата:** 2026-05-02
**Ветка:** development

### Файлы
- `database/migrations/2026_05_02_170151_add_content_to_category_trees_table.php` (+12 −0)
- `app/Models/CategoryTree.php` (+1 −0)
- `app/Services/Admin/CategoryTreeService.php` (+55 −3)
- `app/Http/Requests/Admin/CategoryTree/StoreCategoryTreeRequest.php` (+65 −0)
- `app/Http/Requests/Admin/CategoryTree/UpdateCategoryTreeRequest.php` (+1 −0)
- `app/Http/Controllers/Admin/CategoryTreeController.php` (+20 −2)
- `routes/admin-web.php` (+1 −0)
- `resources/views/admin/pages/category-tree/index.blade.php` (+180 −80)
- `resources/views/admin/pages/static-pages/create.blade.php` (+14 −1)
- `resources/views/admin/pages/static-pages/edit.blade.php` (+14 −1)

### Что сделано
Добавлено поле `content` (longtext, nullable) в таблицу `category_trees` с миграцией. В `CategoryTreeService` добавлены методы `createItem` и `buildParentOptionsHtml` по аналогии с `MainMenuItemService`. Создан `StoreCategoryTreeRequest`, добавлен маршрут и метод `store` в контроллере. В `category-tree/index` добавлен Create-модал с полем `content`. В оба модала (Create + Edit) интегрирован Jodit Editor с инициализацией на `shown.bs.modal` и уничтожением на `hidden.bs.modal`. Jodit подключён также в StaticPage create/edit через CDN.

### Почему
Задача требует поля `content` для CategoryTree и WYSIWYG-редактора Jodit во всех формах, работающих с контентом. Предыдущие попытки не справились с открытием Create-модала и внедрением Jodit.

### Влияние
- **БД:** новая колонка `content` в `category_trees`
- **API:** новый POST `/admin/category-tree` (store)
- **Производительность:** N/A

### Проверено
- Тесты: N/A
- Линтер: ok (pint --dirty pass)

### Follow-up
- [ ] N/A

---

## 20:56 (Europe/Kyiv) refactor[controller.admin.category-tree,controller.admin.main-menu,service.admin.category-tree,service.admin.main-menu,routes.admin-web,views.admin] — CategoryTree и MainMenu: page-based create/edit вместо модальных окон
**Entry ID:** 01JCAT20260502PAGES
**Agent:** Sonnet 4.6
**Дата:** 2026-05-02
**Ветка:** mysiwyg-editor

### Файлы
- `app/Http/Controllers/Admin/CategoryTreeController.php` (+20 −35)
- `app/Http/Controllers/Admin/MainMenuItemController.php` (+20 −45)
- `app/Services/Admin/CategoryTreeService.php` (+4 −3)
- `app/Services/Admin/MainMenuItemService.php` (+4 −3)
- `routes/admin-web.php` (+4 −0)
- `resources/views/admin/pages/category-tree/index.blade.php` (−300, упрощён до DnD-only)
- `resources/views/admin/pages/category-tree/create.blade.php` (+70 −0)
- `resources/views/admin/pages/category-tree/edit.blade.php` (+75 −0)
- `resources/views/admin/pages/category-tree/partials/tree-node.blade.php` (+1 −3)
- `resources/views/admin/pages/main-menu/index.blade.php` (−200, упрощён до DnD-only)
- `resources/views/admin/pages/main-menu/create.blade.php` (+55 −0)
- `resources/views/admin/pages/main-menu/edit.blade.php` (+60 −0)
- `resources/views/admin/pages/main-menu/partials/tree-node.blade.php` (+1 −3)

### Что сделано
CategoryTree и MainMenu переведены с модальных окон на отдельные страницы create/edit по аналогии со StaticPage. Добавлены GET-роуты `/create` и `/{id}/edit`, методы `create()` и `edit()` в контроллерах. `buildParentOptionsHtml()` в обоих сервисах получил параметр `$selectedId` для корректного предвыбора родителя. `update()` в обоих контроллерах упрощён до `RedirectResponse`. Кнопки edit в tree-node partials заменены на `<a>` ссылки. Index-страницы обоих деревьев упрощены — содержат только DnD-логику.

### Почему
Модальные окна не открывались из-за гонки `window.bootstrap` (Vite deferred modules). Переход на страницы надёжнее и проще в поддержке.

### Влияние
- **БД:** N/A
- **API:** новые GET-роуты create/edit для обоих деревьев
- **Производительность:** N/A

### Проверено
- Тесты: N/A
- Линтер: ok (pint pass)

### Follow-up
- [ ] N/A
