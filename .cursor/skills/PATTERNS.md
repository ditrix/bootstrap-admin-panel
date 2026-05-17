# Reusable Development Patterns

## Pattern 1 - Table CRUD
### Description
Table CRUD is the standard pattern for flat admin entities such as pages, blogs, products, users, and other back-office modules.

### Features
- pagination
- sorting
- search
- filters
- bulk actions when the module needs batch operations

### Typical Architecture
- Controller
- FormRequest
- Service when logic is reusable or non-trivial
- Model
- Blade view
- **В bootstrap-admin-panel:** отдельный **GET** endpoint и invokable `*TableDataController` + `*ListingService::paginateForBootstrapTable()` + при необходимости `*Resource` (см. [ADMIN_PANEL_PATTERN.md](ADMIN_PANEL_PATTERN.md)).

### Frontend
- Blade tables for structure
- **bootstrap-admin-panel:** bootstrap-table (server-side) через `bootstrap-table-widget` и jQuery (см. ADMIN_PANEL_PATTERN)
- optional Vue components for specific interactive fragments

### Typical Flow
1. `index()` returns the list page (часто с `tableId` и `dataUrl` для виджета).
2. Отдельный endpoint возвращает JSON `{ "total", "rows" }` для bootstrap-table.
3. `create()` or `show()` returns the form page.
4. `store()` and `update()` use `FormRequest::validated()`.
5. `destroy()` removes the entity and redirects back with a flash message.

### Guidance
- Keep listing pages separate from mutation logic.
- Prefer a dedicated table endpoint for heavy list views.
- Put formatting and query-specific list logic outside Blade when it becomes complex.

## Pattern 2 - Tree CRUD
### Description
Tree CRUD is used for hierarchical entities such as categories, documents, and menus.

### Recommended Database Schema
- `id`
- `parent_id`
- `position`
- `title`
- `slug`
- `type` when the hierarchy supports multiple node kinds
- `published` or another explicit visibility flag

### Model Requirements
- `parent()` relationship
- `children()` or project-consistent equivalent relationship
- recursive loading support
- deterministic ordering by `position` or another explicit sort field
- use global scope for default ordering by `position`

### Backend Requirements
- validation rules for parent assignment and ordering
- efficient nested queries with eager loading
- bulk reorder endpoint for drag-and-drop updates
- service methods for recursive transformation when needed

### Frontend Requirements
- recursive rendering
- drag-and-drop support for reordering
- libraries such as SortableJS or VueDraggable when interaction is needed

### Guidance
- Keep hierarchy logic out of controllers.
- Avoid loading entire trees repeatedly without caching or constrained eager loading.
- Store ordering explicitly instead of relying on creation order.

## Pattern 3 - Admin Table Pattern
### Description
Admin table pages are the canonical listing pattern inside the back office.

### Required Capabilities
- search
- pagination
- sorting
- filters
- bulk actions where relevant

### Integration With Table CRUD
This pattern is the listing half of Table CRUD:
- the page is rendered by Blade
- the data is fetched through a dedicated route
- the controller stays thin
- search and sorting remain consistent across modules

## Pattern 4 - File Upload Pattern
### Description
Files are uploaded through Laravel storage and reusable admin UI components.

### Storage
- files are stored under `storage/app/public`
- public URLs are exposed through `/storage`

### Recommended Flow
1. Validate file input in a `FormRequest`.
2. Delegate storage to a service or a shared upload subsystem.
3. Store the returned path or attachment relation on the model.
4. Expose the file through a predictable public URL or resource field.

### Validation Concerns
- mime type
- file size
- image dimensions when required
- single vs multiple upload behavior

### Guidance
- Keep upload handling reusable across modules.
- Do not hardcode storage logic in controllers.
- Prefer Laravel `Storage` abstractions over manual file system work.


## Pattern 5 - Query Filtering (Scopes)
### Description
Query Scopes are the standard way to encapsulate reusable SQL logic and filters within the Model layer.

### Features
- **Readability**: Controllers use `$model->active()` instead of `$model->where('status', 1)`.
- **Reusability**: One scope can be used in Web, Admin, and API layers.
- **Maintainability**: If the logic for "active" changes, it’s updated only in the Model.

### Implementation
- **Local Scopes**: Prefixed with `scope`, e.g., `scopePublished(Builder $query)`.
- **Dynamic Scopes**: Scopes that accept parameters, e.g., `scopeOfType(Builder $query, string $type)`.

### Guidance
- Never write complex `where` clauses directly in Controllers.
- If a filter is used more than once, it MUST be a Scope.