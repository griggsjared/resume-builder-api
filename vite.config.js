import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import inertiaLayout from './resources/scripts/vite/inertia-layout'
import basicSsl from '@vitejs/plugin-basic-ssl'

export default defineConfig({
  server: {
    port: import.meta.env?.VITE_SERVER_PORT || 3000,
    https: import.meta.env?.VITE_SERVER_HTTPS || false,
    host: import.meta.env?.VITE_SERVER_HOST || 'localhost'
  },
	plugins: [
    import.meta.env?.VITE_SERVER_HTTPS === true ? basicSsl() : null,
		inertiaLayout(),
		vue(),
		laravel({
      input: 'resources/scripts/app.ts',
      ssr: 'resources/scripts/ssr.ts',
      refresh: true
		}),
	],
  ssr: {
    noExternal: ['@inertiajs/server'],
  }
})

