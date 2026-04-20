# Дневник изменений — 2026-04-01 (architecture_js_scripts)

## 16:51 (Europe/Riga) feat[views.admin,frontend.vite] — Перенос admin JS из maket в resources/views/admin/assets/js
**Entry ID:** 20260401T165100RIGA
**Дата:** 2026-04-01
**Ветка:** architecture_js_scripts

### Файлы
- `vite.config.js` (+2 −0)
- `resources/views/admin/assets/js/scripts.js` (+20 −0)
- `resources/views/admin/assets/js/datatables-simple-demo.js` (+7 −0)
- `resources/views/admin/layouts/sb-admin.blade.php` (+1 −1)
- `resources/views/admin/layouts/sb-admin-static.blade.php` (+1 −1)
- `resources/views/admin/layouts/auth.blade.php` (+1 −1)
- `resources/views/admin/layouts/error.blade.php` (+1 −1)
- `resources/views/admin/dashboard.blade.php` (+1 −1)
- `resources/views/admin/tables.blade.php` (+1 −1)

### Что сделано
JS-файлы `scripts.js` и `datatables-simple-demo.js` перенесены в `resources/views/admin/assets/js` и подключены через `@vite`. В `vite.config.js` добавлены новые JS entrypoints для сборки. Все Blade-подключения, которые использовали `maketAsset('js/...')`, переключены на Vite-ассеты.

### Почему
Нужно перестать использовать `maket/js/*` как источник JS в админской части и перейти к проектной структуре ресурсов со сборкой через Vite.

### Влияние
- **БД:** N/A
- **API:** N/A
- **Производительность:** JS теперь проходит через Vite-пайплайн, что улучшает управление кэшированием собранных ассетов

### Проверено
- Тесты: N/A
- Линтер: ok

### Follow-up
- [ ] N/A

## 17:19 (Europe/Riga) feat[views.admin,frontend.vite] — Подключение jQuery через Sail и Vite
**Entry ID:** 20260401T171900RIGA
**Дата:** 2026-04-01
**Ветка:** architecture_js_scripts

### Файлы
- `package.json` (+1 −0)
- `package-lock.json` (обновлён)
- `resources/views/admin/assets/js/scripts.js` (+5 −0)

### Что сделано
Через `./vendor/bin/sail npm install jquery` добавлен пакет `jquery` последней версии в зависимости проекта. В admin entrypoint `resources/views/admin/assets/js/scripts.js` добавлен ESM-импорт jQuery и экспорт в глобальные `window.$` и `window.jQuery` для совместимости с существующими скриптами.

### Почему
Нужно централизованно подключить jQuery в текущем Sail/Vite-процессе сборки, без CDN и без прямых ручных подключений в Blade.

### Влияние
- **БД:** N/A
- **API:** N/A
- **Производительность:** минимальное увеличение JS-бандла за счёт включения jQuery

### Проверено
- Тесты: N/A
- Линтер: ok

### Follow-up
- [ ] N/A
