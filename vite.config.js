import { defineConfig, loadEnv } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import inertiaLayout from './resources/scripts/utils/inertia-layout'
import basicSsl from '@vitejs/plugin-basic-ssl'

export default ({ mode }) => {

  process.env = {
    ...process.env,
    ...loadEnv(mode, process.cwd())
  };

  return defineConfig({
    server: {
      port: process.env?.VITE_SERVER_PORT || 3000,
      https: process.env?.VITE_SERVER_HTTPS == 'true' || false,
      host: process.env.VITE_SERVER_HOST || 'localhost'
    },
    plugins: [
      process.env?.VITE_SERVER_HTTPS == 'true' ? basicSsl() : {},
      inertiaLayout(),
      vue({
        template: {
          transformAssetUrls: {
            base: null,
            includeAbsolute: false,
          },
          useVueStyleLoader: true
        },
      }),
      laravel({
        input: 'resources/scripts/app.ts',
        ssr: 'resources/scripts/ssr.ts',
        refresh: true
      }),
    ],
    ssr: {
      noExternal: ['@inertiajs/server'],
    },
    resolve: {
      alias: {
        '@': '/resources',
      },
    },
  })
}
