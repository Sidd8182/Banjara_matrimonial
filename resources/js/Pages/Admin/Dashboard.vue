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
              <div
                v-for="item in menu.items"
                :key="`${menu.label}-${item.label}`"
              >
                <a
                  href="#"
                  class="block rounded-md px-2.5 py-1.5 text-[12px] text-slate-300 hover:bg-slate-800 hover:text-white"
                >
                  {{ item.label }}
                </a>
                <div v-if="item.children" class="ml-3 mt-1 space-y-1 border-l border-slate-700 pl-2">
                  <a
                    v-for="child in item.children"
                    :key="`${item.label}-${child}`"
                    href="#"
                    class="block rounded-md px-2 py-1 text-[11px] text-slate-400 hover:bg-slate-800 hover:text-slate-100"
                  >
                    {{ child }}
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </aside>

      <main class="px-4 sm:px-6 lg:px-7 py-5">
        <header class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm flex flex-wrap items-center justify-between gap-3">
          <div>
            <p class="text-[10px] uppercase tracking-[0.18em] text-slate-500">Operational Analytics</p>
            <h2 class="text-xl font-bold text-slate-900">Super Administrator Dashboard</h2>
          </div>
          <form @submit.prevent="logout">
            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition">
              Logout
            </button>
          </form>
        </header>

        <section class="mt-4 grid grid-cols-2 xl:grid-cols-6 gap-3">
          <article class="rounded-xl bg-white border border-slate-200 p-3 shadow-sm">
            <p class="text-[11px] text-slate-500">Total Users</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ stats.totalUsers }}</p>
          </article>
          <article class="rounded-xl bg-white border border-slate-200 p-3 shadow-sm">
            <p class="text-[11px] text-slate-500">Verified</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ stats.verifiedUsers }}</p>
          </article>
          <article class="rounded-xl bg-white border border-slate-200 p-3 shadow-sm">
            <p class="text-[11px] text-slate-500">Completed</p>
            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ stats.profileCompletedUsers }}</p>
          </article>
          <article class="rounded-xl bg-white border border-slate-200 p-3 shadow-sm">
            <p class="text-[11px] text-slate-500">Male</p>
            <p class="text-2xl font-bold text-cyan-600 mt-1">{{ stats.maleUsers }}</p>
          </article>
          <article class="rounded-xl bg-white border border-slate-200 p-3 shadow-sm">
            <p class="text-[11px] text-slate-500">Female</p>
            <p class="text-2xl font-bold text-rose-600 mt-1">{{ stats.femaleUsers }}</p>
          </article>
          <article class="rounded-xl bg-white border border-slate-200 p-3 shadow-sm">
            <p class="text-[11px] text-slate-500">Admins</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ stats.adminUsers }}</p>
          </article>
        </section>

        <section class="mt-4 grid grid-cols-1 xl:grid-cols-3 gap-4">
          <article class="xl:col-span-2 rounded-2xl bg-white border border-slate-200 p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-sm font-bold text-slate-900">User Growth Trend</h3>
              <span class="text-[11px] text-slate-500">Last 6 Months</span>
            </div>

            <div class="h-44 rounded-xl bg-gradient-to-b from-sky-50 to-white border border-slate-100 p-3">
              <svg viewBox="0 0 600 170" class="w-full h-full">
                <polyline
                  fill="none"
                  stroke="#0f172a"
                  stroke-width="3"
                  stroke-linecap="round"
                  points="20,140 110,125 200,112 290,98 380,86 470,74 560,58"
                />
                <polyline
                  fill="none"
                  stroke="#06b6d4"
                  stroke-width="2"
                  stroke-dasharray="4 4"
                  points="20,150 110,140 200,132 290,122 380,115 470,109 560,100"
                />
              </svg>
            </div>

            <div class="mt-3 grid grid-cols-6 gap-2 text-[10px] text-slate-500">
              <span v-for="m in ['Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr']" :key="m" class="text-center">{{ m }}</span>
            </div>
          </article>

          <article class="rounded-2xl bg-white border border-slate-200 p-4 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900">Audience Split</h3>
            <div class="mt-3 flex items-center justify-center">
              <div class="h-36 w-36 rounded-full relative" :style="donutStyle">
                <div class="absolute inset-5 rounded-full bg-white grid place-items-center text-center">
                  <p class="text-[10px] text-slate-500">Verified</p>
                  <p class="text-lg font-bold text-slate-900">{{ verifiedRate }}%</p>
                </div>
              </div>
            </div>
            <div class="mt-3 space-y-1.5 text-[11px]">
              <div class="flex items-center justify-between"><span class="text-slate-500">Male Users</span><span class="font-semibold text-cyan-600">{{ stats.maleUsers }}</span></div>
              <div class="flex items-center justify-between"><span class="text-slate-500">Female Users</span><span class="font-semibold text-rose-600">{{ stats.femaleUsers }}</span></div>
              <div class="flex items-center justify-between"><span class="text-slate-500">Admins</span><span class="font-semibold text-amber-600">{{ stats.adminUsers }}</span></div>
            </div>
          </article>
        </section>

        <section class="mt-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
          <div class="px-4 py-3 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
            <h3 class="text-sm font-bold text-slate-900">Advanced User Table</h3>
            <div class="flex items-center gap-2">
              <input
                v-model="search"
                type="text"
                placeholder="Search user, email, role"
                class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-200"
              />
              <select v-model="roleFilter" class="rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-200">
                <option value="all">All Roles</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
                <option value="super_admin">Super Admin</option>
              </select>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-slate-50 text-[10px] uppercase tracking-wide text-slate-500">
                <tr>
                  <th class="px-4 py-2 text-left">Name</th>
                  <th class="px-4 py-2 text-left">Email</th>
                  <th class="px-4 py-2 text-left">Gender</th>
                  <th class="px-4 py-2 text-left">Role</th>
                  <th class="px-4 py-2 text-left">Verification</th>
                  <th class="px-4 py-2 text-left">Created</th>
                  <th class="px-4 py-2 text-right">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 text-[12px]">
                <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-slate-50/60">
                  <td class="px-4 py-2.5 font-semibold text-slate-900">{{ user.name }}</td>
                  <td class="px-4 py-2.5 text-slate-600">{{ user.email }}</td>
                  <td class="px-4 py-2.5">{{ user.gender || '-' }}</td>
                  <td class="px-4 py-2.5">
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold" :class="roleClass(user.role)">
                      {{ user.role }}
                    </span>
                  </td>
                  <td class="px-4 py-2.5">
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold" :class="verificationClass(user)">
                      {{ userVerification(user) }}
                    </span>
                  </td>
                  <td class="px-4 py-2.5 text-slate-600">{{ formatDate(user.created_at) }}</td>
                  <td class="px-4 py-2.5 text-right">
                    <button class="rounded-md border border-slate-300 px-2 py-1 text-[10px] font-semibold text-slate-700 hover:bg-slate-100">View</button>
                  </td>
                </tr>
                <tr v-if="!filteredUsers.length">
                  <td colspan="7" class="px-4 py-6 text-center text-xs text-slate-500">No matching records found.</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="px-4 py-3 border-t border-slate-200 flex items-center justify-between text-[11px] text-slate-500">
            <p>Showing {{ filteredUsers.length }} of {{ recentUsers.length }} users</p>
            <div class="flex items-center gap-1">
              <button class="rounded border border-slate-300 px-2 py-1 hover:bg-slate-100">Prev</button>
              <button class="rounded border border-slate-300 px-2 py-1 bg-slate-900 text-white">1</button>
              <button class="rounded border border-slate-300 px-2 py-1 hover:bg-slate-100">2</button>
              <button class="rounded border border-slate-300 px-2 py-1 hover:bg-slate-100">Next</button>
            </div>
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
  logoutAction: {
    type: String,
    required: true,
  },
  stats: {
    type: Object,
    required: true,
  },
  recentUsers: {
    type: Array,
    default: () => [],
  },
});

