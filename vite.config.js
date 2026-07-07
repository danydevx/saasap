import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'
import less from 'less'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/less/app.less', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    css: {
        preprocessorOptions: {
            less: {
                javascriptEnabled: true,
            }
        }
    },
    server: {
         host: 'saas.local',
            port: 5173,
            hmr: {
                host: 'saas.local',
            },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
