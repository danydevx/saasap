import '../less/app.less'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap-icons/font/bootstrap-icons.css'
import 'leaflet/dist/leaflet.css'
import 'bootstrap'

import { createApp, h } from 'vue'
import { createInertiaApp, Link, Head } from '@inertiajs/vue3'
import { createPinia } from 'pinia'
import Toastify from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

createInertiaApp({
    resolve: async (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue')
        return await pages[`./Pages/${name}.vue`]()
    },

    setup({ el, App, props, plugin }) {
        const pinia = createPinia()

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(Toastify, {
                position: 'top-right',
                duration: 4000,
                theme: 'colored',
            })
            .component('Link', Link)
            .component('Head', Head)
            .mount(el)
    },

    defaults: {
        prefetch: {
            hoverDelay: 75,
        },
        visitOptions: (href, options) => ({
            ...options,
            viewTransition: true,
        }),
    },

    progress: {
        color: '#0d6efd',
    },
})
