# AI Development Skills

## Introduction
This document defines the development standards used in the Laravel reference architecture found in `reference/laravel-10`.

AI coding assistants must follow these standards when generating code for projects that use this architecture. The goal is consistency with the existing Laravel structure, naming, request flow, and admin-panel implementation style.

## Architecture Layers
### Controllers
- Act as thin entry points.
- Receive typed route-model bindings and `FormRequest` objects.
- Return Blade views for web/admin pages or JSON responses for API endpoints.
- Delegate validation, formatting, and business rules to dedicated layers.

### FormRequests
- Own input validation.
- Usually return `true` from `authorize()` when access is already enforced by middleware.
- Commonly validate multilingual fields as arrays such as `title.*`, `slug.*`, and `description.*`.

### Services
- Hold reusable business logic that does not belong in controllers.
- Often encapsulate tree building, file handling, transformation, or domain-specific workflows.
- Are commonly injected through constructor property promotion.

### Models
- Are rich Eloquent models, not passive records.
- Define relationships, scopes, accessors, casts, translatable attributes, and domain helpers.
- May integrate third-party traits for caching, translations, soft deletes, or attachments.

### Traits
- Are used sparingly for targeted reuse.
- Should only contain behavior that is shared by multiple classes and fits naturally as a trait.

### Helpers
- Small reusable utility classes with static methods.
- Used for cross-cutting concerns such as cache keys, routing helpers, strings, images, or permissions.
- Should not become a replacement for domain services.

### Policies
- Laravel policies are supported, but the reference project relies more on middleware and permission checks than on a full policy-first design.
- If a project already uses policies, AI should continue that approach consistently.

### API Resources
- Normalize API output.
- Keep serialization logic out of controllers and models.
- Are preferred for structured JSON responses.

### Observers
- React to model lifecycle events such as `creating`, `created`, `updating`, or `deleted`.
- Are suitable for model-adjacent side effects and defaults.

### Event Listeners
- Handle post-action side effects outside the request core.
- Are useful for mail, logging, notifications, and background-oriented reactions.

### Validation Rules
- Encapsulate custom validation logic.
- Are used from `FormRequest` classes together with built-in Laravel rules.

## Layer Interaction
The default request flow is:

`Request -> FormRequest -> Controller -> Service/Model -> Resource or View -> Response`

Supporting layers plug into this flow:
- middleware performs authentication, permission checks, and context resolution
- observers react during model persistence
- listeners react after events are dispatched
- helpers support small reusable operations

## Coding Conventions
### Naming
- Classes use explicit domain names such as `PageController`, `CategoryService`, `UpdateOrCreatePageRequest`, and `CategoryResource`.
- Methods are verb-based and descriptive: `index`, `show`, `store`, `update`, `destroy`, `getCategoryTreeAsArray`.
- Variables use clear snake_case for database columns and readable camelCase in PHP code.

### Database Columns
- Use snake_case: `parent_id`, `is_active`, `feature_position`, `meta_description`.
- Boolean flags commonly start with `is_`.
- Position and hierarchy fields are explicit, for example `parent_id` and `position` or project-specific ordering fields.

### Type Hints And Return Types
- Constructor injection is preferred.
- Controller methods should declare concrete return types such as `View`, `RedirectResponse`, or `JsonResponse`.
- Relationship methods should declare Laravel relation types such as `BelongsTo`, `HasMany`, and `BelongsToMany`.
- Service methods should use explicit parameter and return types whenever possible.

### PHPDoc (source documentation)
- Every class under `app/` MUST have a **class-level PHPDoc block** above the declaration: one or two sentences on purpose (layer, domain entity, or HTTP responsibility). Use **English** for PHPDoc text to align with Laravel core and IDE tooling.
- Document **public and protected** methods when the contract is non-obvious: `@param`, `@return`, and `@throws` where applicable. Prefer **array shapes** for structured arrays (e.g. `@return array{total: int, rows: Collection<int, Model>}`, `@param array<int, array{id: int, children?: array<mixed>}> $nodes`).
- **Model** `$fillable`, `$casts`, `$hidden`, and similar MUST keep accurate `@var` lists. Add `@property` / `@property-read` on the class docblock only when the team relies on them for IDE support.
- **Eloquent** scopes and custom query methods: document `Builder<Model>` (or concrete model) in `@param` / `@return` when it helps static analysis.
- **Form requests**: class-level PHPDoc describing which action or resource is validated; no need to repeat every rule in prose if `rules()` is self-explanatory.
- **Invokable controllers** (`__invoke`): describe the JSON/view contract briefly in the class docblock or above the method.
- Do **not** restate the obvious in PHPDoc when the signature and name are fully clear; the class description and types are enough.
- When touching a file, bring its PHPDoc in line with this section (do not add redundant inline `//` comments for the same thing—prefer PHPDoc per project rules).

## CRUD Overview
The architecture uses two recurring CRUD styles.

### Table CRUD
- Standard admin CRUD for flat entities.
- Usually includes list, create, show/edit, update, and delete actions.
- **В этом репозитории** списки реализуются через Blade + **bootstrap-table** (server-side): общий виджет `admin.partials.bootstrap-table-widget`, invokable `*TableDataController`, `*ListingService::paginateForBootstrapTable()`, при необходимости `*Resource`. Источник правды по паттерну: **[ADMIN_PANEL_PATTERN.md](ADMIN_PANEL_PATTERN.md)** и `docs/admin-bootstrap-table.md`. DataTables не использовать для новых админских таблиц, если нет явного исключения.

### Tree CRUD
- Used for hierarchical entities such as categories, documents, and menus.
- Centers on `parent_id`, ordering, recursive loading, and drag-and-drop reordering in the admin UI.

## Frontend Stack
The reference project primarily uses:
- Blade templates
- Vue components
- Bootstrap

In practice, Blade is the server-rendered foundation and Vue is added where the UI needs richer interaction. Other projects built on the same backend architecture may use:
- Blade + Tailwind
- Inertia + Vue
- Blade only

The backend architecture should remain consistent even if the frontend stack changes.

## Laravel Best Practices
- Use `FormRequest` classes for validation.
- Keep controllers thin.
- Move business logic into services when it is reused or non-trivial.
- Use helpers only for small reusable utilities.
- Avoid N+1 queries with eager loading.
- Use authorization consistently through policies, gates, or middleware.
- Prefer readable, maintainable code over clever abstractions.
- Keep response formatting in resources or views, not in controllers.

## AI Development Rules
When generating code, AI assistants must:
- follow Laravel conventions and the existing project structure
- keep controllers thin and focused on orchestration
- use `FormRequest` validation instead of inline validation
- place business logic in services when it goes beyond simple model updates
- use API resources for API responses
- use eager loading to avoid N+1 queries
- follow the established Table CRUD and Tree CRUD patterns
- preserve naming and namespace structure already used in the project
- prefer extending existing modules over introducing new architectural layers
- document new and edited classes/methods according to the **PHPDoc (source documentation)** subsection above (English summaries, accurate `@param`/`@return`/`@throws` and array shapes when non-obvious)
- if Laravel Boost is installed in the project, always use its available capabilities and tools where applicable before falling back to generic approaches

