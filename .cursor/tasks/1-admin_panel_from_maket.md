Project: Laravel 10 Admin Dashboard (PET)

Stack: Laravel 10 + MySQL (Sail). Strictly follow ./cursor/rules, ./cursor/skills and ./cursor/prompts.

The folder /maket contains the complete admin panel mockup:
- HTML, CSS, Bootstrap 5
- index.html = main dashboard
- /maket/sb-admin = original template sources

Task: Convert the entire mockup into a fully functional Laravel admin panel with maximum visual and functional fidelity.

Requirements:
1. Create folder: resources/views/admin/
2. Convert EVERY HTML page from the /maket folder into Blade templates and place them in resources/views/admin/
3. Implement all pages shown in the mockup (dashboard, tables, charts, forms, login, register, forgot password, 404, blank page, etc.).
4. Create the following Laravel components:
   - app/Http/Controllers/Admin/*
   - app/Models/*
   - app/Http/Resources/Admin/*
   - app/Enums/
   - app/Helpers/
   - app/View/Composers/
   - app/Services/
5. Route /adm/:
   - If user is logged in via session → redirect to dashboard
   - If not logged in → show login page
6. All tables must use standard `id` + timestamps + exactly the same columns that are defined in /maket/tables.html
7. Administrators are stored in a separate dedicated table (not the default `users` table). Default credentials: admin@mail.com / password
8. Use fake data:
   - Create DatabaseSeeder and specific AdminSeeder
   - Use Faker to populate all tables with realistic fake data (at least 50-100 records per table where it makes sense)
   - Seed data when running `php artisan migrate --seed`
9. Add basic tests:
   - Feature tests for authentication (login, logout, protected routes)
   - Feature tests for main admin routes (dashboard, tables page, forms)
   - Simple unit tests for Helpers and Services
   - Browser tests (Laravel Dusk) are optional but welcome for critical pages

Implement everything exactly as shown in the sb-admin mockup using Laravel best practices and the components listed above. Pay special attention to layout consistency, sidebar navigation, and responsive design.
