import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import {fileURLToPath} from 'url'
export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
          '@core-scss': fileURLToPath(new URL('./resources/styles/@core', import.meta.url)),
          '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
          '@themeConfig': fileURLToPath(new URL('./themeConfig.js', import.meta.url)),
          '@core': fileURLToPath(new URL('./resources/js/@core', import.meta.url)),
          '@layouts': fileURLToPath(new URL('./resources/js/@layouts', import.meta.url)),
          '@images': fileURLToPath(new URL('./resources/images/', import.meta.url)),
          '@styles': fileURLToPath(new URL('./resources/styles/', import.meta.url)),
          '@configured-variables': fileURLToPath(new URL('./resources/styles/variables/_template.scss', import.meta.url)),
          '@axios': fileURLToPath(new URL('./resources/js/plugins/axios', import.meta.url)),
          '@validators': fileURLToPath(new URL('./resources/js/@core/utils/validators', import.meta.url)),
          'apexcharts': fileURLToPath(new URL('node_modules/apexcharts-clevision', import.meta.url)),
        },
      },
    
});
