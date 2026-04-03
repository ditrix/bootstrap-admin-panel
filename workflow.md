# Workflow

## 2026-04-03 — npm / Rollup: чистая установка в Sail

**Статус:** готово.

**Контекст:** Ошибка `Cannot find module '@rollup/rollup-linux-arm64-gnu'` при `./vendor/bin/sail npm run build` (баг npm с optional dependencies). Выполнено: `rm -rf node_modules package-lock.json`, затем `./vendor/bin/sail npm install` и `./vendor/bin/sail npm run build` — OK.

**Следующий шаг:** закоммитить обновлённый `package-lock.json` при необходимости.

---

## 2026-04-03 — StaticPage CRUD + bootstrap-table (Sail)

**Команды:** по таску предпочтительно `./vendor/bin/sail artisan …`, `./vendor/bin/sail npm run build`, `./vendor/bin/sail test`. Если контейнеры Sail не запущены, миграции на MySQL из хоста могут падать (`getaddrinfo for mysql failed`).

**Сделано**

- Модель `StaticPage`, миграция, фабрика, `StaticPageSeeder`, CRUD в админке (`adm/static-pages`), JSON для bootstrap-table `{ total, rows }`.
- Виджет `admin/partials/bootstrap-table-widget.blade.php` (CDN jQuery + bootstrap-table, настраиваемые колонки и formatter для actions).
- Демо-страницы `dashboard` и `tables` переведены с Simple-DataTables на server-side bootstrap-table; `GET admin/api/employees` отдаёт `total`/`rows`.
- Тема: блок SCSS `blocks/_bootstrap-table.scss`, общий JS для delete вынесен во встроенный скрипт виджета (без отдельного Vite-entry — манифест не ломается, если `npm run build` не гоняли после правок).
- Тесты: `tests/Feature/Admin/AdminPagesTest.php`, `tests/Feature/Admin/StaticPageAdminTest.php`.
- Дополнительно: колонки `created_at` / `updated_at` с сортировкой; поиск по числам и датам в listing-сервисах; даты в JSON таблиц в формате `d.m.Y`.
- Описание для разработчиков: [`docs/admin-bootstrap-table.md`](docs/admin-bootstrap-table.md).

**Проверено (локально без Sail MySQL)**

- `./vendor/bin/phpunit tests/Feature/Admin/AdminPagesTest.php tests/Feature/Admin/StaticPageAdminTest.php` — OK
- `./vendor/bin/pint --dirty` — OK

**Следующий шаг**

- При работающем стеке: `./vendor/bin/sail up -d`, затем `./vendor/bin/sail artisan migrate`, при необходимости `./vendor/bin/sail npm run build`, `./vendor/bin/sail artisan test`.

---

## 2026-04-03 — Vite: admin-bootstrap-table.js

**Статус:** готово (виджет + `vite.config.js`, текст на `tables`, правка `docs/admin-bootstrap-table.md`).

**Контекст:** Хелпер `adminBootstrapTableDelete` грузится из `resources/themes/admin/assets/js/admin-bootstrap-table.js` через `@vite` в `bootstrap-table-widget`, без inline-дубля.

**Проверено:** `npm run build`; `php artisan test tests/Feature/Admin/AdminPagesTest.php tests/Feature/Admin/StaticPageAdminTest.php`.

**Следующий шаг:** при необходимости полный `php artisan test`; если `vite build` падает на optional Rollup — чистая `npm install` (см. дневник Follow-up).

---

## 2026-04-03 — Sail: .env.example + compose + Vite

**Статус:** готово.

**Контекст:** `.env.example` совпадает с `compose.yaml` (DB_HOST=`mysql`, sail/пароль). `laravel.test` ждёт healthy `mysql`. Vite `server.host`/`hmr`/`usePolling` для `./vendor/bin/sail npm run dev`.

**Следующий шаг:** после остановки Sail при новом клоне скопировать `.env.example` → `.env`, при необходимости подставить `WWWUSER`/`WWWGROUP` с хоста (`id -u`, `id -g`).

---

## 2026-04-03 — npm / Rollup в Sail

**Статус:** в `package.json` добавлены `optionalDependencies` на `@rollup/rollup-*@4.60.1` (linux gnu + darwin), обновлён lockfile, в README — `sail npm run build` и troubleshooting.

**Следующий шаг:** `./vendor/bin/sail up -d`, затем `rm -rf node_modules && ./vendor/bin/sail npm install && ./vendor/bin/sail npm run build` (на чистой установке в контейнере).
