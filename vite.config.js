import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import fs from 'fs';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        {
            name: 'create-dist-dir',
            closeBundle() {
                try {
                    if (!fs.existsSync('dist')) {
                        fs.mkdirSync('dist', { recursive: true });
                    }
                    fs.cpSync('public', 'dist', { recursive: true });
                } catch (e) {
                    console.error('Failed to copy to dist:', e);
                }
            }
        }
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});

