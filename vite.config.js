import path from 'path';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import {quasar, transformAssetUrls}  from "@quasar/vite-plugin";

export default defineConfig({
    server: {
        host: 'localhost',
        port: 5175,
        cors: true  // فعال کردن CORS
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: { transformAssetUrls },
        }),
        quasar()
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js')
        },
    },
});
