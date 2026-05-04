# Изменения: routes.admin-web

- 2026-04-28 — Создан `routes/admin-web.php`: все admin web-роуты (auth, dashboard, UI-демо, static-pages, category-tree, main-menu, seo-redirects, administrators) с группировкой по контексту → [../../diary/2026/04/2026-04-28-routes-refactoring.md#01JR20260428ROUTEREFACTOR]
- 2026-05-02 — Добавлен `POST /admin/category-tree` (store) → [../../diary/2026/05/2026-05-02-jodit-category-tree.md#01JCAT20260502JODIT]
- 2026-05-02 — Добавлены `GET /admin/category-tree/create`, `GET /admin/category-tree/{id}/edit`, `GET /admin/main-menu/create`, `GET /admin/main-menu/{id}/edit` → [../../diary/2026/05/2026-05-02-jodit-category-tree.md#01JCAT20260502PAGES]
- 2026-05-04 — Удалены `GET` layouts/static, layouts/sidenav-light, blank, errors/401|404|500 (демо) → [../../diary/2026/05/2026-05-04-development.md#01JRMVSBDEMO20260504]
- 2026-05-04 — Tables: `Route::resource(...)->only(['index'])` (`admin.tables.index`), удалён `/forms` → [../../diary/2026/05/2026-05-04-development.md#01JTBLFORMS20260504]
