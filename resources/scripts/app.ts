import '../css/app.css'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import { InertiaProgress } from '@inertiajs/progress'
import { importPageComponent } from '@/scripts/utils/import-page-component'

createInertiaApp({
	resolve: name => importPageComponent(name, import.meta.glob('../vue/pages/**/*.vue')),
  title: title => title !== 'Hodia' ? `${title} - Hodia` : 'Hodia',
	setup({ el, app, props, plugin }) {
		createApp({ render: () => h(app, props) })
			.use(plugin)
			.mount(el)
	},
})

InertiaProgress.init({
  delay: 100,
  color: '#f1f1f1',
  includeCSS: true
})
