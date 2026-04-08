<template>
  <MainLayout>
    <section class="mt-10 mb-12">
      <div class="text-center mb-8">
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900">Simple, Transparent Pricing</h1>
        <p class="mt-2 text-gray-600">Choose a plan that matches your partner search journey.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
          <h2 class="text-xl font-bold text-gray-900">Free</h2>
          <p class="mt-2 text-3xl font-extrabold text-gray-900">$0</p>
          <p class="text-sm text-gray-500">Forever</p>
          <ul class="mt-5 space-y-2 text-sm text-gray-700">
            <li>Browse limited profiles</li>
            <li>Send interests</li>
            <li>Basic filters</li>
          </ul>
          <button class="mt-6 w-full rounded-lg border border-primary text-primary px-4 py-2 font-semibold hover:bg-rose-50 transition">Get Started</button>
        </article>

        <article class="rounded-2xl border-2 border-primary bg-white p-6 shadow-md relative">
          <span class="absolute -top-3 right-4 rounded-full bg-primary text-white px-3 py-1 text-xs font-semibold">Popular</span>
          <h2 class="text-xl font-bold text-gray-900">Premium</h2>
          <p class="mt-2 text-3xl font-extrabold text-gray-900">$19</p>
          <p class="text-sm text-gray-500">per month</p>
          <ul class="mt-5 space-y-2 text-sm text-gray-700">
            <li>Unlimited profile views</li>
            <li>Direct messaging</li>
            <li>Priority visibility</li>
          </ul>
          <button class="mt-6 w-full rounded-lg bg-primary text-white px-4 py-2 font-semibold hover:bg-red-700 transition">Choose Premium</button>
        </article>

        <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
          <h2 class="text-xl font-bold text-gray-900">Elite</h2>
          <p class="mt-2 text-3xl font-extrabold text-gray-900">$49</p>
          <p class="text-sm text-gray-500">per month</p>
          <ul class="mt-5 space-y-2 text-sm text-gray-700">
            <li>Everything in Premium</li>
            <li>Relationship manager</li>
            <li>Concierge matchmaking</li>
          </ul>
          <button class="mt-6 w-full rounded-lg border border-primary text-primary px-4 py-2 font-semibold hover:bg-rose-50 transition">Contact Sales</button>
        </article>
      </div>
    </section>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
</script>
