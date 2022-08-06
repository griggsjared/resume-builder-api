import { createSSRApp, h } from 'vue'
import { renderToString } from '@vue/server-renderer'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import createServer from '@inertiajs/server'

createServer((page) => createInertiaApp({
  page,
  render: renderToString,
  resolve: (name) => resolvePageComponent(`../vue/pages/${name}.vue`, import.meta.glob('../vue/pages/**/*.vue')),
  setup: ({ app, props, plugin: inertia }) => {
    return createSSRApp({ render: () => h(app, props) })
      .use(inertia)
  }
}))
