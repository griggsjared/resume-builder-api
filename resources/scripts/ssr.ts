import { createSSRApp, h } from 'vue'
import { renderToString } from '@vue/server-renderer'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import { importPageComponent } from '@/scripts/utils/import-page-component'
import createServer from '@inertiajs/server'

createServer(page => createInertiaApp({
  page,
  render: renderToString,
  resolve: name => importPageComponent(name, import.meta.glob('../vue/pages/**/*.vue')),
  title: title => title ? `${title} - Hodia` : 'Hodia',
  setup: ({ app, props, plugin: inertia }) => {
    return createSSRApp({ render: () => h(app, props) })
      .use(inertia)
  }
}))
