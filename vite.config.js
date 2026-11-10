import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/app/documentation.js',
                'resources/js/app/project-create.js',
                'resources/js/app/project-edit.js',
                'resources/css/app/documentation.css',
                'resources/css/app/project-create.css',
                'resources/css/app/project-edit.css',
            ],
            refresh: true,
        }),
    ],
});
