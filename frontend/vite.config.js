import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  plugins: [
    vue(),
    tailwindcss(),
  ],
  server: {
    port: 5173,
    proxy: {
      '/api': {
        target: process.env.API_URL || 'http://localhost:80',
        changeOrigin: true,
      },
      '/storage': {
        target: process.env.API_URL || 'http://localhost:80',
        changeOrigin: true,
      },
    },
  },
})
