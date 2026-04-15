import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

import Welcome from './Pages/Welcome.vue';
import Browse from './Pages/Browse.vue';
import Login from './Pages/Login.vue';
import Register from './Pages/Register.vue';
import ForgotPassword from './Pages/ForgotPassword.vue';
import ResetPassword from './Pages/ResetPassword.vue';
import VerifyEmail from './Pages/VerifyEmail.vue';
import Dashboard from './Pages/Dashboard.vue';
import DashboardRequests from './Pages/DashboardRequests.vue';
import Profiles from './Pages/Profiles.vue';
import ProfileView from './Pages/ProfileView.vue';
import MatchProfile from './Pages/MatchProfile.vue';
import KundliHistory from './Pages/KundliHistory.vue';
import Faqs from './Pages/Faqs.vue';
import CmsPageView from './Pages/CmsPageView.vue';
import Pricing from './Pages/Pricing.vue';
import AdminLogin from './Pages/Admin/Login.vue';
import AdminDashboard from './Pages/Admin/Dashboard.vue';
import AdminPricingPlans from './Pages/Admin/PricingPlans.vue';
import AdminIntegrationSettings from './Pages/Admin/IntegrationSettings.vue';
import AdminSubscriptions from './Pages/Admin/Subscriptions.vue';
import AdminCmsPages from './Pages/Admin/CmsPages.vue';
import AdminCmsSections from './Pages/Admin/CmsSections.vue';

const pages = {
    Welcome,
    Browse,
    Login,
    Register,
    ForgotPassword,
    ResetPassword,
    VerifyEmail,
    Dashboard,
    DashboardRequests,
    Profiles,
    ProfileView,
    MatchProfile,
    KundliHistory,
    Faqs,
    CmsPageView,
    Pricing,
    'Admin/Login': AdminLogin,
    'Admin/Dashboard': AdminDashboard,
    'Admin/PricingPlans': AdminPricingPlans,
    'Admin/IntegrationSettings': AdminIntegrationSettings,
    'Admin/Subscriptions': AdminSubscriptions,
    'Admin/CmsPages': AdminCmsPages,
    'Admin/CmsSections': AdminCmsSections,
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


