import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/sass/app.scss",
                "resources/sass/login.scss",
                "resources/sass/settings.scss",
                "resources/js/app.js",
                "resources/js/bootstrap.js",
                "resources/image/background-login-3.svg",
                "resources/image/IVAN.jpg",
                "resources/image/pic-login-3.svg",
            ],
            refresh: true,
        }),
    ],
});
