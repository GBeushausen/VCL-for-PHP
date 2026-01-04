import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        tailwindcss(),
    ],
    publicDir: false, // Disable public directory copying
    build: {
        outDir: 'public/assets/css',
        emptyOutDir: false,
        copyPublicDir: false,
        rollupOptions: {
            input: {
                'vcl-theme': resolve(__dirname, 'src/VCL/Assets/css/vcl-theme.css'),
            },
            output: {
                assetFileNames: '[name][extname]',
            },
        },
        cssMinify: true,
    },
    css: {
        devSourcemap: true,
    },
});
