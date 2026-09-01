import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: ['src/styles/main.css', 'src/scripts/main.js'],
            refresh: true,
        }),
    ],
})
