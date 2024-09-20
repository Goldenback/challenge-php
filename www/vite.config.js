import {defineConfig} from 'vite';

export default defineConfig({
    build: {
        outDir: 'public/assets',
        assetsDir: 'css',
        rollupOptions: {
            input: 'assets/css/main.scss',
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
