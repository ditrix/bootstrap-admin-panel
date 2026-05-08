# Technical Documentation: bootstrap-admin-panel

This document describes the **current state** of the repository: an admin panel built on **Laravel 10** with a **Bootstrap 5** UI and the **Start Bootstrap SB Admin** theme.

---

## 1. Project Purpose

- Laravel web application with a dedicated admin area under the `/admin` URL prefix.
- Includes:
  - demo layout pages (dashboard, charts, tables, forms, layouts),
  - CRUD for static pages with tree structure via `parent_id`,
  - WYSIWYG editing using Jodit,
  - Category Tree with drag-and-drop sorting,
  - Settings section in the sidebar:
    - Main Menu,
    - 301 Redirects,
    - Users,
  - `employees` table as a server-side pagination example for Bootstrap Table.

Public GET 301 redirects from `seo_redirects` are processed through global middleware.

---

## 2. Technology Stack

### Backend

| Component | Version |
|---|---|
| PHP | `^8.1` |
| Laravel | `^10.10` |
| Sanctum | `^3.3` |
| Guzzle | `^7.2` |

### Frontend

| Component | Purpose |
|---|---|
| Vite | Asset bundling |
| Bootstrap 5 | UI |
| Sass | Styling |
| Axios | HTTP client |
| Dripicons | Icons |

### UI Theme

Based on:
- Start Bootstrap — SB Admin

---

## 3. Project Structure

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

## 4. Routing

### Public Routes

| Method | Path |
|---|---|
| GET | `/` |

### Admin Routes

All admin routes use:
- prefix: `/admin`
- name: `admin.*`

Includes:
- authentication,
- dashboard,
- static pages,
- category tree,
- main menu,
- redirects,
- administrators.

---

## 5. Authentication

### Guards

| Guard | Model |
|---|---|
| `web` | `User` |
| `admin` | `Administrator` |

### Features

- Password reset support
- Separate admin authentication
- Middleware protection via `auth:admin`

---

## 6. Database Models

### employees

Demo table for Bootstrap Table examples.

### static_pages

Contains:
- hierarchy via `parent_id`,
- `slug`,
- `content`,
- activation status.

### category_trees

Tree structure with:
- drag-and-drop sorting,
- recursive hierarchy,
- Jodit content editor.

### main_menu_items

Dynamic menu tree with unlimited nesting depth.

### seo_redirects

Stores 301 redirects:
- `slug_from`
- `slug_to`
- `is_active`

### administrators

Admin users with:
- password hashing,
- active status,
- self-delete protection.

---

## 7. Architecture and Patterns

### Controllers

Thin controllers:
- validation via FormRequest,
- business logic moved to services.

### Services

Includes:
- tree services,
- listing services,
- dashboard service.

### API Resources

Used for Bootstrap Table JSON formatting.

### Helpers

Includes:
- pagination helpers,
- salary formatting,
- theme asset helpers.

---

## 8. Blade Views and Frontend

### Layouts

Main layout:
- `sb-admin.blade.php`

Includes:
- sidebar,
- top navigation,
- flash notifications,
- Vite assets.

### Modules

Modules:
- Static Pages
- Category Tree
- Main Menu
- Redirects
- Administrators

### Editors and JS

Uses:
- Jodit Editor
- SortableJS
- Bootstrap
- Vite

---

## 9. Testing

Framework:
- PHPUnit 10

Includes:
- Feature tests
- Unit tests

Database:
- SQLite in-memory

---

## 10. Localization

Translations stored in:
- `lang/en/admin.php`

---

## 11. Deployment

Typical setup:
1. Copy `.env`
2. Configure database
3. Run migrations
4. Install npm dependencies
5. Build assets

---

## 12. Related Documentation

| Path | Description |
|---|---|
| `.cursor/skills/STACK.md` | Stack description |
| `.cursor/skills/ARCHITECTURE.md` | Architecture |
| `.cursor/skills/PATTERNS.md` | CRUD and patterns |
| `README.md` | Installation |
| `docs/admin-bootstrap-table.md` | Bootstrap Table docs |

---

## 13. Document Version

Repository:
- `bootstrap-admin-panel`
- Laravel 10
- PHP `^8.1`

Latest updates include:
- Settings module
- Tree service refactoring
- Jodit integration
- Separate admin route files
