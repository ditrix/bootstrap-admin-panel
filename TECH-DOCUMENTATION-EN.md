# Technical Documentation: bootstrap-admin-panel

This document describes the **current state** of the repository: a reusable admin-panel starter kit on **Laravel 10** with a **Bootstrap 5** UI based on the **Start Bootstrap SB Admin** theme.

Related quick-start guides: [README.md](README.md) (EN), [README_UA.md](README_UA.md) (UA).

---

## 1. Project Purpose

- Laravel web application with a dedicated admin area under the `/admin` URL prefix.
- Designed as a **bootstrap skeleton** for product admin panels (content, catalogs, menus, SEO, users).
- Includes:
  - dashboard and demo layout pages (charts, tables, forms),
  - CRUD for static pages,
  - **Category Tree (catalog)** with recursive hierarchy and **drag-and-drop** reordering (SortableJS),
  - **Main Menu** tree with unlimited nesting and the same DnD UX,
  - banners with image attachments and soft deletes,
  - SEO 301 redirects (public GET handled by global middleware),
  - employees listing as a **server-side Bootstrap Table** reference,
  - administrators CRUD with **Spatie roles/permissions** (`admin` / `manager`).

---

## 2. Technology Stack

### Backend

| Component | Version / notes |
|---|---|
| PHP | `^8.1` (Sail image: 8.2) |
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

| Component | Purpose |
|---|---|
| Vite 5 | Asset bundling |
| Bootstrap 5.3 | UI framework |
| Sass | Theming / SCSS |
| Axios | HTTP client |
| jQuery + bootstrap-table | Server-side admin lists |
| SortableJS | Nested drag-and-drop trees |
| Jodit | WYSIWYG content editing |
| Dripicons / Font Awesome | Icons |

### UI Theme

