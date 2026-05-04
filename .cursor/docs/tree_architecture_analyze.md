# Анализ архитектуры Tree-модулей

> **Область:** `CategoryTree` и `MainMenu` — два древовидных CRUD-модуля в `bootstrap-admin-panel`.  
> **Дата:** 2026-05-03  
> **Принципы оценки:** SOLID · DRY · KISS

---

## 1. Текущая архитектура

### 1.1 Компонентная карта

```
┌─────────────────────────────────────────────────────────────────────┐
│  HTTP Layer                                                         │
│                                                                     │
│  CategoryTreeController          MainMenuItemController             │
│  ┌──────────────────┐            ┌──────────────────┐              │
│  │ index()          │            │ index()          │              │
│  │ create()         │            │ create()         │              │
│  │ edit()           │            │ edit()           │              │
│  │ store()          │            │ store()          │              │
│  │ saveOrder()      │            │ saveOrder()      │              │
│  │ update()         │            │ update()         │              │
│  │ destroy()        │            │ destroy()        │              │
│  └────────┬─────────┘            └────────┬─────────┘              │
│           │                               │                         │
│  FormRequests (6 классов)                                           │
│  CategoryTree/Store·Update·SaveOrder      MainMenu/Store·Update·SaveOrder │
└───────────┬───────────────────────────────┬─────────────────────────┘
            │                               │
┌───────────▼───────────────────────────────▼─────────────────────────┐
│  Service Layer                                                       │
│                                                                     │
│         ┌──────────────────────────────────────────┐               │
│         │         AbstractTreeService<TModel>       │               │
│         │  + buildGroupedTree(): Collection         │               │
│         │  + allNodesOrderedForMeta(): Collection   │               │
│         │  + saveOrder(array): void                 │               │
│         │  + deleteNodeReparentingChildren(Model)   │               │
│         │  # modelClass(): string  [abstract]       │               │
│         └─────────────┬────────────────┬────────────┘               │
│                       │                │                            │
│         ┌─────────────▼──┐   ┌─────────▼────────────┐              │
│         │CategoryTreeSvc │   │ MainMenuItemSvc       │              │
│         │ + createItem() │   │ + createItem()        │              │
│         │ + buildOptions │   │ + buildOptions...()   │              │
│         └────────────────┘   └──────────────────────┘              │
└─────────────────────────────────────────────────────────────────────┘
            │                               │
┌───────────▼───────────────────────────────▼─────────────────────────┐
│  Model Layer                                                         │
│  CategoryTree  ─────────────────────────── MainMenuItem             │
│  (parent/children, scopeOrdered)           (parent/children,        │
│                                             scopeOrdered,           │
│                                             getDepthFromRoot)       │
└─────────────────────────────────────────────────────────────────────┘
```

### 1.2 Диаграмма классов (UML)

```
«abstract»
AbstractTreeService<TModel>
├── # modelClass(): string
├── + buildGroupedTree(): Collection
├── + allNodesOrderedForMeta(): EloquentCollection
├── + saveOrder(array, int): void
├── + deleteNodeReparentingChildren(Model): void
└── - applyTreeOrder(array, int): void
        ▲                       ▲
        │                       │
CategoryTreeService      MainMenuItemService
├── # modelClass()       ├── # modelClass()
├── + createItem()       ├── + createItem()           ← 100% дубль
└── + buildParentOptionsHtml()  └── + buildParentOptionsHtml() ← 100% дубль
```

---

## 2. Оценка текущего решения

### 2.1 Сильные стороны ✅

| Принцип | Что реализовано хорошо |
|---------|------------------------|
| **SRP** | Контроллер — только HTTP-адаптер; бизнес-логика в сервисе |
| **DRY (частично)** | `AbstractTreeService` убирает дублирование структурных операций |
| **OCP** | Новый модуль дерева добавляется наследованием без правки базы |
| **Dependency Injection** | Сервис внедряется через конструктор (`readonly`) |
| **FormRequest** | Валидация вынесена из контроллера |
| **DB transaction** | `saveOrder` и `deleteNodeReparentingChildren` атомарны |

### 2.2 Проблемы ⚠️

