import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/*', 'resources/js/*', 'resources/js/main.ts'],
            refresh: true,
        }),
    ],
});
