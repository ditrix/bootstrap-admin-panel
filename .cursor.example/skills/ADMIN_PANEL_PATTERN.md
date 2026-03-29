# Admin Panel Pattern

## Purpose
This document defines the preferred architecture for admin-panel modules based on the reference Laravel project.

## Admin Panel Architecture
Admin functionality is organized as modular CRUD sections with matching routes, controllers, requests, views, and API endpoints.

### Example Module
`Admin/Product`

### Common Components
- `ProductController`
- `ProductService`
- `StoreProductRequest`
- `UpdateProductRequest`
- `Product` model
- Blade views

### Responsibilities
- controller: orchestrates request handling and responses
- service: contains reusable business rules and non-trivial workflows
- requests: validate create and update payloads
- model: owns persistence, relations, scopes, and accessors
- views: render list pages, forms, and reusable partials

## Standard Module Structure
The reference project commonly organizes admin views like this:
- `pages/<module>/index.blade.php`
- `pages/<module>/create.blade.php`
- `pages/<module>/show.blade.php`
- `pages/<module>/form/*.blade.php`
- `pages/<module>/data-table/table.blade.php`

This structure should be reused when adding new admin modules.

## Admin Tables
### Required Features
- pagination
- sorting
- search
- filters
- bulk actions when the domain needs them

### UI Stack
- Blade + Bootstrap
- jQuery DataTables for server-fed admin lists
- Vue components only when the table needs richer interaction

### Pattern
- `index` renders the page shell
- the table partial initializes the JS table
- a dedicated endpoint returns JSON for rows and actions

## Admin Forms
### Supported Controls
- text inputs
- select fields
- checkboxes
- file uploads
- rich text editors

### Validation
All admin form validation must use `FormRequest` classes.

### Form Rules
- keep forms Blade-first
- extract repeated form blocks into partials
- show clear validation feedback
- support multilingual tabs when the entity uses translated fields

## Tree Structures
Hierarchical data in the admin panel commonly includes:
- categories
- documents
- menus

### Requirements
- drag-and-drop sorting
- parent-child relationships
- explicit `position` or equivalent ordering field

### Frontend Libraries
- SortableJS
- VueDraggable

### Backend Rules
- expose reorder endpoints
- validate parent assignment
- keep recursive transformation logic in services or models, not controllers

## File Uploads
Uploads should use Laravel storage through shared admin components or services.

Recommended behavior:
- validate file input in requests
- store files through Laravel abstractions
- reuse upload partials/components
- support ordering for multiple uploads when the module needs galleries

## Rich Text Editors
Possible editors include:
- TinyMCE
- CKEditor

Content should be stored as HTML when the module is meant to render rich formatted text.

## Permissions And Authorization
Admin access should be enforced consistently through:
- policies
- gates
- middleware-based permission checks

AI should follow the authorization strategy already used by the project instead of mixing multiple styles in one module.

## Admin UX Guidelines
- keep forms simple and task-focused
- avoid heavy frontend logic unless the UI clearly needs it
- use pagination for large datasets
- show clear validation errors and flash messages
- favor predictable CRUD screens over custom one-off flows

## AI Development Rules
When generating admin functionality:
- always follow the Table CRUD pattern
- use `FormRequest` validation
- keep controllers thin
- use services for business logic when it is more than a simple model update
- use Blade templates as the default rendering layer
- use Bootstrap-compatible markup in a Bootstrap-based admin
- add pagination and search to table-based modules
- keep Vue limited to interactive islands, not full admin SPA rewrites
- avoid using Vue for simple forms that can be handled by Blade, and use it only for Tree CRUD and complex dynamic UI
