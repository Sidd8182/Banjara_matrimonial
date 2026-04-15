<template>
  <div class="min-h-screen bg-slate-100 text-[13px] text-slate-700">
    <div class="grid min-h-screen grid-cols-1 lg:grid-cols-[280px_1fr]">
      <aside class="bg-slate-950 text-slate-200 px-4 py-5 border-r border-slate-800">
        <p class="text-[10px] uppercase tracking-[0.22em] text-cyan-300">Banjara SaaS Console</p>
        <h1 class="mt-1 text-xl font-bold text-white">Super Admin</h1>

        <div class="mt-6 space-y-2">
          <div
            v-for="menu in sidebarMenus"
            :key="menu.label"
            class="rounded-lg border border-slate-800 bg-slate-900/60"
          >
            <button
              type="button"
              class="w-full px-3 py-2.5 flex items-center justify-between text-left text-xs font-semibold text-slate-100 hover:bg-slate-800/80 rounded-lg"
              @click="toggleMenu(menu.label)"
            >
              <span>{{ menu.label }}</span>
              <span class="text-[11px]">{{ openMenus[menu.label] ? '▾' : '▸' }}</span>
            </button>

            <div v-if="openMenus[menu.label]" class="px-2 pb-2 space-y-1">
              <div v-for="item in menu.items" :key="`${menu.label}-${item.label}`">
                <a :href="item.url || '#'" class="block rounded-md px-2.5 py-1.5 text-[12px] text-slate-300 hover:bg-slate-800 hover:text-white">
                  {{ item.label }}
                </a>
                <div v-if="item.children" class="ml-3 mt-1 space-y-1 border-l border-slate-700 pl-2">
                  <a
                    v-for="child in item.children"
                    :key="`${item.label}-${child.label}`"
                    :href="child.url || '#'"
                    class="block rounded-md px-2 py-1 text-[11px] text-slate-400 hover:bg-slate-800 hover:text-slate-100"
                  >
                    {{ child.label }}
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </aside>

      <main class="px-4 sm:px-6 lg:px-7 py-5">
        <header class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm flex items-center justify-between gap-3">
          <div>
            <p class="text-[10px] uppercase tracking-[0.2em] text-slate-500">Super Admin</p>
            <h1 class="text-xl font-bold text-slate-900">Third-Party Integration Settings</h1>
            <p class="text-xs text-slate-500 mt-1">Configure Email, SMS, WhatsApp, Payment, Push, and Astrology integrations.</p>
          </div>
          <form @submit.prevent="logout">
            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition">
              Logout
            </button>
          </form>
        </header>

        <div v-if="statusMessage" class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm text-emerald-700">
          {{ statusMessage }}
        </div>

        <form class="mt-4 space-y-4" @submit.prevent="saveSettings">
        <section id="email" class="panel">
          <div class="panel-header">
            <h2 class="panel-title">Email Setting</h2>
          </div>
          <div class="panel-grid">
            <Field label="From Name" v-model="form.mail_from_name" />
            <Field label="From Address" v-model="form.mail_from_address" />
            <Field label="SMTP Host" v-model="form.mail_host" />
            <Field label="SMTP Port" v-model="form.mail_port" />
            <Field label="SMTP Username" v-model="form.mail_username" />
            <Field label="SMTP Password" type="password" v-model="form.mail_password" />
            <Field label="Encryption (tls/ssl)" v-model="form.mail_encryption" />
          </div>
        </section>

        <section id="sms" class="panel">
          <div class="panel-header">
            <h2 class="panel-title">SMS Setting</h2>
            <Toggle label="Enable SMS" v-model="form.sms_enabled" />
          </div>
          <div class="panel-grid">
            <Field label="SMS Provider" v-model="form.sms_provider" />
            <Field label="API Key" type="password" v-model="form.sms_api_key" />
            <Field label="Sender ID" v-model="form.sms_sender_id" />
            <Field label="Base URL" v-model="form.sms_base_url" />
          </div>
        </section>

        <section id="whatsapp" class="panel">
          <div class="panel-header">
            <h2 class="panel-title">WhatsApp Setting</h2>
            <Toggle label="Enable WhatsApp" v-model="form.whatsapp_enabled" />
          </div>
          <div class="panel-grid">
            <Field label="Provider" v-model="form.whatsapp_provider" />
            <Field label="Access Token" type="password" v-model="form.whatsapp_token" />
            <Field label="Phone Number ID" v-model="form.whatsapp_phone_id" />
            <Field label="Webhook Secret" type="password" v-model="form.whatsapp_webhook_secret" />
          </div>
        </section>

        <section id="payment" class="panel">
          <div class="panel-header">
            <h2 class="panel-title">Payment Gateway Setting</h2>
            <Toggle label="Enable Payments" v-model="form.payment_gateway_enabled" />
          </div>
          <div class="panel-grid">
            <Field label="Primary Gateway" v-model="form.payment_primary_gateway" placeholder="razorpay / paypal / stripe" />
            <Field label="Currency" v-model="form.payment_currency" placeholder="INR" />
          </div>

          <h3 class="sub-title">Razorpay</h3>
          <div class="panel-grid">
            <Field label="Razorpay Mode" v-model="form.razorpay_mode" placeholder="test/live" />
            <Field label="Razorpay Key ID" v-model="form.razorpay_key_id" />
            <Field label="Razorpay Secret" type="password" v-model="form.razorpay_key_secret" />
          </div>
          <p class="mt-2 text-[11px] text-slate-500">
            Key prefix detected: <strong>{{ razorpayKeyPrefix || 'not-set' }}</strong>. Use <strong>rzp_test_</strong> for test mode and <strong>rzp_live_</strong> for live mode.
          </p>

          <h3 class="sub-title">PayPal</h3>
          <div class="panel-grid">
            <Field label="PayPal Client ID" v-model="form.paypal_client_id" />
            <Field label="PayPal Client Secret" type="password" v-model="form.paypal_client_secret" />
            <Field label="PayPal Mode" v-model="form.paypal_mode" placeholder="sandbox/live" />
          </div>

          <h3 class="sub-title">Stripe</h3>
          <div class="panel-grid">
            <Field label="Stripe Public Key" v-model="form.stripe_public_key" />
            <Field label="Stripe Secret Key" type="password" v-model="form.stripe_secret_key" />
            <Field label="Stripe Webhook Secret" type="password" v-model="form.stripe_webhook_secret" />
          </div>
        </section>

        <section id="push" class="panel">
          <div class="panel-header">
            <h2 class="panel-title">Push Notification Setting</h2>
            <Toggle label="Enable Push" v-model="form.push_enabled" />
          </div>
          <div class="panel-grid">
            <Field label="Push Provider" v-model="form.push_provider" placeholder="fcm/onesignal" />
            <Field label="FCM Server Key" type="password" v-model="form.fcm_server_key" />
            <Field label="FCM Sender ID" v-model="form.fcm_sender_id" />
            <Field label="OneSignal App ID" v-model="form.onesignal_app_id" />
            <Field label="OneSignal REST API Key" type="password" v-model="form.onesignal_rest_api_key" />
          </div>
        </section>

        <section id="astrology" class="panel">
          <div class="panel-header">
            <h2 class="panel-title">Astrology Setting</h2>
            <Toggle label="Enable Astrology" v-model="form.astrology_enabled" />
          </div>

          <div class="panel-grid">
            <Field label="Provider" v-model="form.astrology_provider" placeholder="freeastrologyapi" />
            <Field label="API Base URL" v-model="form.astrology_api_base_url" placeholder="https://json.freeastrologyapi.com" />
            <Field label="Birth Details Path" v-model="form.astrology_birth_details_path" placeholder="/planets/extended" />
            <Field label="Matchmaking Path" v-model="form.astrology_matchmaking_path" placeholder="/matchmaking" />
            <Field label="API Key" type="password" v-model="form.astrology_api_key" />
            <Field label="Timeout (seconds)" v-model="form.astrology_timeout_seconds" placeholder="8" />
            <Field label="Cache Duration (minutes)" v-model="form.astrology_cache_minutes" placeholder="1440" />
          </div>

          <div class="mt-3 flex flex-wrap items-center gap-4">
            <Toggle label="Enable Kundli Matchmaking" v-model="form.astrology_matchmaking_enabled" />
            <Toggle label="Fail Open (normal mode on API error)" v-model="form.astrology_fail_open" />
          </div>

          <p class="mt-2 text-[11px] text-slate-500">
            If the API key/provider is not configured or an API call fails, the project continues in normal matchmaking mode.
          </p>
        </section>

        <div class="sticky bottom-4 flex justify-end">
          <button type="submit" class="rounded-lg bg-slate-900 px-5 py-2.5 text-xs font-semibold text-white hover:bg-slate-800" :disabled="form.processing">
            {{ form.processing ? 'Saving...' : 'Save All Settings' }}
          </button>
        </div>
        </form>
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, defineComponent, h, reactive } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
  settings: {
    type: Object,
    required: true,
  },
  saveAction: {
    type: String,
    required: true,
  },
  dashboardUrl: {
    type: String,
    required: true,
  },
  pricingManagementUrl: {
    type: String,
    required: true,
  },
  integrationSettingsUrl: {
    type: String,
    required: true,
  },
  subscriptionsUrl: {
    type: String,
    required: true,
  },
  sidebarMenus: {
    type: Array,
    default: () => [],
  },
  logoutAction: {
    type: String,
    required: true,
  },
});