#### P-1. Контроллеры: ~95% дублирования кода

`CategoryTreeController` и `MainMenuItemController` различаются **только:**
- именами переменных (`$categoryTree` / `$mainMenuItem`)
- путями представлений (`category-tree/*` / `main-menu/*`)
- именами маршрутов (`admin.category-tree.*` / `admin.main-menu.*`)
- именем JS-переменной (`categoryTreeMetaForJs` / `mainMenuMetaForJs`)
- текстом flash-сообщений

Вся логика методов — идентична. Пример — `destroy()`:

```php
// CategoryTreeController::destroy()            // MainMenuItemController::destroy()
public function destroy(                         public function destroy(
    Request $request,                                Request $request,
    CategoryTree $categoryTree                       MainMenuItem $mainMenuItem
): JsonResponse|RedirectResponse {              ): JsonResponse|RedirectResponse {
    $this->service->deleteNode...($categoryTree);    $this->service->deleteNode...($mainMenuItem);
    $message = __('Category tree node deleted.');    $message = __('Menu item deleted.');
    if ($request->wantsJson()) {                     if ($request->wantsJson()) {
        return response()->json(...);                    return response()->json(...);
    }                                                }
    return redirect()                                return redirect()
        ->route('admin.category-tree.index')             ->route('admin.main-menu.index')
        ->with('success', $message);                     ->with('success', $message);
}                                               }
```

#### P-2. Сервисы: `createItem()` и `buildParentOptionsHtml()` — точные копии

Оба метода в `CategoryTreeService` и `MainMenuItemService` идентичны построчно. Единственное отличие — тип в PHPDoc-аннотации.

```
CategoryTreeService::createItem()         == MainMenuItemService::createItem()
CategoryTreeService::buildParentOptionsHtml() == MainMenuItemService::buildParentOptionsHtml()
```

#### P-3. FormRequest `SaveOrder` — дубль на 90%

`SaveCategoryTreeOrderRequest` и `SaveMainMenuItemOrderRequest` идентичны, кроме:
- используемого класса модели (`CategoryTree` vs `MainMenuItem`)
- текста ошибки

#### P-4. Генерация HTML в сервисе (нарушение SRP)

`buildParentOptionsHtml()` — метод, генерирующий HTML-строку с `<option>` — живёт в Service-слое. Это смешение представления и бизнес-логики.

#### P-5. Жёсткая связь запросов и моделей в `SaveOrder`

`SaveCategoryTreeOrderRequest` напрямую обращается к `CategoryTree::query()`. При смене модели нужно менять Request — нарушение OCP.

---

## 3. Варианты улучшения

---

### Вариант A — Поднять `createItem()` и `buildParentOptionsHtml()` в `AbstractTreeService`

**Суть:** Переместить дублирующиеся методы в базовый класс. Обобщить `buildParentOptionsHtml` так, чтобы он работал через `modelClass()`.

```
AbstractTreeService<TModel>
├── + createItem(array): TModel          ← поднято
├── + buildParentOptionsHtml(...): string ← поднято
└── ... (существующие методы)

CategoryTreeService      MainMenuItemService
└── # modelClass()       └── # modelClass()
     (только это)              (только это)
```

**Диаграмма:**
```
«abstract»                         
AbstractTreeService<TModel>        
├── # modelClass(): string         
├── + buildGroupedTree()           
├── + allNodesOrderedForMeta()     
├── + saveOrder()                  
├── + deleteNodeReparentingChildren()
├── + createItem(array): TModel    ← NEW
└── + buildParentOptionsHtml()     ← NEW
        ▲               ▲
CategoryTreeSvc    MainMenuItemSvc
(только modelClass)  (только modelClass)
```

**Pros:**
- Минимальное изменение — 2 метода переезжают вверх
- Идеальный DRY для сервисов
- KISS: меньше файлов, меньше кода
- Обратно совместимо

**Cons:**
- `buildParentOptionsHtml()` генерирует HTML в сервисе — нарушение SRP сохраняется
- Не решает дублирование контроллеров (P-1)
- Не решает дублирование Request-классов (P-3)

