# Technology Stack

## Purpose
This document describes the technology stack used by the Laravel reference project and the stable backend assumptions that should remain consistent across projects.

## Backend Stack
### Laravel
- Core application framework.
- Provides routing, middleware, Eloquent ORM, validation, events, queues, Blade, and API resources.

### PHP
- Main application language.
- The reference project targets modern PHP with typed methods, constructor injection, and Laravel conventions.

### MySQL
- Primary relational database.
- Stores normalized entity data, flags, sorting fields, and parent-child relations for tree structures.

## Frontend Stack In The Reference Project
### Blade
- Primary rendering layer for both shop and admin pages.
- Defines layouts, partials, forms, tables, and page composition.

### Vue.js
- Used for interactive components rather than full SPA routing.
- Mounted into Blade-rendered pages as progressive enhancement.

### Bootstrap
- Main UI framework in the reference architecture.
- The admin UI is Bootstrap-first and form-heavy.

## Frontend Libraries In Use
The reference project also uses supporting frontend tools around the core stack:
- jQuery
- DataTables for admin lists
- Axios for requests
- Dropzone for uploads
- CKEditor for rich text
- `slick-carousel` for sliders in the shop frontend
- `vuedraggable` for drag-and-drop tree or ordering interfaces in admin
- `select2` for richer select inputs
- `toastr` for UI notifications

These tools support the same Blade-first architecture rather than replacing it.

## Translation Libraries
The reference project uses several translation-related packages and patterns:
- `mcamara/laravel-localization` for locale prefixes, translated routes, and localized URL generation
- `spatie/laravel-translatable` for multilingual model attributes such as `title`, `slug`, and `description`
- `barryvdh/laravel-translation-manager` for managing translation strings
- `tanmuhittin/laravel-google-translate` for automated translation support in translation workflows

Common translation usage patterns:
- localized route prefixes via `LaravelLocalization::setLocale()`
- translated route segments via `LaravelLocalization::transRoute(...)`
- translated model fields stored as translatable attributes
- Blade/UI translations via `trans()` and `__()`

## Logging And Monitoring Libraries
The reference project uses:
- `bugsnag/bugsnag-laravel` for application error monitoring
- `opcodesio/log-viewer` for viewing Laravel logs through a UI
- Laravel's built-in logging stack for standard application logging

Related UI feedback tools also appear in the frontend:
- `toastr` for user-facing success and error notifications
- flash message partials in Blade for request-cycle feedback

These tools support the same Blade-first architecture rather than replacing it.

## Optional Stacks In Other Projects
Some projects may use alternative frontend stacks while keeping the same Laravel backend structure:
- TailwindCSS
- Inertia.js
- Blade only

## Stable Rule
Frontend technologies may change, but the backend architecture should stay consistent:
- controllers remain thin
- validation stays in `FormRequest` classes
- business rules stay in services and models
- JSON output uses resources when appropriate
- CRUD modules keep the same structural boundaries

## AI Guidance
When generating code:
- match the frontend stack already present in the project
- do not force a SPA approach into a Blade-first codebase
- preserve Laravel backend conventions even if the UI layer differs
