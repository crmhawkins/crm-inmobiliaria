import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/sass/login.scss',
                'resources/sass/settings.scss',
                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'resources/image/background-login-3.svg',
                'resources/image/IVAN.jpg',
                'resources/image/pic-login-3.svg',

            ],
            refresh: true,
        }),
    ],
});