**Оценка:** ★★★☆☆ — быстрая победа над P-2, остальные проблемы остаются

---

### Вариант B — Трейт для контроллеров `HasTreeCrudActions`

**Суть:** Вынести общую логику контроллера в трейт. Конкретные контроллеры предоставляют конфигурацию через абстрактные методы.

```php
trait HasTreeCrudActions
{
    abstract protected function indexViewPath(): string;
    abstract protected function jsMetaKey(): string;
    abstract protected function saveOrderRoute(): string;
    abstract protected function indexRoute(): string;

    public function index(): View { /* общая логика */ }
    public function create(): View { /* общая логика */ }
    public function destroy(Request $request, Model $node): JsonResponse|RedirectResponse { ... }
    // ...
}

class CategoryTreeController extends Controller
{
    use HasTreeCrudActions;

    protected function indexViewPath(): string { return 'admin.pages.category-tree.index'; }
    protected function jsMetaKey(): string { return 'categoryTreeMetaForJs'; }
    // ...
}
```

**Диаграмма:**
```
«trait»
HasTreeCrudActions
├── index(): View
├── create(): View
├── edit(): View
├── store(): RedirectResponse
├── saveOrder(): JsonResponse
├── update(): RedirectResponse
├── destroy(): JsonResponse|RedirectResponse
├── «abstract» indexViewPath(): string
├── «abstract» jsMetaKey(): string
├── «abstract» saveOrderRoute(): string
└── «abstract» indexRoute(): string
        ↑               ↑
CategoryTreeController  MainMenuItemController
(config only)           (config only)
```

**Pros:**
- Контроллеры становятся «конфигурацией», а не кодом
- Трейты — идиоматичны для Laravel
- Легко переопределить отдельный метод при необходимости
- Не ломает существующую структуру классов

**Cons:**
- Трейты снижают читаемость — метод не видно «в контроллере»
- IDE-навигация усложняется
- Если логика расходится у двух модулей — трейт начинает «ветвиться» флагами
- Не решает проблему P-4 (HTML в сервисе)

**Оценка:** ★★★★☆ — убирает P-1 и P-2 (в связке с A), сохраняет Laravel-паттерны

---

### Вариант C — Абстрактный базовый контроллер

**Суть:** `AbstractTreeController` содержит всю логику; конкретные контроллеры реализуют конфигурацию через методы.

```php
abstract class AbstractTreeController extends Controller
{
    abstract protected function getService(): AbstractTreeService;
    abstract protected function viewPrefix(): string;      // 'admin.pages.category-tree'
    abstract protected function routePrefix(): string;     // 'admin.category-tree'
    abstract protected function jsMetaKey(): string;       // 'categoryTreeMetaForJs'
    abstract protected function modelClass(): string;

    public function index(): View { /* общая логика */ }
    public function store(FormRequest $request): RedirectResponse { ... }
    // ...
}

class CategoryTreeController extends AbstractTreeController
{
    public function __construct(private CategoryTreeService $service) {}
    protected function getService(): AbstractTreeService { return $this->service; }
    protected function viewPrefix(): string { return 'admin.pages.category-tree'; }
    // ...
}
```

**Диаграмма:**
```
Controller (Laravel)
    ▲
AbstractTreeController
├── + index(): View
├── + create(): View
├── + edit(): View
├── + store(): RedirectResponse
├── + saveOrder(): JsonResponse
├── + update(): RedirectResponse
├── + destroy(): JsonResponse|RedirectResponse
├── «abstract» getService(): AbstractTreeService
├── «abstract» viewPrefix(): string
├── «abstract» routePrefix(): string
└── «abstract» jsMetaKey(): string
        ▲                   ▲
CategoryTreeController  MainMenuItemController
```

**Pros:**
- Жёсткий контракт — невозможно забыть реализовать метод
- Полное устранение P-1 (дублирование контроллеров)
- Явное наследование — IDE показывает источник метода
- Легко тестировать через конкретный контроллер

**Cons:**
- Абстрактные контроллеры — нетипичный паттерн в Laravel (чаще используют трейты)
- Типизация `FormRequest` в сигнатурах методов усложняется (нужен базовый класс для Request)
- `store()` / `update()` принимают разные FormRequest — нужен общий базовый Request или дженерик
- Сложнее переопределить единственный метод

