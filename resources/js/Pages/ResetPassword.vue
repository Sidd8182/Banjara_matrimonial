<template>
  <MainLayout>
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-lg">
      <h2 class="text-3xl font-bold text-gray-900 mb-3 text-center">Reset Password</h2>
      <p class="text-sm text-gray-600 text-center mb-6">Set a new password for your account.</p>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
          <input
            v-model="form.email"
            type="email"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
            required
          />
          <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
          <input
            v-model="form.password"
            type="password"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
            required
          />
          <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
            required
          />
        </div>

        <button type="submit" class="w-full btn-primary disabled:opacity-60" :disabled="form.processing">
          {{ form.processing ? 'Resetting...' : 'Reset Password' }}
        </button>
      </form>

      <p class="mt-6 text-center text-gray-600">
        Back to
        <a href="/login" class="text-primary font-semibold hover:underline">Login</a>
      </p>
    </div>
  </MainLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
  token: {
    type: String,
    required: true,
  },
  email: {
    type: String,
    default: '',
  },
});

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post('/reset-password');
};
</script>
