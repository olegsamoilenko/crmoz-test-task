import '../css/app.css';
import "vue-toastification/dist/index.css";
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';
import Toast, { POSITION, type PluginOptions } from "vue-toastification";

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

import { router } from '@inertiajs/vue3'
import { useToast } from "vue-toastification";

router.on('invalid', (event) => {
    const responseBody = event.detail.response?.data;
    if (responseBody?.error_message) {
        const toast = useToast()
        toast.error(responseBody.error_message);
        event.preventDefault();
    }
});

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const options: PluginOptions = {
            position: POSITION.TOP_RIGHT,
            timeout: 3500,
        };
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Toast, options)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