**Оценка:** ★★★☆☆ — мощно, но создаёт проблемы с типизацией FormRequest

---

### Вариант D — Интерфейс `TreeModuleConfig` + единый `TreeController`

**Суть:** Один контроллер получает конфигурацию через интерфейс. Каждый модуль — отдельный Config-класс.

```php
interface TreeModuleConfig
{
    public function service(): AbstractTreeService;
    public function viewPrefix(): string;
    public function routePrefix(): string;
    public function jsMetaKey(): string;
    public function storeRequest(): string; // FQCN FormRequest
    public function updateRequest(): string;
    public function saveOrderRequest(): string;
}

class TreeController extends Controller
{
    public function __construct(private TreeModuleConfig $config) {}

    public function index(): View { /* через $this->config */ }
    // ...
}

// routes/admin-web.php
Route::controller(TreeController::class)->group(function () {
    Route::get('category-tree', [TreeController::class, 'index'])
        ->defaults('_config', CategoryTreeModuleConfig::class);
});
```

**Диаграмма:**
```
«interface»
TreeModuleConfig
├── service(): AbstractTreeService
├── viewPrefix(): string
├── routePrefix(): string
├── jsMetaKey(): string
├── storeRequest(): string
└── updateRequest(): string
        ▲               ▲
CategoryTreeConfig  MainMenuConfig
        ↓               ↓
            TreeController
            ├── index()
            ├── create()
            ├── store()
            ├── ...
```

**Pros:**
- Один контроллер на все дерева
- Добавление нового модуля = один Config-класс
- Максимальное DRY и OCP
- Хорошо тестируется через мок `TreeModuleConfig`

**Cons:**
- **Высокая сложность:** IoC и route model binding для Config сложнее в Laravel 10
- **Нетипично для Laravel:** разработчики не ожидают такого паттерна
- Типизация FormRequest теряется — нельзя использовать route-model binding напрямую
- Значительное усложнение роутинга
- Нарушает KISS

**Оценка:** ★★☆☆☆ — интересно теоретически, но overengineered для 2 модулей

---

### Вариант E — Базовый класс для `SaveOrder` Request

**Суть:** Вынести общую логику валидации `SaveOrder` в абстрактный базовый Request.

```php
abstract class AbstractSaveTreeOrderRequest extends FormRequest
{
    abstract protected function modelClass(): string;
    abstract protected function invalidIdsMessage(): string;

    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nodes' => [
                'required', 'array',
                fn($attr, $value, $fail) => $this->allIdsExist($value) ?: $fail($this->invalidIdsMessage()),
            ],
        ];
    }

    private function allIdsExist(array $nodes): bool { /* общая логика */ }
    private function collectIds(array $nodes): array { /* общая логика */ }
}

class SaveCategoryTreeOrderRequest extends AbstractSaveTreeOrderRequest
{
    protected function modelClass(): string { return CategoryTree::class; }
    protected function invalidIdsMessage(): string { return __('One or more category tree IDs are invalid.'); }
}
```

**Диаграмма:**
```
FormRequest (Laravel)
    ▲
AbstractSaveTreeOrderRequest
├── + authorize(): bool
├── + rules(): array
├── - allIdsExist(array): bool
├── - collectIds(array): array
├── «abstract» modelClass(): string
└── «abstract» invalidIdsMessage(): string
        ▲                       ▲
SaveCategoryTreeOrderRequest  SaveMainMenuItemOrderRequest
```

**Pros:**
- Устраняет P-3 (дублирование Request)
- Минимальное изменение
- Легко добавить новый модуль
- OCP: расширяем без правки базы

**Cons:**
- Решает только P-3, не затрагивает другие проблемы

**Оценка:** ★★★★☆ — точечное решение P-3, отличное дополнение к другим вариантам

---

## 4. Сравнительная матрица

