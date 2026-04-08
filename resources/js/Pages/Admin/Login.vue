<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-gray-900 text-white flex items-center justify-center px-4">
    <div class="w-full max-w-md rounded-2xl border border-slate-700 bg-slate-900/80 p-6 shadow-2xl backdrop-blur">
      <p class="text-xs uppercase tracking-[0.2em] text-amber-300">Control Room</p>
      <h1 class="mt-2 text-3xl font-extrabold">Administrator Login</h1>
      <p class="mt-2 text-sm text-slate-300">Only admin and super admin accounts can sign in here.</p>

      <div v-if="statusMessage" class="mt-4 rounded-lg border border-emerald-300/30 bg-emerald-400/10 px-3 py-2 text-sm text-emerald-200">
        {{ statusMessage }}
      </div>

      <form class="mt-5 space-y-4" @submit.prevent="submit">
        <div>
          <label class="block text-sm font-semibold text-slate-200 mb-1">Admin Email</label>
          <input
            v-model="form.email"
            type="email"
            class="w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-amber-400"
            placeholder="admin@domain.com"
          />
          <p v-if="form.errors.email" class="mt-1 text-xs text-rose-300">{{ form.errors.email }}</p>
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-200 mb-1">Password</label>
          <input
            v-model="form.password"
            type="password"
            class="w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-amber-400"
            placeholder="Enter password"
          />
          <p v-if="form.errors.password" class="mt-1 text-xs text-rose-300">{{ form.errors.password }}</p>
        </div>

        <label class="inline-flex items-center gap-2 text-sm text-slate-300">
          <input v-model="form.remember" type="checkbox" class="h-4 w-4 rounded border-slate-500 bg-slate-700 text-amber-400" />
          Remember this device
        </label>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full rounded-lg bg-amber-400 px-4 py-2.5 font-bold text-slate-900 hover:bg-amber-300 transition disabled:opacity-70"
        >
          {{ form.processing ? 'Signing in...' : 'Login to Admin Panel' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

const page = usePage();
const statusMessage = computed(() => page.props.flash?.status || '');

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post('/admin/login');
};
</script>