const page = usePage();
const statusMessage = computed(() => page.props.flash?.status || '');
const sidebarMenus = computed(() => props.sidebarMenus || []);

const form = useForm({ ...props.settings });
if (!form.razorpay_mode) {
  form.razorpay_mode = 'test';
}

const razorpayKeyPrefix = computed(() => {
  const key = (form.razorpay_key_id || '').trim();
  if (!key) {
    return '';
  }

  if (key.startsWith('rzp_test_')) {
    return 'rzp_test_';
  }

  if (key.startsWith('rzp_live_')) {
    return 'rzp_live_';
  }

  return 'unknown';
});

const openMenus = reactive(
  Object.fromEntries((sidebarMenus.value || []).map((menu) => [menu.label, menu.label === 'Platform Settings']))
);

const toggleMenu = (label) => {
  openMenus[label] = !openMenus[label];
};

const saveSettings = () => {
  form.post(props.saveAction, {
    preserveScroll: true,
  });
};

const logout = () => {
  router.post(props.logoutAction);
};

const baseFieldClass = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-200';

const settingsFieldExample = (label) => {
  const text = String(label || '').toLowerCase();
  if (text.includes('email')) {
    return 'e.g., support@example.com';
  }
  if (text.includes('port')) {
    return 'e.g., 587';
  }
  if (text.includes('host')) {
    return 'e.g., smtp.example.com';
  }
  if (text.includes('url')) {
    return 'e.g., https://api.example.com';
  }
  if (text.includes('timeout')) {
    return 'e.g., 8';
  }

  return `e.g., ${label}`;
};

