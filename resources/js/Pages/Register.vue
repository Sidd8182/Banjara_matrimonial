<template>
  <MainLayout>
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-lg">
      <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">Create Account</h2>

      <p v-if="status" class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-2 text-sm text-emerald-700">
        {{ status }}
      </p>

      <!-- Form -->
      <form @submit.prevent="submit" class="space-y-4">
        <!-- Name -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
          <input 
            v-model="form.name"
            type="text" 
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
            placeholder="Enter your name"
            required
          />
          <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
        </div>

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

        <!-- Phone -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
          <input 
            v-model="form.phone"
            type="tel" 
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
            placeholder="+91 98765 43210"
            required
          />
          <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
        </div>

        <!-- Gender -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
          <select 
            v-model="form.gender"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
            required
          >
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
          </select>
          <p v-if="form.errors.gender" class="mt-1 text-sm text-red-600">{{ form.errors.gender }}</p>
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

        <!-- Confirm Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
          <input 
            v-model="form.password_confirmation"
            type="password" 
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
            placeholder="Confirm password"
            required
          />
        </div>

        <!-- Terms -->
        <div class="flex items-center">
          <input type="checkbox" id="terms" class="rounded" required />
          <label for="terms" class="ml-2 text-sm text-gray-600">
            I agree to the Terms & Conditions
          </label>
        </div>

        <!-- Submit Button -->
        <button 
          type="submit"
          class="w-full btn-primary disabled:opacity-60"
          :disabled="form.processing"
        >
          {{ form.processing ? 'Creating...' : 'Create Account' }}
        </button>
      </form>

      <!-- Login Link -->
      <p class="mt-6 text-center text-gray-600">
        Already have an account? 
        <a href="/login" class="text-primary font-semibold hover:underline">Login</a>
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
  name: '',
  email: '',
  phone: '',
  gender: '',
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post('/register');
};
</script>
