import './bootstrap';
import { createApp, h } from 'vue';
import { Quasar,Notify } from 'quasar';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import '@quasar/extras/material-icons/material-icons.css';
import 'quasar/src/css/index.sass';

createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(Quasar, {
                plugins: {Notify}, // در صورت نیاز، پلاگین‌های Quasar را اینجا اضافه کنید
            })
            .mount(el);
    },
});