const Field = defineComponent({
  name: 'SettingsField',
  props: {
    modelValue: { type: [String, Number], default: '' },
    label: { type: String, required: true },
    type: { type: String, default: 'text' },
    placeholder: { type: String, default: '' },
  },
  emits: ['update:modelValue'],
  setup(componentProps, { emit }) {
    return () =>
      h('div', [
        h('label', { class: 'block text-[11px] font-semibold text-slate-700 mb-1' }, componentProps.label),
        h('input', {
          class: baseFieldClass,
          value: componentProps.modelValue,
          type: componentProps.type,
          placeholder: componentProps.placeholder || settingsFieldExample(componentProps.label),
          onInput: (event) => emit('update:modelValue', event.target.value),
        }),
      ]);
  },
});

const Toggle = defineComponent({
  name: 'SettingsToggle',
  props: {
    modelValue: { type: Boolean, default: false },
    label: { type: String, required: true },
  },
  emits: ['update:modelValue'],
  setup(componentProps, { emit }) {
    return () =>
      h('label', { class: 'inline-flex items-center gap-2 text-[11px] font-semibold text-slate-600' }, [
        h('input', {
          type: 'checkbox',
          checked: componentProps.modelValue,
          onChange: (event) => emit('update:modelValue', event.target.checked),
        }),
        componentProps.label,
      ]);
  },
});
</script>

<style scoped>
.panel {
  border: 1px solid #e2e8f0;
  border-radius: 1rem;
  background: #ffffff;
  padding: 1rem;
  box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
}

.panel-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.75rem;
  gap: 0.5rem;
}

.panel-title {
  font-size: 0.9rem;
  font-weight: 700;
  color: #0f172a;
}

.panel-grid {
  display: grid;
  grid-template-columns: repeat(1, minmax(0, 1fr));
  gap: 0.75rem;
}

.sub-title {
  margin-top: 0.9rem;
  margin-bottom: 0.5rem;
  font-size: 0.78rem;
  font-weight: 700;
  color: #334155;
}

@media (min-width: 768px) {
  .panel-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
</style>
