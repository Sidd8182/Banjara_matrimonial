<template>
  <MainLayout :fluid="true">
    <div class="grid min-h-[calc(100vh-220px)] grid-cols-1 lg:grid-cols-[280px_1fr] rounded-2xl border border-slate-200 bg-slate-100 text-[13px] text-slate-700 shadow-sm">
      <aside class="border-r border-slate-800 bg-slate-950 px-4 py-5 text-slate-200">
        <p class="text-[10px] uppercase tracking-[0.22em] text-cyan-300">Banjara User Console</p>
        <h1 class="mt-1 text-xl font-bold text-white">Member Dashboard</h1>
        <p class="mt-1 text-[11px] text-slate-300">Frontend Experience Panel</p>

        <div class="mt-6 space-y-2">
          <div
            v-for="menu in menus"
            :key="menu.label"
            class="rounded-lg border border-slate-800 bg-slate-900/60"
          >
            <button
              type="button"
              class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-left text-xs font-semibold text-slate-100 hover:bg-slate-800/80"
              @click="toggleMenu(menu.label)"
            >
              <span>{{ menu.label }}</span>
              <span class="text-[11px]">{{ openMenus[menu.label] ? '▾' : '▸' }}</span>
            </button>

            <div v-if="openMenus[menu.label]" class="space-y-1 px-2 pb-2">
              <a
                v-for="item in menu.items"
                :key="`${menu.label}-${item.key}`"
                :href="item.url"
                class="block rounded-md px-2.5 py-1.5 text-[12px]"
                :class="itemClass(item.key)"
              >
                {{ item.label }}
              </a>
            </div>
          </div>
        </div>
      </aside>

      <main class="px-4 py-5 sm:px-6 lg:px-7">
        <header class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
          <div>
            <p class="text-[10px] uppercase tracking-[0.18em] text-slate-500">User Analytics</p>
            <h2 class="text-xl font-bold text-slate-900">Welcome, {{ authUserName }}</h2>
          </div>
          <form @submit.prevent="logout">
            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition">
              Logout
            </button>
          </form>
        </header>

        <section class="mt-4">
          <slot />
        </section>
      </main>
    </div>
  </MainLayout>
</template>

<script setup>
import { computed, reactive } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
  activeModule: {
    type: String,
    default: 'overview',
  },
});

const page = usePage();
const authUserName = computed(() => page.props?.auth?.user?.name || 'Member');
const activeKey = computed(() => String(props.activeModule || 'overview').toLowerCase());

const menus = [
  {
    label: 'Profile',
    items: [
      { key: 'overview', label: 'Dashboard Overview', url: '/dashboard' },
      { key: 'my-profile', label: 'My Profile', url: '/profiles' },
      { key: 'browse', label: 'Browse Profiles', url: '/browse' },
    ],
  },
  {
    label: 'Match Results',
    items: [
      { key: 'requests', label: 'Request Center', url: '/dashboard/requests' },
      { key: 'kundli-matching', label: 'Kundli Matching', url: '/kundli-history' },
      { key: 'kundli-history', label: 'Kundli History', url: '/kundli-history' },
    ],
  },
  {
    label: 'Membership',
    items: [
      { key: 'pricing', label: 'Plans & Pricing', url: '/pricing' },
    ],
  },
];

const openMenus = reactive(
  Object.fromEntries(
    menus.map((menu) => {
      const containsActiveItem = menu.items.some((item) => String(item.key || '').toLowerCase() === activeKey.value);
      return [menu.label, containsActiveItem];
    })
  )
);

const toggleMenu = (label) => {
  openMenus[label] = !openMenus[label];
};

const itemClass = (key) => {
  if (activeKey.value === String(key || '').toLowerCase()) {
    return 'bg-cyan-500/20 text-cyan-200';
  }

  return 'text-slate-300 hover:bg-slate-800 hover:text-white';
};

const logout = () => {
  router.post('/logout');
};
</script>
