import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import vuetify from "vite-plugin-vuetify";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/comparisonCharts.js',
                'resources/js/radarChart.js',
                'resources/js/dashboard.js',
                'resources/js/initiatives.js',
                'resources/js/institutions.js',
                'resources/js/user-feedback.js',
                'resources/js/assess.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        vuetify({autoImport: true}),
    ],
    resolve: {
        alias: [
            {
                find: /^~(.*)$/,
                replacement: '$1',
            },
            {
                find: 'spatie-media-library-pro',
                replacement: '/vendor/spatie/laravel-medialibrary-pro/resources/js',
            },
        ],
    },
});
