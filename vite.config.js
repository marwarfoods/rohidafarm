import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        port: 5173,
        strictPort: false, // falls back if 5173 is busy
    },
    plugins: [
        laravel({
            input: [
                // Frontend
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/home.js',
                'resources/sass/specialties.scss',
                'resources/js/specialties.js',
                'resources/sass/product-detail.scss',
                'resources/js/product-detail.js',
                'resources/sass/shop.scss',
                'resources/js/shop.js',
                'resources/sass/about.scss',
                'resources/js/about.js',
                'resources/sass/header.scss',
                'resources/js/header.js',
                'resources/sass/mail.scss',
                'resources/js/mail.js',
                // Admin Panel (separate bundle)
                'resources/sass/admin.scss',
                'resources/js/admin.js',
                'resources/sass/admin/products.scss',
                'resources/js/admin/products.js',
                // Lucky Wheel Promotion Component
                'resources/sass/lucky-wheel.scss',
                'resources/js/lucky-wheel.js',
            ],
            refresh: true,
        }),
    ],
});

