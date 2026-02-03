import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/styles.css',
                'resources/css/home/volcano-animation.css',
                'resources/css/home/ai-bot.css',
                'resources/css/home/filter-modal.css',
                'resources/css/home/sort-dropdown.css',
                'resources/css/realTimeVolcano.css',
                'resources/js/main.js',
                'resources/js/bootstrap.js',
                'resources/js/home/home.js',
                'resources/js/home/volcano-animation.js',
                'resources/js/home/ai-bot.js',
                'resources/js/home/filter-modal.js',
                'resources/js/home/sort-dropdown.js',
                'resources/js/home/lazy-load.js',
                'resources/js/home/load-more.js',
                'resources/js/home/volcano-modal.js',
                'resources/js/home/interactive-map/map-core.js',
                'resources/js/home/interactive-map/map-display.js',
                'resources/js/home/interactive-map/map-sync.js',
                'resources/js/profile/profile.js',
                'resources/js/realTimeVolcano/realTimeVolcano.js',
                'resources/js/login/login.js',
                'resources/js/admin/manage-volcanoes.js',
                'resources/js/admin/manage-users.js',
                'resources/js/admin/manage-achievements.js',
                'resources/js/my-volcanoes/volcano-actions.js',
                'resources/js/my-volcanoes/number_increment.js',
                'resources/js/my-volcanoes/edit-date-popup.js',
                'resources/js/my-volcanoes/panels.js'
            ],
            refresh: true,
        }),
    ],
});
