import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        {
            name: 'force-exit',
            closeBundle() {
                setTimeout(() => process.exit(0), 100);
            },
        },
    ],
    build: {
        sourcemap: false,
    },
});

