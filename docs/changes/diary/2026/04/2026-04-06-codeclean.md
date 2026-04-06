# Дневник изменений — 2026-04-06 (codeclean)

## 14:30 (Europe/Kyiv) refactor[views.admin,frontend.vite,styles.admin.theme] — SB Admin scripts.js в тему + Vite
**Entry ID:** 01JSBADMSCRIPTS20260406
**Дата:** 2026-04-06
**Ветка:** code_clean

### Файлы
- `resources/themes/admin/assets/js/sb-admin-scripts.js` (новый)
- `vite.config.js` (+1 input)
- `resources/views/admin/layouts/sb-admin.blade.php`
- `resources/views/admin/layouts/sb-admin-static.blade.php`
- `resources/views/admin/layouts/auth.blade.php`
- `resources/views/admin/layouts/error.blade.php`
- `public/maket/js/scripts.js` (удалён)
- `maket/js/scripts.js` (удалён)

### Что сделано
Логика переключения сайдбара SB Admin перенесена из `maket/js/scripts.js` в `resources/themes/admin/assets/js/sb-admin-scripts.js` и подключается через `@vite` в layout `sb-admin` и `sb-admin-static`. На layout `auth` и `error` подключение убрано: элемента `#sidebarToggle` нет, скрипт не выполнял осмысленной работы. Файлы `scripts.js` удалены из `maket/` и `public/maket/`.

### Почему
Задача codeclean: убрать зависимость основного layout от статики макета; единая линия с остальными admin JS через Vite.

### Влияние
- **БД:** N/A
- **API:** N/A
- **Производительность:** на страницах auth/error меньше лишних запросов к скриптам

### Проверено
- Тесты: Feature Admin (auth + pages)
- Линтер: `./vendor/bin/sail npm run build` — ok

### Follow-up
- [x] Chart demos и 404 SVG перенесены; каталоги `maket/` удалены (см. запись ниже).

---

## 16:00 (Europe/Kyiv) refactor[views.admin,frontend.vite,styles.admin.theme,app.helpers.admin] — полный отказ от /maket
**Entry ID:** 01JMAKETRMV20260406
**Дата:** 2026-04-06
**Ветка:** code_clean

### Файлы
- `resources/themes/admin/assets/js/admin-chart-demos.js` (новый)
- `resources/themes/admin/assets/img/error-404-monochrome.svg` (новый)
- `app/Helpers/AdminHelper.php` — `maketAsset` заменён на `themeAssetDataUri()` для файлов из `resources/themes/admin/assets/`
- `resources/views/admin/dashboard.blade.php`, `charts.blade.php`, `errors/404.blade.php`
- `vite.config.js` (+ input `admin-chart-demos.js`)
- каталоги `maket/` и `public/maket/` удалены

### Что сделано
Демо Chart.js (area/bar/pie) собраны в один Vite entry `admin-chart-demos.js`; на страницах по-прежнему подключается Chart.js 2.8 с CDN, затем бандл. Иллюстрация 404 перенесена в тему; в Blade используется data-URI через хелпер. Неиспользуемые в шаблонах файлы (`datatables-simple-demo.js`, jQuery datatables demo, `styles.css` макета) удалены вместе с корнем `maket`.

### Почему
Макет не является частью runtime-приложения; статика должна жить в теме и в Vite.

### Влияние
- **БД:** N/A
- **API:** N/A
- **Производительность:** N/A (404: небольшой data-URI вместо отдельного HTTP к `/maket/...`)

### Проверено
- Тесты: `./vendor/bin/sail artisan test tests/Feature/Admin/`
- Сборка: `./vendor/bin/sail npm run build`, Pint

### Follow-up
- [ ] N/A

---

## 16:45 (Europe/Kyiv) merge[views.admin,frontend.vite] — code_clean → development
**Entry ID:** 01JMRGDEV20260406
**Дата:** 2026-04-06
**Merge:** code_clean → development

### Ветки
- **Source:** code_clean
- **Target:** development
- **Merge commit:** fast-forward (линейная история до `e372652`)

### Файлы
- См. коммит `e372652` и fast-forward на `development` (25 файлов: тема, Vite, удаление `public/maket`, дневник/теги).

### Что смерджено
В `development` перенесены refactor админской статики: `AdminHelper`, JS темы и демо графиков через Vite, отказ от каталога `public/maket`, обновления Blade и документации изменений.

### Конфликты
- **Были конфликты:** нет

### Влияние
- **БД:** N/A
- **API:** N/A
- **Производительность:** N/A

### Проверено
- Тесты: до merge — `tests/Feature/Admin/`
- Линтер: Pint; сборка: Sail `npm run build`
- Конфликты: N/A

### Follow-up
- [ ] При необходимости: `git push origin development`
