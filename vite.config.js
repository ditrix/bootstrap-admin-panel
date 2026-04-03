import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/themes/admin/assets/css/app.scss',
                'resources/themes/admin/assets/js/admin-bootstrap-table.js',
            ],
            refresh: true,
        }),
    ],
});
