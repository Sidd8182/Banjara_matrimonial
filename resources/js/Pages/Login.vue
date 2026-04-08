<template>
  <MainLayout>
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-lg">
      <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">Login</h2>

      <p v-if="status" class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-2 text-sm text-emerald-700">
        {{ status }}
      </p>

      <!-- Form -->
      <form @submit.prevent="submit" class="space-y-4">
        <!-- Email -->
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

        <!-- Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
          <input 
            v-model="form.password"
            type="password" 
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
            placeholder="Enter password"
            required
          />
          <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
          <label class="flex items-center text-sm">
            <input v-model="form.remember" type="checkbox" class="rounded" />
            <span class="ml-2 text-gray-600">Remember me</span>
          </label>
          <a href="/forgot-password" class="text-sm text-primary hover:underline">Forgot password?</a>
          <button type="button" class="text-sm text-primary hover:underline" @click="resendVerification" :disabled="resendForm.processing">
            Resend verification
          </button>
        </div>

        <!-- Submit Button -->
        <button 
          type="submit"
          class="w-full btn-primary disabled:opacity-60"
          :disabled="form.processing"
        >
          {{ form.processing ? 'Logging in...' : 'Login' }}
        </button>
      </form>

      <!-- Register Link -->
      <p class="mt-6 text-center text-gray-600">
        Don't have an account? 
        <a href="/register" class="text-primary font-semibold hover:underline">Register Now</a>
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
  password: '',
  remember: false,
});

const resendForm = useForm({
  email: '',
});

const submit = () => {
  form.post('/login');
};

const resendVerification = () => {
  resendForm.email = form.email;
  resendForm.post('/email/verification-notification');
};
</script>
