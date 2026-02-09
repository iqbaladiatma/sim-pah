import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
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
        VitePWA({
            registerType: 'autoUpdate',
            injectRegister: 'auto',
            outDir: 'public',
            devOptions: {
                enabled: true,
                type: 'module',
                navigateFallback: 'index.php',
            },
            workbox: {
                cleanupOutdatedCaches: true,
                globPatterns: ['**/*.{js,css,html,ico,png,svg,json,vue,txt,woff2}']
            },
            manifestFilename: 'site.webmanifest',
            manifest: {
                name: 'SIM URT PAH - MATARAM',
                short_name: 'SIM URT',
                description: 'Sistem Manajemen Unit Rumah Tangga Pondok Pesantren Abu Hurairah Mataram',
                start_url: '/',
                display: 'standalone',
                background_color: '#ffffff',
                theme_color: '#C9A658',
                orientation: 'portrait',
                icons: [
                    {
                        src: '/favicon.ico',
                        sizes: '64x64 32x32 24x24 16x16',
                        type: 'image/x-icon'
                    },
                    {
                        src: '/favicon.ico',
                        sizes: '192x192',
                        type: 'image/x-icon',
                        purpose: 'any maskable'
                    },
                    {
                        src: '/favicon.ico',
                        sizes: '512x512',
                        type: 'image/x-icon',
                        purpose: 'any maskable'
                    }
                ],
                screenshots: [
                     {
                        "src": "/favicon.ico",
                        "sizes": "512x512",
                        "type": "image/x-icon",
                        "form_factor": "wide",
                        "label": "Homescreen of SIM URT"
                    },
                     {
                        "src": "/favicon.ico",
                        "sizes": "512x512",
                        "type": "image/x-icon",
                        "form_factor": "mobile",
                        "label": "Homescreen of SIM URT"
                    }
                ]
            }
        })
    ],
    build: {
        chunkSizeWarningLimit: 1600,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        return id.toString().split('node_modules/')[1].split('/')[0].toString();
                    }
                }
            }
        }
    }
});
