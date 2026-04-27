# Admin Panel Pattern

## Purpose
This document defines the preferred architecture for admin-panel modules based on the reference Laravel project.

## Admin Panel Architecture
Admin functionality is organized as modular CRUD sections with matching routes, controllers, requests, views, and API endpoints.

### Example Module
`Admin/Product`

### Common Components
- `ProductController`
- `ProductService`
- `StoreProductRequest`
- `UpdateProductRequest`
- `Product` model
- Blade views

### Responsibilities
- controller: orchestrates request handling and responses
- service: contains reusable business rules and non-trivial workflows
- requests: validate create and update payloads
- model: owns persistence, relations, scopes, and accessors
- views: render list pages, forms, and reusable partials

## Standard Module Structure
The reference project commonly organizes admin views like this:
- `pages/<module>/index.blade.php`
- `pages/<module>/create.blade.php`
- `pages/<module>/show.blade.php`
- `pages/<module>/form/*.blade.php`
- `pages/<module>/data-table/table.blade.php`

This structure should be reused when adding new admin modules.

Для **плоских списков (табличный CRUD)** в этом репозитории страница обычно: `pages/<module>/index.blade.php` или `view.blade.php` с подключением общего виджета (см. ниже), а не отдельный `data-table/table.blade.php` из условного reference — ориентируйся на существующие модули (**Static pages**, **Administrators**, **301 Redirects**).

## Admin Tables (стандарт проекта: bootstrap-table, server-side)

**Эталон:** серверный список с пагинацией, поиском и сортировкой через плагин **bootstrap-table** (jQuery) и общий Blade-виджет — не DataTables и не «ручная» HTML-таблица с `links()`, если задача — именно табличный каталог в админке.

### Обязательные возможности для новых модулей
- пагинация на сервере (`limit` / `offset`)
- глобальный поиск (`search`)
- сортировка по белому списку полей (`sort`, `order`)
- при необходимости: фильтры и массовые действия (по домену)

### Стек UI
- Blade + разметка Bootstrap (тема SB Admin)
- CDN: **jQuery** + **bootstrap-table** (JS); стили — SCSS проекта (`_bootstrap-table.scss`), без CDN CSS плагина
- Vite: `resources/themes/admin/assets/js/admin-bootstrap-table.js` (`adminBootstrapTableDelete`, `adminBootstrapTableBooleanIcon`, i18n через `#admin-bootstrap-table-i18n` внутри виджета)
- Vue — только если таблице нужна нетипичная интеракция; для стандартного CRUD не подменяй этим паттерн

### Blade
- Виджет: `resources/views/admin/partials/bootstrap-table-widget.blade.php`
- Подключение: `@include('admin.partials.bootstrap-table-widget', ['tableId' => ..., 'dataUrl' => route('admin.api…'), 'columns' => [...], 'actionsFormatter' => '…'])`
- Колонки: массив с `field`, `title`, опционально `sortable`, `formatter`, `escape`
- Действия (редактирование / удаление): глобальная JS-функция в `@push('scripts')` на странице (пример: `resources/views/admin/pages/static-pages/view.blade.php`)

### Бэкенд (паттерн)
- **Invokable controller** в `App\Http\Controllers\Admin\Api\*TableDataController`: принимает `Request`, отдаёт `JsonResponse`
- **Listing service** с методом `paginateForBootstrapTable(Request $request): array{total: int, rows: Collection}` — единая логика лимита, offset, поиска, `SORTABLE`
- **API Resource** (`App\Http\Resources\Admin\Admin\*`) для строк; даты в списке в формате **`d.m.Y`**, если так уже принято в модуле
- Формат ответа для клиента: `{"total": <int>, "rows": [<object>, …]}`
- Маршрут: `GET` под префиксом `admin`, имя вида `admin.api.<module>.table`, middleware **`auth:admin`**

Подробности и таблица маршрутов: **`docs/admin-bootstrap-table.md`**.

### Исключения
- **Деревья** (main menu, category tree): SortableJS и своё сохранение порядка, не этот виджет
- Демо `/admin/tables` и прочие страницы без доменной сущности — по существующему коду

## Admin Forms
### Supported Controls
- text inputs
- select fields
- checkboxes
- file uploads
- rich text editors

### Validation
All admin form validation must use `FormRequest` classes.

### Form Rules
- keep forms Blade-first
- extract repeated form blocks into partials
- show clear validation feedback
- support multilingual tabs when the entity uses translated fields

## Tree Structures
Hierarchical data in the admin panel commonly includes:
- categories
- documents
- menus

### Requirements
- drag-and-drop sorting
- parent-child relationships
- explicit `position` or equivalent ordering field

### Frontend Libraries
- SortableJS
- VueDraggable

### Backend Rules
- expose reorder endpoints
- validate parent assignment
- keep recursive transformation logic in services or models, not controllers

## File Uploads
Uploads should use Laravel storage through shared admin components or services.

Recommended behavior:
- validate file input in requests
- store files through Laravel abstractions
- reuse upload partials/components
- support ordering for multiple uploads when the module needs galleries

## Rich Text Editors
Possible editors include:
- TinyMCE
- CKEditor

Content should be stored as HTML when the module is meant to render rich formatted text.

## Permissions And Authorization
Admin access should be enforced consistently through:
- policies
- gates
- middleware-based permission checks

AI should follow the authorization strategy already used by the project instead of mixing multiple styles in one module.

## Admin UX Guidelines
- keep forms simple and task-focused
- avoid heavy frontend logic unless the UI clearly needs it
- use pagination for large datasets
- show clear validation errors and flash messages
- favor predictable CRUD screens over custom one-off flows

## AI Development Rules
When generating admin functionality:
- always follow the Table CRUD pattern
- **for flat list screens, use the bootstrap-table widget + server JSON endpoint** (`*TableDataController`, `*ListingService::paginateForBootstrapTable()`, optional `*Resource`) as described in **Admin Tables** above; do not introduce DataTables for new modules unless explicitly requested
- use `FormRequest` validation
- keep controllers thin
- use services for business logic when it is more than a simple model update
- use Blade templates as the default rendering layer
- use Bootstrap-compatible markup in a Bootstrap-based admin
- add pagination and search to table-based modules
- keep Vue limited to interactive islands, not full admin SPA rewrites
- avoid using Vue for simple forms that can be handled by Blade, and use it only for Tree CRUD and complex dynamic UI
