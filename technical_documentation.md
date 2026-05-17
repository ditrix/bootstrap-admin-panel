# Technical Documentation — Bootstrap Admin Panel

## Overview

Bootstrap Admin Panel is a production-ready Laravel admin panel starter kit. It provides a full CRUD back-office with role-based access control, a tree-structured category module, banner management, static pages, SEO redirects, and an employee directory.

The project was built using **vibecoding** methodologies — structured AI-assisted development with iterative prompting, automated code review cycles, and convention-first architecture.

---

## Technology Stack

### Backend

| Technology | Version |
|-----------|---------|
| PHP | ^8.1 (tested on 8.2) |
| Laravel | ^10.10 |
| MySQL | 8.x (via Docker) |
| Laravel Sanctum | ^3.3 |
| Spatie Laravel Permission | ^6.25 |
| Laravel Sail | ^1.55 |
| arcanedev/log-viewer | ^10.1 |
| laravel/boost | ^1.8 |

### Frontend

| Technology | Version |
|-----------|---------|
| Bootstrap | ^5.3 |
| Vite | ^5.0 |
| Sass | ^1.98 |
| Axios | ^1.6 |
| Vue.js | via CDN (island components) |

**UI template:** [SB Admin](https://github.com/startbootstrap/startbootstrap-sb-admin) by Start Bootstrap.

---

## Architecture

The application follows a layered MVC architecture with strict separation of responsibilities.

```
app/
├── Authorization/          # AdminPermission / AdminRole constants
├── Http/
│   ├── Controllers/Admin/  # Thin controllers — delegate to services
│   ├── Requests/Admin/     # FormRequest validation per action
│   └── Resources/          # API JSON transformers
├── Models/                 # Eloquent models with relationships
├── Services/               # Reusable business logic
└── Helpers/                # Small shared utilities
```

### Request Flow

```
Route → Middleware → FormRequest (validate) → Controller → Service → Model → Response
```

### Key Conventions

- Controllers remain thin; all business logic lives in services and models.
- Every create/update action has its own `FormRequest` class.
- Naming follows Laravel conventions: `snake_case` for database, `StudlyCase` for classes.
- Admin routes are prefixed with `/admin` and protected by the `auth:admin` middleware.
- Tree structures use `parent_id = 0` to denote root nodes and `sort_no` for ordering.

---

## Database Schema

### Migration Execution Order

All 13 migrations run in a single pass (`php artisan migrate`):

| # | Migration | Description |
|---|-----------|-------------|
| 1 | `2014_10_12_000000` | `users` — standard Laravel users table |
| 2 | `2014_10_12_100000` | `password_reset_tokens` |
| 3 | `2019_08_19_000000` | `failed_jobs` |
| 4 | `2019_12_14_000001` | `personal_access_tokens` (Sanctum) |
| 5 | `2026_03_29_152652` | `employees` |
| 6 | `2026_03_29_152800` | `administrator_password_reset_tokens` |
| 7 | `2026_04_03_102446` | `static_pages` |
| 8 | `2026_04_21_000001` | `category_trees` (with `slug`, `description`, `content`) |
| 9 | `2026_04_26_200000` | `main_menu_items` |
| 10 | `2026_04_26_200001` | `seo_redirects` |
| 11 | `2026_05_12_120000` | `banners` (with soft deletes) |
| 12 | `2026_05_15_160653` | Spatie permission tables |
| 13 | `2026_05_15_161000` | `administrators` (with `role_id` FK → `roles`, `is_active`) |

> The `administrators` table is intentionally created **after** the Spatie permission tables so the `role_id` foreign key constraint can be declared inline in the `CREATE TABLE` statement — no ALTER TABLE required.

### Key Table Structures

#### `administrators`
| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint PK | |
| `role_id` | bigint FK | → `roles.id`, nullable, nullOnDelete |
| `name` | varchar | |
| `email` | varchar unique | |
| `password` | varchar | |
| `is_active` | boolean | default true |
| `remember_token` | varchar | |
| `timestamps` | | |

#### `category_trees`
| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint PK | |
| `parent_id` | bigint | 0 = root node |
| `sort_no` | int | indexed |
| `title` | varchar | |
| `slug` | varchar unique nullable | |
| `description` | text nullable | |
| `content` | longtext nullable | |
| `is_active` | boolean | |
| `timestamps` | | |

#### `banners`
| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint PK | |
| `code` | varchar nullable | indexed |
| `title` | varchar | |
| `sort_no` | int | indexed |
| `is_active` | boolean | |
| `image_path` | varchar nullable | |
| `timestamps` | | |
| `deleted_at` | timestamp | soft deletes |

---

## Authorization

Authorization uses **Spatie Laravel Permission** with the `admin` guard.

### Roles

| Role | Description |
|------|-------------|
| `admin` | Full access to all permissions |
| `manager` | Limited access (see below) |

### Permissions

| Permission constant | Name | admin | manager |
|--------------------|------|-------|---------|
| `DASHBOARD_VIEW` | `dashboard.view` | ✓ | ✓ |
| `STATIC_PAGES_MANAGE` | `static_pages.manage` | ✓ | ✓ |
| `CATEGORY_TREE_MANAGE` | `category_tree.manage` | ✓ | ✓ |
| `BANNERS_MANAGE` | `banners.manage` | ✓ | ✓ |
| `EMPLOYEES_MANAGE` | `employees.manage` | ✓ | ✓ |
| `SEO_REDIRECTS_MANAGE` | `seo_redirects.manage` | ✓ | — |
| `MAIN_MENU_MANAGE` | `main_menu.manage` | ✓ | — |
| `USERS_MANAGE` | `users.manage` | ✓ | — |
| `PERMISSIONS_VIEW` | `permissions.view` | ✓ | — |
| `LOG_VIEWER_VIEW` | `log_viewer.view` | ✓ | — |

---

## Admin Modules

| Module | Route prefix | Controller |
|--------|-------------|------------|
| Dashboard | `/admin` | `DashboardController` |
| Static Pages | `/admin/static-pages` | `StaticPageController` |
| Category Tree | `/admin/category-trees` | `CategoryTreeController` |
| Banners | `/admin/banners` | `BannerController` |
| Employees | `/admin/employees` | `TablesController` / dedicated |
| SEO Redirects | `/admin/seo-redirects` | `SeoRedirectController` |
| Main Menu | `/admin/main-menu` | `MainMenuItemController` |
| Administrators | `/admin/administrators` | `AdministratorController` |
| Permissions | `/admin/permissions` | `PermissionController` |
| Log Viewer | `/admin/log-viewer` | via package |

---

## Development Environment

The project ships with **Laravel Sail** — a Docker-based local environment.

### Containers

| Container | Service |
|-----------|---------|
| `laravel.test` | PHP 8.2 + Nginx |
| `mysql` | MySQL 8 |
| `redis` | Redis (queue / cache) |

### Useful Commands

```bash
# Start environment
./vendor/bin/sail up -d

# Stop environment
./vendor/bin/sail down

# Run Artisan
./vendor/bin/sail artisan <command>

# Run Composer
./vendor/bin/sail composer <command>

# Run npm
./vendor/bin/sail npm <command>

# Open a shell inside the container
./vendor/bin/sail shell

# Reset database with fresh seed data
./vendor/bin/sail artisan migrate:fresh --seed

# Run tests
./vendor/bin/sail artisan test
```

---

## Demo Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@mail.com` | `password` |
| Manager | `manager@mail.com` | `password` |

---

## Project Conventions

- **No ALTER TABLE** in custom migrations — all schema changes are consolidated into the original `CREATE TABLE` statement.
- **Single-pass deployment** — `migrate --seed` is the only command needed to set up a fresh database.
- **DRY / SOLID** — services encapsulate reusable logic; controllers stay under ~50 lines.
- **Bootstrap Table** (server-side) is the standard for paginated admin lists.
- **Vibecoding** — the codebase was developed iteratively with AI assistance, following structured prompt templates for feature implementation, code review, and refactoring.
