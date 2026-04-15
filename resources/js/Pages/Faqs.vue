<template>
  <MainLayout>
    <section class="rounded-3xl bg-gradient-to-r from-rose-600 via-red-500 to-orange-500 p-8 sm:p-12 text-white shadow-lg">
      <p class="text-xs tracking-[0.3em] uppercase text-white/80">Support</p>
      <h1 class="mt-2 text-3xl sm:text-5xl font-black">Frequently Asked Questions</h1>
      <p class="mt-3 text-white/90 max-w-3xl">Common questions about profiles, membership, privacy and matchmaking.</p>
    </section>

    <section class="mt-8 rounded-2xl border border-gray-200 bg-white p-5 sm:p-7 shadow-sm">
      <div class="space-y-3">
        <article v-for="(faq, index) in faqItems" :key="`${faq.question}-${index}`" class="rounded-xl border border-gray-200 bg-gray-50">
          <button
            type="button"
            class="w-full px-5 py-4 text-left flex items-center justify-between gap-3"
            @click="toggle(index)"
          >
            <span class="font-semibold text-gray-900">{{ faq.question }}</span>
            <span class="text-2xl text-gray-400 leading-none" :class="openIndex === index ? 'rotate-45' : ''">+</span>
          </button>
          <div v-if="openIndex === index" class="px-5 pb-4 text-sm text-gray-700">{{ faq.answer }}</div>
        </article>
      </div>

      <p v-if="!faqItems.length" class="text-sm text-gray-500">No FAQs available right now.</p>
    </section>
  </MainLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
  faqs: {
    type: Array,
    default: () => [],
  },
});

const fallbackFaqs = [
  {
    question: 'How do I register on Banjara Matrimonial?',
    answer: 'Create your account, verify your contact details and complete profile steps to receive matches.',
  },
  {
    question: 'Can free users view full profile details?',
    answer: 'No. Full details like family and contact information are available to paid members only.',
  },
];

const faqItems = computed(() => (props.faqs?.length ? props.faqs : fallbackFaqs));
const openIndex = ref(null);

const toggle = (index) => {
  openIndex.value = openIndex.value === index ? null : index;
};
</script>
