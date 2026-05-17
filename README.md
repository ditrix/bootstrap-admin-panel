# Bootstrap Admin Panel

A Laravel-based admin panel built on the [SB Admin](https://github.com/startbootstrap/startbootstrap-sb-admin) Bootstrap template. The project was developed using **vibecoding** methodologies — iterative AI-assisted development with structured prompting and automated code review.

> **Technical documentation:** [technical_documentation.md](technical_documentation.md)

---

## Features

- Drag-and-drop tree structures
- CRUD operations
- WYSIWYG editor
- Roles and permissions
- Log viewer

---

## Requirements

| Tool | Version |
|------|---------|
| Docker & Docker Compose | latest |
| Git | any |

Everything else (PHP, Composer, Node.js, MySQL) runs inside Docker via Laravel Sail.

---

## Quick Start

### 1. Clone the repository

```bash
git clone git@github.com:ditrix/bootstrap-admin-panel.git
cd bootstrap-admin-panel
```

### 2. Install Composer dependencies

```bash
composer install --dev
```

### 3. Configure environment

```bash
cp .env.example .env
```

Open `.env` and verify the following variables. On Linux, match `WWWUSER` and `WWWGROUP` to your own UID/GID:

```bash
id -u   # → WWWUSER value
id -g   # → WWWGROUP value
```

Default values that work out of the box:

```env
APP_NAME="Admin Panel"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

APP_PORT=80
VITE_PORT=5173
FORWARD_DB_PORT=3306
WWWGROUP=1000
WWWUSER=1000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=admin_panel
DB_USERNAME=sail
DB_PASSWORD=password
```

### 4. Start containers

```bash
./vendor/bin/sail up -d
```

### 5. Generate application key

```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan storage:link
```

### 6. Run migrations and seed demo data

```bash
./vendor/bin/sail artisan migrate --seed
```

This runs all migrations in a **single pass** and populates the database with demo records:

| Entity | Records |
|--------|---------|
| Administrator (admin) | `admin@mail.com` / `password` |
| Administrator (manager) | `manager@mail.com` / `password` |
| Static Pages | 5 |
| Banners | 2 |
| Category Tree nodes | 20 |
| Employees | 50 |
| Main Menu Items | 10 |
| SEO Redirects | 10 |

### 7. Install frontend dependencies and build assets

```bash
npm install
npm run dev
```

### 8. Clear caches (optional)

```bash
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan route:clear
```

---

## Access

| URL | Credentials |
|-----|------------|
| `http://localhost/admin` | `admin@mail.com` / `password` |

---

## Daily Workflow

```bash
# Start
./vendor/bin/sail up -d

# Stop
./vendor/bin/sail down

# Reset database
./vendor/bin/sail artisan migrate:fresh --seed

# Artisan commands
./vendor/bin/sail artisan <command>

# Run tests
./vendor/bin/sail artisan test
```

---

## License

MIT
