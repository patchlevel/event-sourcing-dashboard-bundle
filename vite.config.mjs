import { defineConfig } from 'vite';
import Symfony from '@symfony/reprise/vite';

export default defineConfig(({ mode }) => ({
    plugins: [
        Symfony({
            outputPath: 'public/build',
            publicPath: '/build/',
            copy: [
                // Read from disk by HeroiconsExtension, so the names must stay stable.
                { from: 'node_modules/heroicons/24/outline', to: 'icons', hash: false },
                { from: 'assets/fonts', to: '', pattern: /LICENSE/, hash: false },
            ],
        }),
    ],
    build: {
        // The templates reference build/app.css and build/app.js by fixed path
        // (see templates/base.html.twig), so emit unhashed file names.
        rolldownOptions: {
            input: { app: './assets/app.js' },
            output: {
                entryFileNames: '[name].js',
                chunkFileNames: '[name].js',
                assetFileNames: '[name][extname]',
            },
        },
        minify: mode === 'production',
    },
    experimental: {
        // The bundle's public dir is served under /bundles/<name>/, not /build,
        // so fonts must be referenced relative to the stylesheet.
        renderBuiltUrl: (filename, { hostType }) => hostType === 'css' ? { relative: true } : undefined,
    },
}));
