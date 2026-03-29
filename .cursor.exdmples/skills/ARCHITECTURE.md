# Application Architecture

## Purpose
This document describes the Laravel application architecture used by the reference project in `reference/laravel-10`. It is intended to help AI assistants generate code that fits the same structure and responsibilities.

## Project Structure
### app/
- `Http/Controllers` - entry points for web and API requests
- `Http/Requests` - validation classes per action
- `Http/Resources` - JSON response transformers
- `Models` - Eloquent domain models with relationships and helpers
- `Services` - reusable business logic
- `Helpers` - small shared utility classes
- `Policies` - authorization rules when policies are used
- `Observers` - model lifecycle hooks
- `Listeners` - event-driven side effects
- `Rules` - custom validation rules
- `View/Composers` - shared data for Blade layouts and pages

### resources/
- `resources/admin/views` - admin Blade pages and reusable partials
- `resources/shop/views` - public storefront Blade templates
- `resources/admin/assets/js` - admin JavaScript
- `resources/shop/assets/js` - shop JavaScript and Vue bootstrapping

### routes/
- `web.php` - shop web routes
- `api.php` - shop/public API routes
- `admin-web.php` - admin web routes
- `admin-api.php` - admin AJAX/API routes

## Route Architecture
The reference project describes routes declaratively through grouped configuration:
- route files are split by context
- `Route::group()` defines prefixes, middleware, and naming
- `Route::controller()` maps a controller once and then lists action methods
- `Route::resource()` is used where standard CRUD fits cleanly
- route names are organized by module, for example `admin.category.show` or `api.cart.update.shipping-method`

### Common Route Rules
- use route groups for context boundaries such as admin/shop and web/api
- keep route names explicit and module-based
- apply middleware at the highest useful group level
- use prefixes and `as` keys declaratively instead of repeating full names on every route
- prefer route-model binding for typed controller signatures

### Shop Web Example
```php
Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localizationRedirect', 'localeViewPath'],
], function () {
    Route::controller(PageController::class)->group(function () {
        Route::group(['as' => 'page.'], function () {
            Route::get(LaravelLocalization::transRoute('shop-urls.about'), 'about')->name('about');
            Route::get(LaravelLocalization::transRoute('shop-urls.contact'), 'contact')->name('contact');
        });
    });
});
```

### Shop API Example
```php
Route::group([
    'as' => 'api.',
    'prefix' => 'api',
], function () {
    Route::group([
        'as' => 'categories.',
        'prefix' => 'categories',
    ], function () {
        Route::controller(CategoryController::class)->group(function () {
            Route::get('', 'tree')->name('tree');
        });
    });
});
```

### Admin Web Example
```php
Route::group([
    'as' => 'admin.',
    'prefix' => 'adm',
], function () {
    Route::group([
        'middleware' => ['auth:admin', 'admin.has_permission'],
    ], function () {
        Route::controller(CategoryController::class)->group(function () {
            Route::group(['as' => 'category.', 'prefix' => 'categories'], function () {
                Route::get('', 'index')->name('index');
                Route::get('{category}', 'show')->name('show');
                Route::put('{category}', 'update')->name('update');
            });
        });
    });
});
```

## Layer Responsibilities
### Controller Layer
- Accepts the request after middleware and route binding.
- Coordinates validation, service calls, and response creation.
- Returns either Blade views or JSON.

### Service Layer
- Encapsulates domain workflows and reusable business operations.
- Commonly handles tree building, data transformation, uploads, and domain-specific processing.

### Model Layer
- Owns relationships, scopes, accessors, casts, translatable fields, and entity-specific helpers.
- Represents the main domain behavior around persistence.

### Helper Functions
- Provide small reusable utility operations.
- Support but do not replace domain services.

### Traits
- Package or internal traits extend shared behavior on models or supporting classes.

### Resources
- Convert models and collections into consistent JSON payloads.

### Observers
- Attach side effects to model lifecycle events close to the model domain.

### Listeners
- React to framework or domain events for secondary behavior.

### Validation Rules
- Package reusable custom validation logic for requests.

## Request Lifecycle
The typical request lifecycle is:

`Request -> Middleware -> FormRequest -> Controller -> Service/Model -> Blade or Resource -> Response`

In more detail:
1. Middleware resolves auth, permissions, and request context.
2. Routes map the request into shop/admin and web/api areas.
3. Route-model binding injects typed models where possible.
4. `FormRequest` validates and normalizes input.
5. Controller delegates business work to a service or performs simple model operations.
6. Models persist data and may trigger observers.
7. Events may dispatch listeners for secondary side effects.
8. The response is rendered as Blade HTML or JSON.

## Context Separation
The reference project uses a clear separation across two axes:
- audience: `Shop` and `Admin`
- transport: `Web` and `Api`

This produces namespaces such as:
- `App\Http\Controllers\Shop\Web\...`
- `App\Http\Controllers\Shop\Api\...`
- `App\Http\Controllers\Admin\Web\...`
- `App\Http\Controllers\Admin\Api\...`

The same split appears in requests and resources, and AI-generated code should respect it.

## Events And Observers
Observers handle model-adjacent behavior, such as setting defaults or syncing related data during save operations.

Listeners handle broader reactions after events occur. Use them for notifications, logging, asynchronous side effects, or cross-module integration that should not live in controllers.

## Blade And Vue Integration
The frontend is Blade-first. Blade layouts render the page shell and pass initial data into Vue components through props or inline JSON.

Vue is used as progressive enhancement:
- Blade renders the page
- Vue mounts inside a root container such as `#page`
- individual components power interactive sections

This is not a full SPA architecture. AI should prefer server-rendered Blade pages unless the existing module already relies on Vue for richer interactions.

## Architecture Rules For AI
- Reuse the existing `Admin/Shop` and `Web/Api` split.
- Keep controllers as orchestration layers.
- Introduce services for reusable or non-trivial business logic.
- Keep serialization in resources and validation in requests.
- Use observers and listeners for side effects instead of inflating controller actions.
- Follow the same modular structure in routes, controllers, requests, views, and JS assets.
- Describe routes declaratively with groups, prefixes, names, middleware, and controller grouping.
