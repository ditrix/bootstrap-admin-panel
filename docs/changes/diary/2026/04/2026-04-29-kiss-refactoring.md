# 2026-04-29 — KISS рефакторинг Tree и Table модулей

## 13:27 (Europe/Kyiv) refactor[service.admin.abstract-tree,helper.bootstrap-table,service.admin.category-tree,service.admin.main-menu-item,controller.admin.main-menu-item] — KISS рефакторинг Tree и Table модулей
**Entry ID:** 01JKISS20260429TREE
**Agent:** Sonnet 4.6
**Дата:** 2026-04-29
**Ветка:** development

### Файлы
- `app/Helpers/BootstrapTableHelper.php` (+45 −0)
- `app/Services/Admin/AbstractTreeService.php` (+110 −0)
- `app/Services/Admin/CategoryTreeService.php` (+15 −88)
- `app/Services/Admin/MainMenuItemService.php` (+75 −119)
- `app/Http/Controllers/Admin/MainMenuItemController.php` (+67 −100)
- `app/Services/Admin/AdministratorListingService.php` (+5 −18)
- `app/Services/Admin/EmployeeListingService.php` (+5 −18)
- `app/Services/Admin/SeoRedirectListingService.php` (+5 −18)
- `app/Services/Admin/StaticPageListingService.php` (+5 −18)

### Что сделано
Создан `AbstractTreeService` с общей логикой дерева (`buildGroupedTree`, `allNodesOrderedForMeta`, `saveOrder` с транзакцией, `deleteNodeReparentingChildren`). `CategoryTreeService` и `MainMenuItemService` теперь наследуют от него — их тела сводятся к одному методу `modelClass()`. Создан `BootstrapTableHelper` со статическими методами `stringCastType()` и `parsePaginationParams()`, которые устранили дублирование в 4 listing services. Логика `createItem()` и `buildParentOptionsHtml()` перенесена из контроллера в `MainMenuItemService`.

### Почему
`CategoryTreeService` и `MainMenuItemService` содержали идентичные структурные методы, различаясь только классом модели. Все 4 listing services дублировали `stringCastType()` и блок парсинга параметров. `MainMenuItemController::store()` содержал прямой DB-запрос за `max(sort_no)`, нарушая SRP. Также устранено несоответствие: `CategoryTreeService::saveOrder()` не оборачивал операции в транзакцию, хотя `MainMenuItemService::saveOrder()` — оборачивал.

### Влияние
- **БД:** N/A
- **API:** N/A (публичный контракт сервисов сохранён)
- **Производительность:** N/A

### Проверено
- Тесты: N/A (запрос на тесты не поступал)
- Линтер: ok (Pint — pass)

### Follow-up
- [ ] N/A
****