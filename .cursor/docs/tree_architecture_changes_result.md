# Реализация рефакторинга Tree-модулей (A + E + B)

> **Дата:** 2026-05-03  
> **Статус:** ✅ Выполнено — 77 тестов passed, 0 failed

---

## 1. Что изменено и почему

### Вариант A — AbstractTreeService: общие методы подняты в базовый класс

**Проблема:** `createItem()` и `buildParentOptionsHtml()` были 100% идентичными копиями в `CategoryTreeService` и `MainMenuItemService`.

**Решение:** Оба метода перенесены в `AbstractTreeService`. Подклассы теперь содержат только `modelClass(): string`.

**Ключевые решения при реализации:**
- `createItem()` возвращает `Model` в PHP-сигнатуре, но PHPDoc аннотирован `@return TModel` — типизация сохранена на уровне документации без сложностей с PHP generics.
- `buildParentOptionsHtml()` использует `static fn ($n)` без явного типа модели в замыкании — поля `id`, `parent_id`, `title`, `sort_no` общие для всех tree-моделей.

**Файлы:**
- `app/Services/Admin/AbstractTreeService.php` — добавлены 2 метода (+57 строк)
- `app/Services/Admin/CategoryTreeService.php` — удалены дублированные методы (−50 строк → 12 строк)
- `app/Services/Admin/MainMenuItemService.php` — удалены дублированные методы (−50 строк → 12 строк)

---

### Вариант E — AbstractSaveTreeOrderRequest

**Проблема:** `SaveCategoryTreeOrderRequest` и `SaveMainMenuItemOrderRequest` были идентичны на 90% — `rules()`, `allIdsExist()`, `collectIds()` копировались.

**Решение:** Создан `AbstractSaveTreeOrderRequest` с двумя abstract-методами:
```php
abstract protected function modelClass(): string;
abstract protected function invalidIdsMessage(): string;
```

Каждый конкретный Request — 2 метода, ~15 строк.

**Ключевое решение:** `modelClass()` в abstract Request использует тот же паттерн что и `AbstractTreeService` — единый стиль во всём проекте.

**Файлы:**
- `app/Http/Requests/Admin/AbstractSaveTreeOrderRequest.php` — создан (+70 строк)
- `app/Http/Requests/Admin/CategoryTree/SaveCategoryTreeOrderRequest.php` — 75 → 20 строк
- `app/Http/Requests/Admin/MainMenu/SaveMainMenuItemOrderRequest.php` — 73 → 20 строк

---

### Вариант B — Trait HasTreeCrudActions

**Проблема:** Оба контроллера (106 строк каждый) были идентичны на ~95%. Различия только в именах переменных, view-путях, именах маршрутов.

**Ключевое архитектурное решение:**

Методы с **route model binding** (`edit`, `store`, `update`, `destroy`) и типизированными FormRequest-параметрами (`saveOrder`) **остаются в конкретных контроллерах** — это необходимо, потому что Laravel разрешает инъекцию модели/реквеста по конкретному типу в сигнатуре метода.

Трейт предоставляет:
- **Полностью generic public методы:** `index()`, `create()`
- **Protected helper-методы:** `executeSaveOrder()`, `handleEdit()`, `handleStore()`, `handleUpdate()`, `handleDestroy()`
- **11 abstract config-методов** — конфигурируют поведение без переопределения логики

```
Trait HasTreeCrudActions
│
├── PUBLIC (вызываются роутером напрямую)
│   ├── index(): View
│   └── create(): View
│
├── PROTECTED helpers (вызываются из конкретного контроллера)
│   ├── executeSaveOrder(AbstractSaveTreeOrderRequest): JsonResponse
│   ├── handleEdit(Model): View
│   ├── handleStore(FormRequest): RedirectResponse
│   ├── handleUpdate(FormRequest, Model): RedirectResponse
│   └── handleDestroy(Request, Model): JsonResponse|RedirectResponse
│
└── ABSTRACT config (реализуются в контроллере)
    ├── getTreeService(): AbstractTreeService
    ├── indexView(): string
    ├── createView(): string
    ├── editView(): string
    ├── editModelKey(): string       ← ключ модели в edit-view ($categoryTree/$mainMenuItem)
    ├── jsMetaKey(): string          ← ключ JS-метаданных (categoryTreeMetaForJs/mainMenuMetaForJs)
    ├── saveOrderRouteName(): string
    ├── indexRouteName(): string
    ├── storeSuccessMessage(): string
    ├── updateSuccessMessage(): string
    └── destroySuccessMessage(): string
```

**Почему `editModelKey()` необходим:**

Blade-шаблоны `category-tree/edit.blade.php` и `main-menu/edit.blade.php` используют переменные `$categoryTree` и `$mainMenuItem` соответственно. Шаблоны не меняются — ключ передаётся динамически:
```php
return view($this->editView(), [
    $this->editModelKey() => $node,
    'parentOptionsHtml' => ...,
]);
```

**Почему `saveOrder()` не полностью в трейте:**

Laravel резолвит FormRequest по типу в сигнатуре метода. Трейт объявляет `executeSaveOrder(AbstractSaveTreeOrderRequest)` — защищённый хелпер. Каждый контроллер определяет публичный `saveOrder(ConcreteRequest)` → `$this->executeSaveOrder($request)`.

