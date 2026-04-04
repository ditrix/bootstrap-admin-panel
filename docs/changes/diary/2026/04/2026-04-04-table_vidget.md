# Дневник изменений — 2026-04-04 (table_vidget)

## 14:00 (Europe/Kyiv) feat[views.admin,styles.admin.theme,docs.admin.bootstrap-table] — Стили виджета bootstrap-table по макету SB Admin
**Entry ID:** 01JBTABLESTYLE20260404
**Дата:** 2026-04-04
**Ветка:** table_vidget

### Файлы
- `resources/views/admin/partials/bootstrap-table-widget.blade.php` (+3 −5)
- `resources/themes/admin/assets/css/blocks/_bootstrap-table.scss` (+360 −13)
- `docs/admin-bootstrap-table.md` (+2 −2)

### Что сделано
Убрано подключение `bootstrap-table.min.css` с CDN; разметка и базовые правила плагина перенесены в SCSS темы. Таблица обёрнута в `admin-bootstrap-table`, для сетки используется класс `admin-bootstrap-table__grid` вместо Bootstrap `table table-bordered`. Визуал (отступы, границы, hover, тулбар, пагинация) выровнен с макетом SB Admin / simple-datatables через переменные `--bs-*`. Обновлена справка `docs/admin-bootstrap-table.md`.

### Почему
Задача: единый вид админских таблиц с макетом без отдельного CSS bootstrap-table с CDN.

### Влияние
- **БД:** N/A
- **API:** N/A
- **Производительность:** N/A (на странице на один HTTP-запрос меньше к CDN)

### Проверено
- Тесты: N/A (разметка/CSS)
- Линтер: ok (`npm run build`)

### Follow-up
- [ ] N/A
