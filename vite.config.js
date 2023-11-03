import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/assets/sass/app.scss',
                'resources/assets/sass/docs.scss',
                'resources/assets/js/docs.js',
            ],
            refresh: true,
        }),
    ],
});
