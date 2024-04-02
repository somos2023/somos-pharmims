import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy'
import vue from '@vitejs/plugin-vue';

import { fileURLToPath, URL } from "node:url";

export default defineConfig({
    build: {
        manifest: true,
        rtl: true,
        outDir: 'public/build/',
        cssCodeSplit: true,
        rollupOptions: {
            output: {
                assetFileNames: (css) => {
                    if (css.name.split('.').pop() == 'css') {
                        return 'css/' + `[name]` + '.min.' + 'css';
                    } else {
                        return 'icons/' + css.name;
                    }
                },
                entryFileNames: 'js/' + `[name]` + `.js`,
            },
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/sass/app-rtl.scss',
                'resources/sass/bootstrap.scss',
                'resources/sass/icons.scss',
                'resources/js/app.js',
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
        viteStaticCopy({
            targets: [
                {
                    src: 'resources/fonts',
                    dest: ''
                },
                {
                    src: 'resources/images',
                    dest: ''
                },
            ],
        })
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            // '@': fileURLToP ath(new URL("./resources", import.meta.url))
        },
    },
    define: {
        '__VUE_I18N_FULL_INSTALL__': 'false', // Replace with the correct flag value
        '__VUE_I18N_LEGACY_API__': 'true', // Replace with the correct flag value
        // Add other flags here as needed
    },
    build: {
        chunkSizeWarningLimit: 1600,
    }
});
