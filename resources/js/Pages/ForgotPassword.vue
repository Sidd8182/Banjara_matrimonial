<template>
  <MainLayout>
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-lg">
      <h2 class="text-3xl font-bold text-gray-900 mb-3 text-center">Forgot Password</h2>
      <p class="text-sm text-gray-600 text-center mb-6">Enter your email and we will send a reset link.</p>

      <p v-if="status" class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-2 text-sm text-emerald-700">
        {{ status }}
      </p>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
          <input
            v-model="form.email"
            type="email"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
            placeholder="your@email.com"
            required
          />
          <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
        </div>

        <button type="submit" class="w-full btn-primary disabled:opacity-60" :disabled="form.processing">
          {{ form.processing ? 'Sending...' : 'Send Reset Link' }}
        </button>
      </form>

      <p class="mt-6 text-center text-gray-600">
        Remember password?
        <a href="/login" class="text-primary font-semibold hover:underline">Back to Login</a>
      </p>
    </div>
  </MainLayout>
</template>

<script setup>
import { computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const page = usePage();
const status = computed(() => page.props.flash?.status || '');

const form = useForm({
  email: '',
});

const submit = () => {
  form.post('/forgot-password');
};
</script>