- [Start Bootstrap — SB Admin](https://github.com/startbootstrap/startbootstrap-sb-admin)

---

## 3. Project Structure

```text
app/
  Authorization/          # AdminPermission, AdminRole constants
  Http/
    Controllers/Admin/    # Web + Api (thin controllers)
    Requests/Admin/       # FormRequest per action
    Resources/Admin/      # Bootstrap Table / JSON rows
  Models/
  Services/Admin/         # Business logic (trees, listings, uploads)
  Helpers/                # BootstrapTableHelper, AdminHelper, …
resources/
  views/admin/            # Blade layouts, pages, partials
  themes/admin/assets/    # JS / SCSS for admin
routes/
  web.php
  admin-web.php
  admin-api.php
  api.php
database/migrations/
docs/                     # Module-level docs (tables, roles)
tests/Feature|Unit/
```

---

## 4. Routing

### Public Routes

| Method | Path | Notes |
|---|---|---|
| GET | `/` | Welcome |
| GET | `/{slug}` (via middleware) | Active SEO 301 from `seo_redirects` |

### Admin Routes

All admin routes use:

- prefix: `/admin`
- name: `admin.*`
- auth: `auth:admin` (+ permission checks where required)

Main modules: authentication, dashboard, static pages, category tree, main menu, banners, employees/tables, SEO redirects, administrators, permissions, log viewer.

Tree reorder endpoints accept nested JSON (`save-order`) for Category Tree and Main Menu.

---

## 5. Authentication & Authorization

### Guards

| Guard | Model |
|---|---|
| `web` | `User` |
| `admin` | `Administrator` |

### Features

- Separate admin login / password reset
- Middleware `auth:admin`
- **Spatie Permission** on guard `admin`
- Roles: `admin` (full), `manager` (content subset)
- Blade `@can` + middleware for module access

Details: [docs/admin-roles-and-permissions.md](docs/admin-roles-and-permissions.md).

---

## 6. Database Models

| Model / table | Role |
|---|---|
| `administrators` | Admin users (`role_id`, `is_active`, Spatie sync) |
| `category_trees` | Catalog tree: `parent_id`, `sort_no`, slug/content, `is_active` |
| `main_menu_items` | Menu tree: nested `parent_id` / `sort_no` |
| `static_pages` | Flat content pages (Bootstrap Table list) |
| `banners` | Promo banners + soft deletes + image path |
| `seo_redirects` | 301 `slug_from` → `slug_to` |
| `employees` | Demo dataset for server-side tables |
| Spatie `roles` / `permissions` | RBAC for admin guard |

Tree convention: `parent_id = 0` = root; ordering field = `sort_no`.

---

## 7. Architecture and Patterns

### Request flow

```text
Route → Middleware → FormRequest → Controller → Service → Model → Blade / JSON Resource
```

### Controllers

Thin orchestration only; validation in FormRequests; mutations and queries in services.

### Tree pattern (key UX differentiator)

- Shared base: `App\Services\Admin\AbstractTreeService`
- Subclasses: `CategoryTreeService`, `MainMenuItemService` (`modelClass()`, depth limits, domain helpers)
- Frontend: nested SortableJS lists → bulk `save-order` API
- Delete reparents children; depth warnings logged when tree grows too deep

### Table pattern

- Blade widget `bootstrap-table-widget`
- Invokable `*TableDataController` + `*ListingService::paginateForBootstrapTable()`
- Response shape: `{ "total", "rows" }`
- Shared helpers: `BootstrapTableHelper`

Details: [docs/admin-bootstrap-table.md](docs/admin-bootstrap-table.md).

### API Resources

Transform listing rows (dates typically `d.m.Y` where already conventional).

---

## 8. Blade Views and Frontend

### Layout

- `resources/views/admin/layouts/sb-admin.blade.php` — sidebar, top nav, flash, Vite

### Modules (pages)

Static Pages, Category Tree, Main Menu, Banners, SEO Redirects, Administrators, Employees/Tables, Permissions, Dashboard demos.

### Editors / JS

Jodit, SortableJS (trees), Bootstrap Table (lists), Vite-built admin scripts.

---

## 9. Testing

| Item | Value |
|---|---|
| Framework | PHPUnit 10 |
| Style | Feature-first (+ Unit where present) |
| DB in tests | SQLite in-memory (`RefreshDatabase`) |
| Run | `./vendor/bin/sail artisan test` |

Coverage includes admin CRUD, tree reorder (`save-order`), auth, and listing services.

---

## 10. Localization

- Application / admin UI strings under `lang/` (e.g. `lang/en/admin.php`)
- `__()` / `@lang` in Blade

---

## 11. Deployment / Local Environment

Typical local setup (Sail):

1. `composer install` → copy `.env` → set `WWWUSER` / `WWWGROUP`
2. `./vendor/bin/sail up -d`
3. `sail artisan key:generate` && `storage:link`
4. `sail artisan migrate --seed`
5. `npm install` && `npm run dev` (or `build`)

Demo logins after seed: `admin@mail.com` / `password`, `manager@mail.com` / `password`.

---

## 12. Related Documentation

| Path | Description |
|---|---|
| `README.md` / `README_UA.md` | Install & daily workflow |
| `docs/admin-bootstrap-table.md` | Server-side Bootstrap Table |
| `docs/admin-roles-and-permissions.md` | Spatie RBAC |
| `.cursor/skills/STACK.md` | Stack conventions |
| `.cursor/skills/ARCHITECTURE.md` | Layers & request flow |
| `.cursor/skills/PATTERNS.md` | Table / Tree CRUD |
| `.cursor/skills/ADMIN_PANEL_PATTERN.md` | Admin UI patterns |

---

## 13. Document Version

| Field | Value |
|---|---|
| Repository | `bootstrap-admin-panel` |
| Framework | Laravel 10 / PHP `^8.1` |
| UI | Bootstrap 5 + SB Admin |
| Doc date | 2026-07-26 |

Highlights of the current codebase: reusable `AbstractTreeService`, SortableJS DnD for **catalog** and **menu**, Bootstrap Table listings, Spatie admin RBAC, Sail-based single-pass `migrate --seed`.
