# Workflow

## 2026-04-07 — Merge `modals_alers` → `development`

**Статус:** готово (fast-forward).

**Контекст:** Локально `development` обновлена до `modals_alers`; перед merge удалены неотслеживаемые `.cursor/*`, блокировавшие checkout. Задокументировано: `docs/changes/diary/2026/04/2026-04-07-development.md`.

**Следующий шаг:** `git push origin development`; при необходимости полный `php artisan test` и `npm run build`.

---

## 2026-04-06 — Удаление в bootstrap-table: JSON + adminNotify

**Статус:** готово.

**Контекст:** `StaticPageController@destroy` отдаёт JSON для запросов с `wantsJson`; `adminBootstrapTableDelete` вызывает `adminNotify` (success/danger), reload с задержкой 400 ms при успехе.

**Следующий шаг:** для новых таблиц с DELETE — повторить JSON-ответы в контроллерах.

---

## 2026-04-06 — Регистрация админа: запись в БД + тесты

**Статус:** готово.

**Контекст:** `RegisterController@store` создаёт `Administrator` (имя из first/last name); `RegisterAccountRequest` — unique email; feature-тесты: вход после регистрации и отказ при дубликате email.

**Следующий шаг:** при необходимости отключить публичную регистрацию — middleware/флаг.

---

## 2026-04-06 — Auth: без modals/admin-ui (промпт `auth_no_modal`)

**Статус:** готово.

**Контекст:** Layout `auth` снова только Bootstrap + `@stack('scripts')`; login / password-request / register — `alert alert-*` в карточке; `RegisterController` без `status_notify_variant`.

**Следующий шаг:** N/A.

---

## 2026-04-06 — admin-ui: тосты + модалки подтверждения (modals_alers)

**Статус:** готово.

**Контекст:** Задача `6_notify_alerts`: flash через `adminNotify`, `window.confirm` заменён на `adminUiDialog`; общие partials `ui-shell` / `admin-ui-flash`; SCSS `_admin-ui.scss`. После правок фронта — `npm run build` (при отсутствии `@rollup/rollup-darwin-arm64` в node_modules — `npm i` или Sail).

**Следующий шаг:** при необходимости прогнать страницы с таблицей и auth во всплывающих уведомлениях.

---

## 2026-04-06 — удалены `maket/` и `public/maket/`

**Статус:** готово.

**Контекст:** Графики dashboard/charts — `admin-chart-demos.js` (Vite) + Chart.js 2.8 с CDN; 404 — SVG в `resources/themes/admin/assets/img/` и `AdminHelper::themeAssetDataUri()`. Каталоги макета удалены. Сборка: `./vendor/bin/sail npm run build`.

**Следующий шаг:** при появлении ссылок на `maket` в старых доках — считать устаревшим.

---

## 2026-04-06 — codeclean: `maket/js/scripts.js` → тема + Vite (Sail)

**Статус:** готово.

**Контекст:** По промпту `codeclean.md`: код SB Admin для `#sidebarToggle` перенесён в `resources/themes/admin/assets/js/sb-admin-scripts.js`, добавлен input в `vite.config.js`, в `sb-admin` / `sb-admin-static` — `@vite([...])`. С `auth` / `error` убрано бесполезное подключение (нет сайдбара). Удалены `maket/js/scripts.js` и `public/maket/js/scripts.js`. Сборка: `./vendor/bin/sail npm install` (при ошибке Rollup linux в контейнере) и `./vendor/bin/sail npm run build`.

**Следующий шаг:** при смене ветки/клоне — в Sail снова `npm run build`; опционально перенос chart/datatables demo с `public/maket`.

---

## 2026-04-06 — /adm: Vite manifest для admin-bootstrap-table.js

**Статус:** готово.

**Контекст:** `ViteException: Unable to locate file in Vite manifest: resources/themes/admin/assets/js/admin-bootstrap-table.js` — entry уже был в `vite.config.js`, но `public/build/manifest.json` был от старой сборки (в нём не было этого ключа). На хосте дополнительно отсутствовал `@rollup/rollup-darwin-arm64` (optional deps npm); выполнено `npm install`, затем `npm run build` — в манифесте появился `admin-bootstrap-table`.

**Следующий шаг:** в разработке можно `npm run dev` вместо build; после изменений фронта — снова `npm run build` (папка `public/build` в `.gitignore`).

---

## 2026-04-04 — bootstrap-table: поиск «поле + ×», без refresh, без focus-shadow

**Статус:** готово.

**Контекст:** По промпту `table_input_style`: отключён refresh, включена кнопка очистки поиска bootstrap-table, подпись «×» через `post-header`, стили input-group и `box-shadow: none` на фокусе внутри `.admin-bootstrap-table`. Сборка: `./vendor/bin/sail npm run build`.

**Следующий шаг:** при необходимости прогнать страницы с таблицей в браузере.

---

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