| | A: Abstract в сервис | B: Trait в контроллер | C: Abstract контроллер | D: Interface + 1 Controller | E: Abstract Request |
|---|:---:|:---:|:---:|:---:|:---:|
| Устраняет P-1 (контроллеры) | ✗ | ✓ | ✓ | ✓ | ✗ |
| Устраняет P-2 (сервисы) | ✓ | ✗ | ✗ | ✗ | ✗ |
| Устраняет P-3 (requests) | ✗ | ✗ | ✗ | ✗ | ✓ |
| SOLID | ★★★★ | ★★★☆ | ★★★★ | ★★★★ | ★★★★ |
| DRY | ★★★☆ | ★★★★ | ★★★★ | ★★★★★ | ★★★☆ |
| KISS | ★★★★★ | ★★★★ | ★★★☆ | ★★☆☆ | ★★★★★ |
| Laravel-идиоматичность | ★★★★★ | ★★★★★ | ★★★☆ | ★★☆☆ | ★★★★★ |
| Риск регрессий | низкий | средний | средний | высокий | низкий |
| Усилие реализации | малое | среднее | среднее | высокое | малое |

---

## 5. Рекомендованная стратегия

Для данного проекта (Laravel 10, 2 реализованных модуля, без явных планов на 3+) оптимальна **комбинация A + B + E**:

```
Шаг 1 (A): Поднять createItem() и buildParentOptionsHtml() в AbstractTreeService
           → устраняет P-2 полностью, риск низкий

Шаг 2 (E): Создать AbstractSaveTreeOrderRequest
           → устраняет P-3, 10 строк изменений

Шаг 3 (B): Создать трейт HasTreeCrudActions
           → устраняет P-1, контроллеры становятся "конфигурацией"
```

### Итоговая диаграмма рекомендованного решения

```
«abstract»
AbstractTreeService<TModel>
├── # modelClass(): string
├── + buildGroupedTree()
├── + allNodesOrderedForMeta()
├── + saveOrder()
├── + deleteNodeReparentingChildren()
├── + createItem(array): TModel          ← (было в подклассах)
└── + buildParentOptionsHtml()           ← (было в подклассах)
        ▲               ▲
CategoryTreeService  MainMenuItemService
(только modelClass)  (только modelClass)

──────────────────────────────────────────

«abstract»
AbstractSaveTreeOrderRequest
├── + authorize(): bool
├── + rules(): array
├── - allIdsExist(): bool
├── - collectIds(): array
├── «abstract» modelClass(): string
└── «abstract» invalidIdsMessage(): string
        ▲                   ▲
SaveCategoryTreeOrderRequest  SaveMainMenuItemOrderRequest

──────────────────────────────────────────

«trait»
HasTreeCrudActions
├── + index(): View
├── + create(): View
├── + edit(): View
├── + store(): RedirectResponse
├── + saveOrder(): JsonResponse
├── + update(): RedirectResponse
├── + destroy(): JsonResponse|RedirectResponse
└── «requires»:
    - $service: AbstractTreeService
    - indexViewPath(): string
    - createViewPath(): string
    - editViewPath(): string
    - routePrefix(): string
    - jsMetaKey(): string
    - storeSuccessMessage(): string
    - updateSuccessMessage(): string
    - destroySuccessMessage(): string

        ↑                           ↑
CategoryTreeController        MainMenuItemController
use HasTreeCrudActions;       use HasTreeCrudActions;
(только конфигурация)         (только конфигурация)
```

---

## 6. Отдельное замечание: `buildParentOptionsHtml` и SRP

Генерация HTML-строки в сервисе (`buildParentOptionsHtml`) нарушает SRP. В идеале это должен быть:
- Blade component / partial с `@foreach`
- Или отдельный `TreeOptionPresenter`/`ViewHelper`

Однако переработка представлений — более значительное изменение. При рефакторинге по шагам её стоит **отложить до Шага 4**, когда контроллеры и сервисы уже стабилизированы.

---

## 7. Что НЕ менять

- Структуру моделей (`CategoryTree`, `MainMenuItem`) — они правильно разделены
- FormRequest для `Store` и `Update` — имеют специфичную валидацию (slug uniqueness, cycle detection)
- Роутинг — именованные маршруты используются во вьюхах и тестах
