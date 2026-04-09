<template>
  <MainLayout>
    <section class="mt-10 mb-12">
      <div class="text-center mb-8">
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900">Simple, Transparent Pricing</h1>
        <p class="mt-2 text-gray-600">Super admin managed packages with clear features and easy purchase flow.</p>
      </div>

      <div v-if="statusMessage" class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        {{ statusMessage }}
      </div>

      <div v-if="plans.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <article
          v-for="plan in plans"
          :key="plan.id"
          class="rounded-2xl bg-white p-6 shadow-sm relative"
          :class="plan.is_recommended ? 'border-2 border-primary shadow-md' : 'border border-gray-200'"
        >
          <span
            v-if="plan.is_recommended"
            class="absolute -top-3 right-4 rounded-full bg-primary text-white px-3 py-1 text-xs font-semibold"
          >
            Recommended
          </span>

          <h2 class="text-xl font-bold text-gray-900">{{ plan.name }}</h2>
          <p class="mt-2 text-3xl font-extrabold text-gray-900">₹{{ formatPrice(plan.price) }}</p>
          <p class="text-sm text-gray-500">{{ billingLabel(plan.billing_cycle) }}</p>
          <p v-if="plan.description" class="mt-3 text-sm text-gray-600">{{ plan.description }}</p>

          <ul class="mt-5 space-y-2 text-sm text-gray-700 min-h-[90px]">
            <li v-for="feature in plan.features" :key="feature.id">• {{ feature.feature_text }}</li>
            <li v-if="!plan.features.length" class="text-gray-500">Features will be updated soon.</li>
          </ul>

          <button
            v-if="isAuthenticated"
            class="mt-6 w-full rounded-lg px-4 py-2 font-semibold transition"
            :class="plan.is_recommended ? 'bg-primary text-white hover:bg-red-700' : 'border border-primary text-primary hover:bg-rose-50'"
            :disabled="processingPlanId === plan.id"
            @click="buyPlan(plan.id)"
          >
            {{ processingPlanId === plan.id ? 'Processing...' : 'Purchase Plan' }}
          </button>
          <a
            v-else
            href="/login"
            class="mt-6 w-full rounded-lg border border-primary text-primary px-4 py-2 font-semibold hover:bg-rose-50 transition text-center block"
          >
            Login to Purchase
          </a>
        </article>
      </div>

      <div v-else class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-gray-600">
        No pricing plans are live right now. Please check again shortly.
      </div>
    </section>
  </MainLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
  plans: {
    type: Array,
    default: () => [],
  },
  paymentConfig: {
    type: Object,
    default: () => ({
      enabled: false,
      gateway: 'razorpay',
      currency: 'INR',
      razorpayKeyId: '',
    }),
  },
});

const page = usePage();
const isAuthenticated = computed(() => Boolean(page.props.auth?.user));
const statusMessage = computed(() => page.props.flash?.status || '');
const processingPlanId = ref(null);

const loadRazorpayScript = async () => {
  if (window.Razorpay) {
    return true;
  }

  return new Promise((resolve) => {
    const script = document.createElement('script');
    script.src = 'https://checkout.razorpay.com/v1/checkout.js';
    script.onload = () => resolve(true);
    script.onerror = () => resolve(false);
    document.body.appendChild(script);
  });
};

const buyPlan = async (planId) => {
  if (processingPlanId.value) {
    return;
  }

  const selectedPlan = props.plans.find((plan) => plan.id === planId);
  if (!selectedPlan) {
    window.alert('Plan not found. Please refresh and try again.');
    return;
  }

  if (!props.paymentConfig?.enabled) {
    window.alert('Payment gateway is currently disabled. Please contact support.');
    return;
  }

  if ((props.paymentConfig?.gateway || '').toLowerCase() !== 'razorpay') {
    window.alert('Only Razorpay checkout is available right now.');
    return;
  }

  processingPlanId.value = planId;

  try {
    const loaded = await loadRazorpayScript();
    if (!loaded) {
      throw new Error('Unable to load Razorpay checkout.');
    }

    const orderResponse = await window.axios.post('/pricing/create-order', {
      plan_id: planId,
    });

    const orderPayload = orderResponse?.data;
    const options = {
      key: orderPayload.key,
      amount: orderPayload.order.amount,
      currency: orderPayload.order.currency || props.paymentConfig.currency || 'INR',
      name: 'Banjara Matrimonial',
      description: `${orderPayload.plan.name} Membership`,
      order_id: orderPayload.order.id,
      prefill: {
        name: orderPayload.user.name,
        email: orderPayload.user.email,
        contact: orderPayload.user.contact,
      },
      theme: {
        color: '#dc2626',
      },
      handler: async (response) => {
        const verifyResponse = await window.axios.post('/pricing/verify-payment', {
          plan_id: planId,
          razorpay_order_id: response.razorpay_order_id,
          razorpay_payment_id: response.razorpay_payment_id,
          razorpay_signature: response.razorpay_signature,
        });

        window.alert(verifyResponse?.data?.message || 'Payment successful.');
        router.visit('/dashboard');
      },
      modal: {
        ondismiss: () => {
          processingPlanId.value = null;
        },
      },
    };

    const paymentObject = new window.Razorpay(options);
    paymentObject.on('payment.failed', (failedResponse) => {
      const errorDescription = failedResponse?.error?.description || 'Payment failed. Please try again.';
      window.alert(errorDescription);
      processingPlanId.value = null;
    });

    paymentObject.open();
  } catch (error) {
    const errorMessage = error?.response?.data?.message || error?.message || 'Unable to start payment.';
    window.alert(errorMessage);
    processingPlanId.value = null;
  }
};

const billingLabel = (cycle) => {
  if (cycle === 'monthly') return 'per month';
  if (cycle === 'quarterly') return 'per quarter';
  if (cycle === 'yearly') return 'per year';
  if (cycle === 'lifetime') return 'one-time lifetime';
  return cycle;
};

const formatPrice = (value) => {
  const numeric = Number(value || 0);
  return numeric.toLocaleString('en-IN', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  });
};
</script>
