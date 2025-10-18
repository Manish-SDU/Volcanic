import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/styles.css',
                'resources/css/home/volcano-animation.css',
                'resources/css/home/volcano-map.css',
                'resources/js/main.js',
                'resources/js/bootstrap.js',
                'resources/js/home/home.js',
                'resources/js/home/volcano-animation.js',
                'resources/js/home/volcano-map.js',
                'resources/js/profile/profile.js',
                'resources/js/my-volcanoes/panels.js',
                'resources/js/my-volcanoes/number_increment.js'
            ],
            refresh: true,
        }),
    ],
});
