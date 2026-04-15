<template>
  <div class="flex flex-col min-h-screen bg-gray-50">
    <!-- Navigation -->
    <Navigation />

    <!-- Main Content -->
    <main
      class="flex-grow w-full py-10"
      :class="isFluid ? 'px-4 sm:px-6 lg:px-8' : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8'"
    >
      <slot />
    </main>

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script setup>
import Navigation from '@/Components/Navigation.vue';
import Footer from '@/Components/Footer.vue';

const props = defineProps({
  fluid: {
    type: Boolean,
    default: false,
  },
});

const isFluid = props.fluid;
</script>

