# Дневник — 2026-04-06 — modals_alers

## 18:45 (Europe/Kyiv) feat[views.admin,styles.admin.theme,auth.admin,frontend.vite] — Toast-уведомления и модальные подтверждения админки
**Entry ID:** 01JNOTIFYALR20260406
**Дата:** 2026-04-06
**Ветка:** modals_alers

### Файлы
- `resources/themes/admin/assets/js/admin-ui.js` (+новый)
- `resources/themes/admin/assets/js/sb-admin-scripts.js` (+импорт admin-ui)
- `resources/themes/admin/assets/js/admin-bootstrap-table.js` (+adminUiDialog вместо confirm)
- `resources/themes/admin/assets/css/blocks/_admin-ui.scss` (+новый)
- `resources/themes/admin/assets/css/app.scss` (+блок admin-ui)
- `resources/views/admin/partials/ui-shell.blade.php` (+новый)
- `resources/views/admin/partials/admin-ui-flash.blade.php` (+новый)
- `resources/views/admin/layouts/sb-admin.blade.php` (+ui-shell, flash после Vite)
- `resources/views/admin/layouts/sb-admin-static.blade.php` (+то же)
- `resources/views/admin/layouts/auth.blade.php` (+ui-shell, Vite sb-admin-scripts, flash)
- `resources/views/admin/pages/static-pages/view.blade.php` (−inline alert)
- `resources/views/admin/auth/login.blade.php` (−alert)
- `resources/views/admin/auth/password-request.blade.php` (−alert)
- `resources/views/admin/auth/register.blade.php` (−alert)
- `app/Http/Controllers/Admin/Auth/RegisterController.php` (+status_notify_variant info)
- `public/build/*` (локальная сборка Vite, при необходимости пересобрать)

### Что сделано
Flash-сообщения (`success`, `error`, `status`) выводятся как тосты справа сверху на 5 с вместо Bootstrap alert. Добавлено одно переиспользуемое модальное окно Bootstrap 5 с четырьмя режимами кнопок: `yes_no`, `ok`, `close`, `yes_no_cancel` через `window.adminUiDialog`. Удаление строки в bootstrap-table вызывает `yes_no` вместо `window.confirm`. Стили уведомлений — цветная обводка и текст на фоне body, без заливки success.

### Почему
Единый UX для сообщений и подтверждений, без нативных диалогов и без блокирующих alert в разметке.

### Влияние
- **БД:** N/A
- **API:** N/A
- **Производительность:** N/A

### Проверено
- Тесты: `php artisan test` — 18 passed
- Линтер: pint ok

### Follow-up
- [ ] При смене окружения выполнить `npm run build` (или Sail), если `public/build` не в репозитории.

## 19:30 (Europe/Kyiv) refactor[views.admin,auth.admin] — Auth без ui-shell и тостов
**Entry ID:** 01JAUTHNOMODAL20260406
**Дата:** 2026-04-06
**Ветка:** modals_alers

### Файлы
- `resources/views/admin/layouts/auth.blade.php` (−ui-shell, −Vite scripts, −flash; только Bootstrap + stack)
- `resources/views/admin/auth/login.blade.php` (+alert success для `status`)
- `resources/views/admin/auth/password-request.blade.php` (+alert success)
- `resources/views/admin/auth/register.blade.php` (+alert info)
- `app/Http/Controllers/Admin/Auth/RegisterController.php` (−`status_notify_variant`)

### Что сделано
Страницы входа, сброса пароля и регистрации снова показывают классические Bootstrap alert в карточке; layout auth не подключает модалку, стек тостов и `sb-admin-scripts`.

### Почему
Запрос вернуть первоначальный вид Auth без модалок/общего admin-ui на этих экранах.

### Влияние
- **БД:** N/A
- **API:** N/A
- **Производительность:** N/A

### Проверено
- Тесты: 18 passed
- Линтер: pint ok

### Follow-up
- N/A

## 20:00 (Europe/Kyiv) feat[auth.admin,views.admin,controller.admin.auth.register] — POST register → login без alert
**Entry ID:** 01JREGREDIRECT20260406
**Дата:** 2026-04-06
**Ветка:** modals_alers

### Файлы
- `app/Http/Controllers/Admin/Auth/RegisterController.php` (редирект на `admin.entry`, без flash)
- `resources/views/admin/auth/register.blade.php` (−alert `status`)

### Что сделано
После успешной валидации формы регистрации выполняется редирект на точку входа админки (экран логина) без сообщения `alert-info` на странице регистрации.

### Почему
Требование убрать информационный alert и ограничиться переходом на login.

### Влияние
- **БД:** N/A
- **API:** N/A
- **Производительность:** N/A

### Проверено
- Тесты: 18 passed, pint ok

### Follow-up
- Ключ `admin.register_disabled` в `lang/en/admin.php` сейчас не используется (можно удалить при уборке переводов).

## 20:30 (Europe/Kyiv) fix[model.administrator,auth.admin,request.admin.auth.register] — Регистрация создаёт Administrator
**Entry ID:** 01JREGCREATEADM20260406
**Дата:** 2026-04-06
**Ветка:** modals_alers

### Файлы
- `app/Http/Controllers/Admin/Auth/RegisterController.php` (`Administrator::create`, имя из first/last)
- `app/Http/Requests/Admin/Auth/RegisterAccountRequest.php` (`Rule::unique` на `administrators.email`)
- `tests/Feature/Admin/AdminAuthTest.php` (+2 кейса: успех + дубликат email)

### Что сделано
POST `/adm/register` сохраняет запись в `administrators` (поля `name`, `email`, `password` с кастом хэша, `email_verified_at`). После регистрации гость может войти теми же email/паролем. Дубликат email валидируется на уникальность.

### Почему
Ранее редирект на login выполнялся без создания пользователя, из‑за чего вход был невозможен.

### Влияние
- **БД:** новые строки в `administrators` при регистрации
- **API:** N/A
- **Производительность:** N/A

### Проверено
- Тесты: `php artisan test` — 20 passed
- Линтер: pint ok

### Follow-up
- N/A

## 21:00 (Europe/Kyiv) feat[controller.admin.static-page,styles.admin.theme] — Notify после удаления в таблице
**Entry ID:** 01JBTDELNOTIFY20260406
**Дата:** 2026-04-06
**Ветка:** modals_alers

### Файлы
- `app/Http/Controllers/Admin/StaticPageController.php` (`destroy`: JSON `{ message }` при `wantsJson`, ошибка 422)
- `resources/themes/admin/assets/js/admin-bootstrap-table.js` (`adminNotify` success/danger, задержка перед reload)
- `tests/Feature/Admin/StaticPageAdminTest.php` (+2 JSON-теста)

### Что сделано
AJAX-удаление из bootstrap-table получает JSON с текстом результата; клиент показывает тост success или danger и только при успехе перезагружает страницу (короткая пауза, чтобы был виден success-тост).

### Почему
Требование показывать результат удаления через notify, включая ошибку (например, страница с дочерними).

### Влияние
- **БД:** N/A
- **API:** DELETE destroy при `Accept: application/json` — тело JSON вместо редиректа
- **Производительность:** N/A

### Проверено
- Тесты: 22 passed, pint ok

### Follow-up
- Другие ресурсы с `adminBootstrapTableDelete` при появлении — такой же контракт JSON в `destroy`.
