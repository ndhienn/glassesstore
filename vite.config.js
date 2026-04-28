import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';


export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/admin/admin.css', 'resources/js/admin/admin.js'],
            refresh: true,
        }),
    ],
});
