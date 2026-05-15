# Дневник — 2026-05-15

## 18:00 (Europe/Kyiv) docs[documentation.admin-roles,seeders] — Документация ролей и прав; сидеры: два пользователя

**Entry ID:** 01JDOCSADMINPERM20260515  
**Agent:** Composer  
**Дата:** 2026-05-15  
**Ветка:** development  

### Файлы

- `docs/admin-roles-and-permissions.md` (+новый)
- `docs/changes/diary/2026/05/2026-05-15-admin-roles-permissions.md` (+новый)
- По реализации (справочно): `database/seeders/AdminSeeder.php`, `ConfigurationModuleSeeder.php`, `AppServiceProvider.php`, промпт `.cursor/promts/roles_and_permitions.md`

### Что сделано

Добавлен справочный документ **ролей и прав** админ-панели: таблицы Spatie, guard `admin`, список permissions, связь `role_id`, middleware, Blade `@can`, редирект после входа, Log Viewer, шаги расширения. Зафиксировано правило сидеров пользователей: только **admin@mail.com** и **manager@mail.com**, без массовых демо-аккаунтов в `ConfigurationModuleSeeder`. Композер макета подключён к `admin.pages.*`, чтобы переменная `adminHomeUrl` была доступна на странице permissions.

### Почему

Нужна единая точка входа для разработчиков по RBAC после внедрения Spatie; устранение ошибки вида Undefined variable `$adminHomeUrl` на вложенных Blade без дублирования логики в каждом контроллере.

### Влияние

- **БД:** N/A (документация; схема описана)
- **API:** N/A
- **Производительность:** N/A

### Проверено

- Тесты: ранее в сессии `php artisan test` — зелёные
- Линтер: N/A для новых только `.md`

### Follow-up

- [ ] N/A
