# Изменения: request.admin.category-tree

- 2026-04-26 — `UpdateCategoryTreeRequest` (parent_id, title, slug, description, is_active, циклы) → [../../diary/2026/04/2026-04-26-catalog-tree-crud.md#01JCTREE20260426CRUD]
- 2026-04-26 — `authorize()`: проверка `category_tree` как `CategoryTree`; убран `assert` → [../../diary/2026/04/2026-04-26-catalog-tree-crud.md#01JCTREE20260426REVIEW]
- 2026-05-02 — `StoreCategoryTreeRequest` (parent_id, title, slug, description, content, is_active); `UpdateCategoryTreeRequest`: добавлено правило `content` → [../../diary/2026/05/2026-05-02-jodit-category-tree.md#01JCAT20260502JODIT]
- 2026-05-03 — `SaveCategoryTreeOrderRequest` теперь расширяет `AbstractSaveTreeOrderRequest`; содержит только `modelClass()` и `invalidIdsMessage()` → [../../diary/2026/05/2026-05-03-tree-refactoring-aeb.md#01JTREE20260503AEB]
