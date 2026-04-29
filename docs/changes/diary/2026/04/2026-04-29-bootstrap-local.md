# Дневник изменений — 2026-04-29 — bootstrap_5

## 16:00 (Europe/Kyiv) feat[frontend.vite,views.admin,styles.admin.theme] — Bootstrap 5 JS из npm вместо CDN
**Entry ID:** 01JBSTRP5VITE20260429
**Agent:** Composer
**Дата:** 2026-04-29
**Ветка:** bootstrap_5

### Файлы
- `package.json` (+1 зависимость `@popperjs/core`)
- `vite.config.js` (+1 input `resources/themes/admin/assets/js/app.js`)
- `resources/themes/admin/assets/css/app.scss` (+комментарий порядка импорта)
- `resources/themes/admin/assets/js/app.js` (+новый entry: `window.bootstrap`)
- `resources/views/admin/layouts/sb-admin.blade.php` (CDN → Vite)
- `resources/views/admin/layouts/sb-admin-static.blade.php` (CDN → Vite)
- `resources/views/admin/layouts/auth.blade.php` (CDN → Vite)
- `resources/views/admin/layouts/error.blade.php` (CDN → Vite)

### Что сделано
Добавлена точка входа `app.js` для темы admin: импорт Bootstrap из npm и присвоение `window.bootstrap` для инлайна и `admin-ui.js`. В `vite.config.js` зарегистрирован новый input. В макетах SB Admin, auth и error удалён CDN `bootstrap.bundle.min.js`, скрипты подключаются через `@vite`; порядок: сначала `app.js`, затем `sb-admin-scripts.js` где нужно. В `package.json` явно добавлена зависимость `@popperjs/core` (как у полной связки Bootstrap + Popper). В `app.scss` зафиксирован комментарием порядок: переменные → Bootstrap → base → blocks.

### Почему
Переход админки на автономный бандл без CDN при сохранении глобального API `window.bootstrap`.

### Влияние
- **БД:** N/A
- **API:** N/A
- **Производительность:** N/A

### Проверено
- Тесты: N/A (изменения только фронт/Blade/Vite)
- Линтер: todo
- Сборка: `./vendor/bin/sail npm run build` — ok

### Follow-up
- [ ] При необходимости закоммитить обновлённый `package-lock.json` после `sail npm install` на хосте
