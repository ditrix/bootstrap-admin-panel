## Laraver admin

based on html maket (https://github.com/startbootstrap/startbootstrap-sb-admin)

### Installation

### 1. Clone Repository

```bash
git clone git@github.com:ditrix/bootstrap-admin-panel.git
```

### 2. Install Composer


### 3. Install Docker


### 4. Install Laravel Sail



### 5. Configure Environment



#### 5.1 Copy Environment File

Copy `.env.example` to the `.env`

#### 5.2 Configure Basic Settings

After copying `.env.example` → `.env`, check **Sail-specific** variables at the top of the file (`APP_PORT`, `VITE_PORT`, `WWWGROUP`, `WWWUSER`, `DB_HOST=mysql`, …). On Linux/macOS, match your UID/GID if permission issues appear:

```bash
id -u   # → WWWUSER
id -g   # → WWWGROUP
```

The template already includes the essentials:

```env
APP_NAME="Admin"
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

### 6. Setup Docker Environment

#### 6.1 Prepare Docker Compose Configuration

#### 6.2 Build Docker Containers

#### 6.3 Start Docker Containers

## Database

### 7. Import Database

#### 7.2 Run Migrations

If you need to run additional migrations:

```bash
./vendor/bin/sail artisan migrate
```

#### 7.3 Generate Application Key

```bash
./vendor/bin/sail artisan key:generate
```
#### 7.4 Generate Storage Link

```bash        
./vendor/bin/sail artisan storage:link
```

#### 7.5 Install Frontend Dependencies

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

Production build (also run inside Sail so Rollup picks the Linux binaries):

```bash
./vendor/bin/sail npm run build
```

If `vite build` fails with `Cannot find module '@rollup/rollup-linux-...'`, pull the latest `package.json` / `package-lock.json` (root `optionalDependencies` pin Rollup natives for npm’s optional-deps bug), then `rm -rf node_modules` and `./vendor/bin/sail npm install` again.

#### 7.6 Clear Caches

```bash
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan route:clear
```

