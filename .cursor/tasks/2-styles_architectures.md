PROJECT: Laravel 10, MySQL, Bootstrap, Admin Panel.
TASK: Implement a CSS build system from SCSS sources.
GOAL: Transition to a custom SCSS-based styling workflow.

CURRENT STATE:

Bootstrap is used for the layout.

Main styles are currently located in a static file: /public/maket/css/styles.css.

INSTRUCTIONS:

Create the following directory structure:
1.1 /resources/themes/admin/assets/css — for the main app.scss.
1.2 /resources/themes/admin/assets/css/blocks/ — for component-level files (e.g., nav.scss, form.scss, button.scss, grid.scss).

app.scss structure example:

SCSS
@import "base"; 
@import "variables"; 

@import "blocks/nav";
@import "blocks/subnav";
@import "blocks/offers-slider";
@import "blocks/section";
@import "blocks/product";
@import "blocks/btn";
// ... (and so on)
CONTEXT:

Source layout files are located in .cursor/maket.

Refer to .cursor/rules and .cursor/skills for architectural guidelines and coding standards.

If everything is clear, please proceed with the implementation (including Vite/compiler configuration if necessary).