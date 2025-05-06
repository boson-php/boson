const path = require('path');
const {defineConfig} = require('vite');

module.exports = defineConfig({
    build: {
        outDir: path.resolve(__dirname, 'resources/dist'),
        assetsDir: '',
        rollupOptions: {
            input: path.resolve(__dirname, 'resources/src/main.ts'),
        }
    }
})
