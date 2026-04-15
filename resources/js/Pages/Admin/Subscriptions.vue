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
              <a
                v-for="item in menu.items"
                :key="`${menu.label}-${item.label}`"
                :href="item.url || '#'"
                class="block rounded-md px-2.5 py-1.5 text-[12px] text-slate-300 hover:bg-slate-800 hover:text-white"
              >
                {{ item.label }}
              </a>
            </div>
          </div>
        </div>
      </aside>

      <main class="px-4 sm:px-6 lg:px-7 py-5">
        <header class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm flex items-center justify-between gap-3">
          <div>
            <p class="text-[10px] uppercase tracking-[0.2em] text-slate-500">Super Admin</p>
            <h1 class="text-xl font-bold text-slate-900">User Subscription Monitor</h1>
            <p class="text-xs text-slate-500 mt-1">See which user is on which plan and when each subscription expires.</p>
          </div>
          <form @submit.prevent="logout">
            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition">
              Logout
            </button>
          </form>
        </header>

        <section class="mt-4 grid grid-cols-2 xl:grid-cols-5 gap-3">
          <article class="rounded-xl bg-white border border-slate-200 p-3 shadow-sm">
            <p class="text-[11px] text-slate-500">Active</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ stats.active }}</p>
          </article>
          <article class="rounded-xl bg-white border border-slate-200 p-3 shadow-sm">
            <p class="text-[11px] text-slate-500">Expired</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ stats.expired }}</p>
          </article>
          <article class="rounded-xl bg-white border border-slate-200 p-3 shadow-sm">
            <p class="text-[11px] text-slate-500">Pending</p>
            <p class="text-2xl font-bold text-cyan-600 mt-1">{{ stats.pending }}</p>
          </article>
          <article class="rounded-xl bg-white border border-slate-200 p-3 shadow-sm">
            <p class="text-[11px] text-slate-500">Failed</p>
            <p class="text-2xl font-bold text-rose-600 mt-1">{{ stats.failed }}</p>
          </article>
          <article class="rounded-xl bg-white border border-slate-200 p-3 shadow-sm col-span-2 xl:col-span-1">
            <p class="text-[11px] text-slate-500">Revenue</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">INR {{ formatAmount(stats.revenue) }}</p>
          </article>
        </section>

        <section class="mt-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
          <div class="px-4 py-3 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
            <h3 class="text-sm font-bold text-slate-900">Subscriptions</h3>
            <div class="flex items-center gap-2">
              <input
                v-model="search"
                type="text"
                placeholder="Search user/email/plan"
                class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-200"
              />
              <select v-model="statusFilter" class="rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-200">
                <option value="all">All Status</option>
                <option value="active">Active</option>
                <option value="expired">Expired</option>
                <option value="pending">Pending</option>
                <option value="failed">Failed</option>
              </select>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-slate-50 text-[10px] uppercase tracking-wide text-slate-500">
                <tr>
                  <th class="px-4 py-2 text-left">User</th>
                  <th class="px-4 py-2 text-left">Plan</th>
                  <th class="px-4 py-2 text-left">Amount</th>
                  <th class="px-4 py-2 text-left">Status</th>
                  <th class="px-4 py-2 text-left">Start</th>
                  <th class="px-4 py-2 text-left">End</th>
                  <th class="px-4 py-2 text-left">Gateway</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 text-[12px]">
                <tr v-for="subscription in filteredSubscriptions" :key="subscription.id" class="hover:bg-slate-50/60">
                  <td class="px-4 py-2.5">
                    <p class="font-semibold text-slate-900">{{ subscription.user?.name || 'Unknown' }}</p>
                    <p class="text-[11px] text-slate-500">{{ subscription.user?.email || '-' }}</p>
                  </td>
                  <td class="px-4 py-2.5 font-semibold text-slate-900">{{ subscription.plan?.name || '-' }}</td>
                  <td class="px-4 py-2.5 text-slate-700">{{ subscription.currency }} {{ formatAmount(subscription.amount) }}</td>
                  <td class="px-4 py-2.5">
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold" :class="statusClass(subscription.status)">
                      {{ subscription.status }}
                    </span>
                  </td>
                  <td class="px-4 py-2.5 text-slate-600">{{ formatDate(subscription.starts_at || subscription.purchased_at) }}</td>
                  <td class="px-4 py-2.5 text-slate-600">{{ subscription.ends_at ? formatDate(subscription.ends_at) : 'Lifetime' }}</td>
                  <td class="px-4 py-2.5 text-slate-600">{{ subscription.gateway || '-' }}</td>
                </tr>
                <tr v-if="!filteredSubscriptions.length">
                  <td colspan="7" class="px-4 py-6 text-center text-xs text-slate-500">No subscriptions found.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  subscriptions: {
    type: Array,
    default: () => [],
  },
  stats: {
    type: Object,
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

const search = ref('');
const statusFilter = ref('all');
const sidebarMenus = computed(() => props.sidebarMenus || []);

const openMenus = reactive(
  Object.fromEntries((sidebarMenus.value || []).map((menu) => [menu.label, menu.label === 'Subscriptions' || menu.label === 'Main']))
);

const toggleMenu = (label) => {
  openMenus[label] = !openMenus[label];
};

const filteredSubscriptions = computed(() => {
  const q = search.value.toLowerCase().trim();

  return props.subscriptions.filter((subscription) => {
    const statusOk = statusFilter.value === 'all' ? true : subscription.status === statusFilter.value;

    if (!q) {
      return statusOk;
    }

    const userName = subscription.user?.name || '';
    const userEmail = subscription.user?.email || '';
    const planName = subscription.plan?.name || '';

    const textOk = [userName, userEmail, planName, subscription.status, subscription.gateway]
      .filter(Boolean)
      .some((value) => String(value).toLowerCase().includes(q));

    return statusOk && textOk;
  });
});

const statusClass = (status) => {
  if (status === 'active') return 'bg-emerald-100 text-emerald-700';
  if (status === 'expired') return 'bg-amber-100 text-amber-700';
  if (status === 'pending') return 'bg-cyan-100 text-cyan-700';
  if (status === 'failed') return 'bg-rose-100 text-rose-700';
  return 'bg-slate-100 text-slate-700';
};

const formatDate = (value) => {
  if (!value) {
    return '-';
  }

  return new Date(value).toLocaleDateString('en-IN', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  });
};

const formatAmount = (value) => {
  const numeric = Number(value || 0);

  return numeric.toLocaleString('en-IN', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
  });
};

const logout = () => {
  router.post(props.logoutAction);
};
</script>
