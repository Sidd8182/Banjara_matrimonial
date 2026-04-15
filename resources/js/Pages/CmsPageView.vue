<template>
  <MainLayout>
    <section class="rounded-3xl bg-gradient-to-r from-rose-700 via-red-600 to-orange-500 p-8 sm:p-12 text-white shadow-lg">
      <p class="text-xs uppercase tracking-[0.25em] text-white/80">Published Page</p>
      <h1 class="mt-2 text-3xl sm:text-5xl font-black">{{ pageData.title }}</h1>
      <p class="mt-2 text-white/90">Slug: /pages/{{ pageData.slug }}</p>
    </section>

    <section class="mt-8 space-y-5">
      <article
        v-for="block in contentBlocks"
        :key="`block-${block.id}`"
        class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-7 shadow-sm"
      >
        <h2 v-if="block.title" class="text-xl sm:text-2xl font-bold text-gray-900">{{ block.title }}</h2>
        <p class="mt-3 text-sm sm:text-base text-gray-700 whitespace-pre-line">{{ block.body || 'No content' }}</p>
      </article>

      <article v-if="faqBlocks.length" class="rounded-2xl border border-emerald-100 bg-gradient-to-br from-white to-emerald-50 p-5 sm:p-7 shadow-sm">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Frequently Asked Questions</h2>
        <div class="mt-4 space-y-3">
          <div v-for="(faq, index) in faqBlocks" :key="`faq-${faq.id}`" class="rounded-xl border border-gray-200 bg-white">
            <button type="button" class="w-full px-4 py-3 text-left flex items-center justify-between" @click="toggleFaq(index)">
              <span class="font-semibold text-gray-900">{{ faq.title }}</span>
              <span class="text-2xl text-gray-400 leading-none" :class="openFaqIndex === index ? 'rotate-45' : ''">+</span>
            </button>
            <div v-if="openFaqIndex === index" class="px-4 pb-4 text-sm text-gray-700">{{ faq.body }}</div>
          </div>
        </div>
      </article>

      <article v-if="!sections.length" class="rounded-2xl border border-gray-200 bg-white p-6 text-sm text-gray-600">
        No sections have been published on this page yet.
      </article>
    </section>
  </MainLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
  page: {
    type: Object,
    required: true,
  },
  sections: {
    type: Array,
    default: () => [],
  },
});

const pageData = computed(() => props.page || {});
const sections = computed(() => props.sections || []);

const faqBlocks = computed(() => {
  return sections.value.filter((item) => (item.sectionType || '').toLowerCase() === 'faq');
});

const contentBlocks = computed(() => {
  return sections.value.filter((item) => (item.sectionType || '').toLowerCase() !== 'faq');
});

const openFaqIndex = ref(null);

const toggleFaq = (index) => {
  openFaqIndex.value = openFaqIndex.value === index ? null : index;
};
</script>