const search = ref('');
const roleFilter = ref('all');

const sidebarMenus = [
  {
    label: 'User Management',
    items: [
      { label: 'All Members', children: ['Active', 'Blocked', 'Pending Verification'] },
      { label: 'Premium Users' },
      { label: 'Admin Accounts' },
    ],
  },
  {
    label: 'Content Moderation',
    items: [
      { label: 'Profile Approvals' },
      { label: 'Photo Reports' },
      { label: 'Message Monitoring' },
    ],
  },
  {
    label: 'Business Analytics',
    items: [
      { label: 'Revenue Trends' },
      { label: 'Subscription Funnel' },
      { label: 'Geo Intelligence', children: ['City Heatmap', 'State Distribution'] },
    ],
  },
  {
    label: 'Platform Settings',
    items: [
      { label: 'SMTP & Notifications' },
      { label: 'Membership Plans' },
      { label: 'Security Controls' },
    ],
  },
];

const openMenus = reactive({
  'User Management': true,
  'Content Moderation': true,
  'Business Analytics': false,
  'Platform Settings': false,
});

const toggleMenu = (label) => {
  openMenus[label] = !openMenus[label];
};

const verifiedRate = computed(() => {
  if (!props.stats.totalUsers) {
    return 0;
  }

  return Math.round((props.stats.verifiedUsers / props.stats.totalUsers) * 100);
});

const donutStyle = computed(() => {
  const female = props.stats.femaleUsers || 0;
  const male = props.stats.maleUsers || 0;
  const admin = props.stats.adminUsers || 0;
  const total = female + male + admin || 1;

  const femaleAngle = Math.round((female / total) * 360);
  const maleAngle = Math.round((male / total) * 360);
  const adminAngle = 360 - femaleAngle - maleAngle;

  return {
    background: `conic-gradient(#f43f5e 0 ${femaleAngle}deg, #06b6d4 ${femaleAngle}deg ${femaleAngle + maleAngle}deg, #f59e0b ${femaleAngle + maleAngle}deg ${femaleAngle + maleAngle + adminAngle}deg)`,
  };
});

const filteredUsers = computed(() => {
  const q = search.value.toLowerCase().trim();

  return props.recentUsers.filter((user) => {
    const roleOk = roleFilter.value === 'all' ? true : user.role === roleFilter.value;
    const textOk = !q
      ? true
      : [user.name, user.email, user.role, user.gender].filter(Boolean).some((v) => String(v).toLowerCase().includes(q));

    return roleOk && textOk;
  });
});

const logout = () => {
  router.post(props.logoutAction);
};

const roleClass = (role) => {
  if (role === 'super_admin') return 'bg-purple-100 text-purple-700';
  if (role === 'admin') return 'bg-amber-100 text-amber-700';
  return 'bg-slate-100 text-slate-700';
};

const userVerification = (user) => {
  return user.email_verified_at ? 'Verified' : 'Pending';
};

const verificationClass = (user) => {
  return user.email_verified_at ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700';
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
</script>
