import { defineConfig } from 'vite'
import tailwindcss from 'tailwindcss'
import autoprefixer from 'autoprefixer'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import inertiaLayout from './resources/scripts/vite/inertia-layout'

export default defineConfig({
	plugins: [
		inertiaLayout(),
		vue(),
		laravel({
      input: ['resources/css/app.css', 'resources/scripts/app.ts'],
      // ssr: 'resources/scripts/ssr.ts',
      refresh: true,
			postcss: [
				tailwindcss(),
				autoprefixer(),
			],
		}),
	],
})

