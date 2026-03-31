# AI Frontend & Theme Development Skills (Standard v1.1)

## 1. Introduction
This document defines the frontend development standards for projects using the **Multi-Theme Laravel Architecture**. AI agents (Architect, Coder, UI-specialist) MUST strictly follow these standards to ensure modularity, maintainability, and clean asset compilation across different project areas (e.g., Admin, Frontend).

## 2. Preferred Frontend Stack
* **Blade Templates:** Primary engine for SSR (Server-Side Rendering).
* **Modular SCSS:** Mandatory approach for all styling. Use SASS partials for logic separation.
* **Vue 3 Components:** Used only for complex, high-interactivity UI elements.
* **Build Tools:** Support for both **Vite** and **Webpack (Laravel Mix)** depending on the project's existing configuration (`vite.config.js` or `webpack.mix.js`).

## 3. Theme-Based Architecture
Assets are strictly isolated by theme. The AI must identify the current theme context before making changes.

### Directory Structure:
* **Admin Theme:** `resources/themes/admin/assets/css/`
* **Frontend Theme:** `resources/themes/frontend/assets/css/`

### Entry Point:
The main entry point for each theme is always **`app.scss`**. It serves as an orchestrator and should only contain `@import` directives.

## 4. SCSS "Block" Protocol
To ensure clean code and avoid CSS specificity wars, use the following partials system:

### 1. Naming Convention
* **Partials:** All block files must start with an underscore (e.g., `_nav.scss`, `_button.scss`).
* **Location:** Store component-specific styles in the `blocks/` subdirectory of the respective theme.

### 2. File Organization (The Contract)
* **`_variables.scss`:** Only SCSS variables (colors, fonts, sizes). **No CSS rules.**
* **`_mixins.scss`:** Only reusable logic/functions. **No CSS output.**
* **`blocks/_*.scss`:** Component-level logic (e.g., `_card.scss`, `_table.scss`). 
* **Rule:** NEVER hardcode hex colors or pixel values inside blocks. Always reference variables from `_variables.scss`.

### 3. Example `app.scss` Orchestration:
```scss
// 1. Configuration (No CSS output)
@import "variables";
@import "mixins";

// 2. Vendor/Base Integration
@import "bootstrap-integration";

// 3. Component Blocks
@import "blocks/nav";    // Points to _nav.scss
@import "blocks/form";   // Points to _form.scss
@import "blocks/button"; // Points to _button.scss
// ...