import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import { InertiaProgress } from '@inertiajs/progress'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

InertiaProgress.init({
  delay: 500,
  color: '#3b5ed1',
  includeCSS: true
})

createInertiaApp({
	resolve: (name) => resolvePageComponent(`../vue/pages/${name}.vue`, import.meta.glob('../vue/pages/**/*.vue')),
	setup({ el, app, props, plugin }) {
		createApp({ render: () => h(app, props) })
			.use(plugin)
			.mount(el)
	},
})
