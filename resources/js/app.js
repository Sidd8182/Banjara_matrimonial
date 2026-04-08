import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

import Welcome from './Pages/Welcome.vue';
import Browse from './Pages/Browse.vue';
import Login from './Pages/Login.vue';
import Register from './Pages/Register.vue';
import ForgotPassword from './Pages/ForgotPassword.vue';
import ResetPassword from './Pages/ResetPassword.vue';
import Dashboard from './Pages/Dashboard.vue';
import Profiles from './Pages/Profiles.vue';
import Pricing from './Pages/Pricing.vue';
import AdminLogin from './Pages/Admin/Login.vue';
import AdminDashboard from './Pages/Admin/Dashboard.vue';

const pages = {
    Welcome,
    Browse,
    Login,
    Register,
    ForgotPassword,
    ResetPassword,
    Dashboard,
    Profiles,
    Pricing,
    'Admin/Login': AdminLogin,
    'Admin/Dashboard': AdminDashboard,
};

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Banjara Matrimonial';

const appEl = document.getElementById('app');
const initialPage = appEl?.dataset?.page ? JSON.parse(appEl.dataset.page) : null;

if (!window.__BANJARA_INERTIA_BOOTSTRAPPED__) {
    window.__BANJARA_INERTIA_BOOTSTRAPPED__ = true;

    if (!initialPage) {
        throw new Error('Inertia initial page payload is missing on #app[data-page].');
    }

    createInertiaApp({
        id: 'app',
        page: initialPage,
        title: (title) => `${title} - ${appName}`,
        resolve: (name) => {
            const page = pages[name];
            if (!page) {
                throw new Error(`Page component "${name}" not found.`);
            }
            return page;
        },
        setup({ el, App, props, plugin }) {
            createApp({ render: () => h(App, props) })
                .use(plugin)
                .mount(el);
        },
        progress: {
            color: '#dc2626',
        },
    });
}


