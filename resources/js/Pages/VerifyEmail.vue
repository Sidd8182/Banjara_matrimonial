<template>
  <MainLayout>
    <section class="mx-auto max-w-2xl rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 shadow-sm mt-8">
      <h1 class="text-2xl font-bold text-gray-900">Verify Your Email</h1>
      <p class="mt-3 text-sm text-gray-600">
        You are logged in, but your email verification is still pending. A verification link was sent to your email address.
      </p>

      <div v-if="statusMessage" class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        {{ statusMessage }}
      </div>

      <div class="mt-6 flex flex-wrap gap-3">
        <button
          type="button"
          class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition"
          :disabled="sending"
          @click="resendVerification"
        >
          {{ sending ? 'Sending...' : 'Resend Verification Email' }}
        </button>

        <Link
          href="/logout"
          method="post"
          as="button"
          class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition"
        >
          Logout
        </Link>
      </div>
    </section>
  </MainLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const page = usePage();
const sending = ref(false);
const statusMessage = computed(() => page.props.flash?.status || '');

const resendVerification = () => {
  sending.value = true;
  router.post('/email/verification-notification-auth', {}, {
    preserveScroll: true,
    onFinish: () => {
      sending.value = false;
    },
  });
};
</script>
