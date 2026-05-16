# Bootstrap Admin Panel

Laravel-адмін-панель на основі шаблону [SB Admin](https://github.com/startbootstrap/startbootstrap-sb-admin). Проєкт розроблено з використанням методик **vibecoding** — ітеративної AI-асистованої розробки зі структурованими промптами та автоматизованим code review.

> **Технічна документація:** [technical_documentation.md](technical_documentation.md)

---

## Вимоги

| Інструмент | Версія |
|-----------|--------|
| Docker & Docker Compose | latest |
| Git | будь-яка |

Все інше (PHP, Composer, Node.js, MySQL) працює всередині Docker через Laravel Sail.

---

## Швидкий старт

### 1. Клонувати репозиторій

```bash
git clone git@github.com:ditrix/bootstrap-admin-panel.git
cd bootstrap-admin-panel
```

### 2. Встановити залежності Composer

```bash
composer install --dev
```

### 3. Налаштувати оточення

```bash
cp .env.example .env
```

Відкрийте `.env` та перевірте змінні. На Linux встановіть `WWWUSER` і `WWWGROUP` відповідно до вашого UID/GID:

```bash
id -u   # → значення WWWUSER
id -g   # → значення WWWGROUP
```

Стандартні значення, що працюють одразу:

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

### 4. Запустити контейнери

```bash
./vendor/bin/sail up -d
```

### 5. Згенерувати ключ застосунку

```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan storage:link
```

### 6. Виконати міграції та наповнити базу демо-даними

```bash
./vendor/bin/sail artisan migrate --seed
```

Всі міграції виконуються **в один прогін**, база наповнюється тестовими записами:

| Сутність | Записів |
|----------|---------|
| Адміністратор (admin) | `admin@mail.com` / `password` |
| Адміністратор (manager) | `manager@mail.com` / `password` |
| Статичні сторінки | 5 |
| Банери | 2 |
| Вузли дерева категорій | 20 |
| Співробітники | 50 |
| Пункти головного меню | 10 |
| SEO-редиректи | 10 |

### 7. Встановити фронтенд-залежності та зібрати ресурси

```bash
npm install
npm run dev
```

### 8. Очистити кеші (необов'язково)

```bash
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan route:clear
```

---

## Доступ

| URL | Облікові дані |
|-----|--------------|
| `http://localhost/admin` | `admin@mail.com` / `password` |

---

## Щоденна робота

```bash
# Запуск
./vendor/bin/sail up -d

# Зупинка
./vendor/bin/sail down

# Скинути базу
./vendor/bin/sail artisan migrate:fresh --seed

# Artisan-команди
./vendor/bin/sail artisan <команда>

# Тести
./vendor/bin/sail artisan test
```

---

## Ліцензія

MIT