**Файлы:**
- `app/Http/Controllers/Admin/Traits/HasTreeCrudActions.php` — создан (+115 строк)
- `app/Http/Controllers/Admin/CategoryTreeController.php` — 106 → 106 строк*
- `app/Http/Controllers/Admin/MainMenuItemController.php` — 106 → 106 строк*

> *Размер схожий, но структура кардинально другая: 11 config-методов + 4 делегирующих метода вместо 7 методов с дублированной логикой.

---

## 2. Как добавить новый Tree-модуль (например, Accordion)

Минимальный набор для нового модуля:

### 2.1 Модель

```php
// app/Models/AccordionItem.php
class AccordionItem extends Model
{
    use HasFactory;
    protected $fillable = ['parent_id', 'sort_no', 'title', 'content', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
    public function parent(): BelongsTo { ... }
    public function children(): HasMany { ... }
    public function scopeOrdered(Builder $query): Builder { ... }
}
```

### 2.2 Сервис

```php
// app/Services/Admin/AccordionItemService.php
class AccordionItemService extends AbstractTreeService
{
    protected function modelClass(): string
    {
        return AccordionItem::class;
    }
}
```

### 2.3 FormRequest (SaveOrder)

```php
// app/Http/Requests/Admin/Accordion/SaveAccordionItemOrderRequest.php
class SaveAccordionItemOrderRequest extends AbstractSaveTreeOrderRequest
{
    protected function modelClass(): string { return AccordionItem::class; }
    protected function invalidIdsMessage(): string { return __('One or more accordion IDs are invalid.'); }
}
```

### 2.4 Контроллер

```php
// app/Http/Controllers/Admin/AccordionItemController.php
class AccordionItemController extends Controller
{
    use HasTreeCrudActions;

    public function __construct(private readonly AccordionItemService $service) {}

    protected function getTreeService(): AbstractTreeService { return $this->service; }
    protected function indexView(): string { return 'admin.pages.accordion.index'; }
    protected function createView(): string { return 'admin.pages.accordion.create'; }
    protected function editView(): string { return 'admin.pages.accordion.edit'; }
    protected function editModelKey(): string { return 'accordionItem'; }
    protected function jsMetaKey(): string { return 'accordionMetaForJs'; }
    protected function saveOrderRouteName(): string { return 'admin.accordion.save-order'; }
    protected function indexRouteName(): string { return 'admin.accordion.index'; }
    protected function storeSuccessMessage(): string { return __('Accordion item created.'); }
    protected function updateSuccessMessage(): string { return __('Accordion item updated.'); }
    protected function destroySuccessMessage(): string { return __('Accordion item deleted.'); }

    public function edit(AccordionItem $accordionItem): View
    {
        return $this->handleEdit($accordionItem);
    }

    public function store(StoreAccordionItemRequest $request): RedirectResponse
    {
        return $this->handleStore($request);
    }

    public function saveOrder(SaveAccordionItemOrderRequest $request): JsonResponse
    {
        return $this->executeSaveOrder($request);
    }

    public function update(UpdateAccordionItemRequest $request, AccordionItem $accordionItem): RedirectResponse
    {
        return $this->handleUpdate($request, $accordionItem);
    }

    public function destroy(Request $request, AccordionItem $accordionItem): JsonResponse|RedirectResponse
    {
        return $this->handleDestroy($request, $accordionItem);
    }
}
```

Плюс Store/UpdateRequest с полями специфичными для модуля — они не унифицированы намеренно, так как каждый модуль имеет уникальную валидацию (например `content`, cycle-detection, slug-uniqueness).

---

## 3. Что НЕ изменилось

| Компонент | Причина |
|-----------|---------|
| Модели (`CategoryTree`, `MainMenuItem`) | Верная структура, нет дублирования |
| Store/Update FormRequests | Уникальная валидация для каждого модуля |
| Blade-шаблоны | Не затронуты — ключи переменных сохранены |
| Роуты | Не затронуты |
| Миграции | Не затронуты |

---

## 4. Зафиксированные проблемы в тестах (до рефакторинга)

В ходе работы обнаружены и исправлены **3 сломанных теста**, существовавших до начала рефакторинга:

| Тест | Проблема | Исправление |
|------|----------|-------------|
| `CategoryTreeAdminTest::test_category_tree_index_renders_for_authenticated_admin` | `assertViewHas('nodesMeta')` — устаревшее имя переменной | → `assertViewHas('categoryTreeMetaForJs')` |
| `MainMenuItemAdminTest::test_main_menu_index_renders_for_authenticated_admin` | `assertViewHas('nodesMeta')` — устаревшее имя | → `assertViewHas('mainMenuMetaForJs')` |
| `CategoryTreeAdminTest::test_update_returns_json_with_message` | `putJson` + `assertOk` — update вернули на `RedirectResponse` при переходе на page-based CRUD (2026-05-02) | → тест переименован в `test_update_redirects_to_index_with_success`, использует `put()` + `assertRedirect` |
| `MainMenuItemAdminTest::test_update_returns_json_with_message` | То же | Аналогичное исправление |
| `MainMenuItemServiceTest::test_build_parent_options_html_includes_root_option` | `assertStringContainsString('<option value="0">')` — не учитывал атрибут `selected` | → `assertStringContainsString('value="0"')` |

---

## 5. Итоговый результат

```
Tests: 77 passed (242 assertions)
Duration: ~2.75s
```

Редукция дублирования:
- Сервисы: −100 строк дублированного кода
- Request-классы: −116 строк дублированного кода  
- Контроллеры: логика вынесена в трейт, контроллеры = конфигурация
