import {defineConfig} from 'vite';

export default defineConfig({
    build: {
        outDir: 'www/public/assets',
        assetsDir: 'css',
        rollupOptions: {
            input: 'www/assets/css/main.scss',
            output: {
                assetFileNames: 'css/main.css',
                entryFileNames: 'js/[name].js',
                chunkFileNames: 'js/[name].js'
            }
        },
        sourcemap: true,
        cssCodeSplit: false,
    },
});
