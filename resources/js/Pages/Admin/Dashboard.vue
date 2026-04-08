<template>
  <div class="min-h-screen bg-slate-100">
    <header class="bg-slate-900 text-white">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
        <div>
          <p class="text-xs uppercase tracking-[0.2em] text-amber-300">Banjara Command Center</p>
          <h1 class="text-2xl font-extrabold">Super Administrator Dashboard</h1>
        </div>
        <form @submit.prevent="logout">
          <button type="submit" class="rounded-lg bg-amber-400 px-4 py-2 font-semibold text-slate-900 hover:bg-amber-300 transition">
            Logout
          </button>
        </form>
      </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        <article class="rounded-xl bg-white border border-gray-200 p-5 shadow-sm">
          <p class="text-sm text-gray-500">Total Users</p>
          <p class="text-3xl font-extrabold text-slate-900 mt-1">{{ stats.totalUsers }}</p>
        </article>
        <article class="rounded-xl bg-white border border-gray-200 p-5 shadow-sm">
          <p class="text-sm text-gray-500">Verified Users</p>
          <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ stats.verifiedUsers }}</p>
        </article>
        <article class="rounded-xl bg-white border border-gray-200 p-5 shadow-sm">
          <p class="text-sm text-gray-500">Completed Profiles</p>
          <p class="text-3xl font-extrabold text-indigo-600 mt-1">{{ stats.profileCompletedUsers }}</p>
        </article>
        <article class="rounded-xl bg-white border border-gray-200 p-5 shadow-sm">
          <p class="text-sm text-gray-500">Male Users</p>
          <p class="text-3xl font-extrabold text-cyan-600 mt-1">{{ stats.maleUsers }}</p>
        </article>
        <article class="rounded-xl bg-white border border-gray-200 p-5 shadow-sm">
          <p class="text-sm text-gray-500">Female Users</p>
          <p class="text-3xl font-extrabold text-rose-600 mt-1">{{ stats.femaleUsers }}</p>
        </article>
        <article class="rounded-xl bg-white border border-gray-200 p-5 shadow-sm">
          <p class="text-sm text-gray-500">Admin Accounts</p>
          <p class="text-3xl font-extrabold text-amber-600 mt-1">{{ stats.adminUsers }}</p>
        </article>
      </div>

      <section class="mt-6 rounded-2xl bg-white border border-gray-200 p-5 shadow-sm">
        <h2 class="text-xl font-bold text-gray-900">Recent Registrations</h2>
        <div class="mt-4 overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Gender</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Created</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="user in recentUsers" :key="user.id">
                <td class="px-3 py-2 text-sm text-gray-900 font-semibold">{{ user.name }}</td>
                <td class="px-3 py-2 text-sm text-gray-700">{{ user.email }}</td>
                <td class="px-3 py-2 text-sm text-gray-700">{{ user.gender || '-' }}</td>
                <td class="px-3 py-2 text-sm">
                  <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ user.role }}</span>
                </td>
                <td class="px-3 py-2 text-sm text-gray-700">{{ formatDate(user.created_at) }}</td>
              </tr>
              <tr v-if="!recentUsers.length">
                <td colspan="5" class="px-3 py-6 text-center text-sm text-gray-500">No users found yet.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';

defineProps({
  stats: {
    type: Object,
    required: true,
  },
  recentUsers: {
    type: Array,
    default: () => [],
  },
});

const logout = () => {
  router.post('/admin/logout');
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
