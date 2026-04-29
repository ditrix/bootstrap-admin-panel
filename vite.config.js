import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/themes/admin/assets/css/app.scss',
                'resources/themes/admin/assets/js/app.js',
                'resources/themes/admin/assets/js/admin-bootstrap-table.js',
                'resources/themes/admin/assets/js/sb-admin-scripts.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: true,
        },
    },
});
