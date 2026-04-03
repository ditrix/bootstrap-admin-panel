# Workflow

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
