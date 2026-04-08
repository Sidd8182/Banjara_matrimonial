<template>
  <MainLayout>
    <section class="rounded-3xl bg-gradient-to-r from-rose-600 via-red-600 to-orange-500 text-white p-6 sm:p-10 shadow-lg">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <div class="lg:col-span-2">
          <p class="text-white/80 text-sm uppercase tracking-wide">Personalized Dashboard</p>
          <h1 class="mt-2 text-3xl sm:text-4xl font-extrabold">Welcome back, {{ userName }}!</h1>
          <p class="mt-3 text-white/90 max-w-2xl">
            Your compatible matches are ready. Complete your profile to improve match quality and response rate.
          </p>

          <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="rounded-xl bg-white/15 p-3">
              <p class="text-2xl font-bold">126</p>
              <p class="text-xs text-white/85">Profile Views</p>
            </div>
            <div class="rounded-xl bg-white/15 p-3">
              <p class="text-2xl font-bold">24</p>
              <p class="text-xs text-white/85">New Matches</p>
            </div>
            <div class="rounded-xl bg-white/15 p-3">
              <p class="text-2xl font-bold">9</p>
              <p class="text-xs text-white/85">New Messages</p>
            </div>
            <div class="rounded-xl bg-white/15 p-3">
              <p class="text-2xl font-bold">87%</p>
              <p class="text-xs text-white/85">Profile Strength</p>
            </div>
          </div>
        </div>

        <div class="rounded-2xl bg-white text-gray-900 p-5 shadow-md">
          <h2 class="text-lg font-bold">Profile Completion</h2>
          <p class="text-sm text-gray-600 mt-1">Complete remaining details to boost visibility.</p>
          <div class="mt-4 h-3 w-full rounded-full bg-gray-200 overflow-hidden">
            <div class="h-full w-[87%] bg-gradient-to-r from-emerald-500 to-lime-500"></div>
          </div>
          <p class="mt-2 text-sm font-semibold text-emerald-600">87% completed</p>
          <a href="/profiles" class="mt-4 inline-block w-full text-center rounded-lg bg-primary text-white py-2.5 font-semibold hover:bg-red-700 transition">
            Complete Profile
          </a>
        </div>
      </div>
    </section>

    <section class="mt-8 grid grid-cols-1 xl:grid-cols-3 gap-6">
      <div class="xl:col-span-2 rounded-2xl bg-white border border-gray-200 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-5">
          <h3 class="text-2xl font-bold text-gray-900">Recommended Matches</h3>
          <a href="/browse" class="text-primary font-semibold hover:underline">View all</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <article v-for="profile in recommendedProfiles" :key="profile.name" class="rounded-xl border border-gray-200 p-4 hover:shadow-md transition">
            <div class="h-36 rounded-xl" :class="profile.bgClass"></div>
            <h4 class="mt-3 font-bold text-gray-900">{{ profile.name }}, {{ profile.age }}</h4>
            <p class="text-sm text-gray-600">{{ profile.city }} • {{ profile.profession }}</p>
            <p class="mt-2 text-xs font-semibold text-emerald-600">{{ profile.compatibility }}% Match</p>
            <div class="mt-3 flex gap-2">
              <button class="flex-1 rounded-md border border-red-200 text-red-600 py-2 text-sm hover:bg-red-50 transition">Skip</button>
              <button class="flex-1 rounded-md bg-primary text-white py-2 text-sm hover:bg-red-700 transition">Connect</button>
            </div>
          </article>
        </div>
      </div>

      <div class="rounded-2xl bg-white border border-gray-200 p-6 shadow-sm">
        <h3 class="text-xl font-bold text-gray-900">Recent Activity</h3>
        <ul class="mt-4 space-y-4">
          <li v-for="activity in recentActivities" :key="activity.text" class="flex items-start gap-3">
            <span class="mt-1 h-2.5 w-2.5 rounded-full" :class="activity.dot"></span>
            <div>
              <p class="text-sm font-semibold text-gray-900">{{ activity.text }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ activity.time }}</p>
            </div>
          </li>
        </ul>

        <div class="mt-6 rounded-xl bg-gradient-to-r from-indigo-50 to-sky-50 border border-indigo-100 p-4">
          <p class="text-sm font-bold text-gray-900">Premium Insight</p>
          <p class="text-sm text-gray-600 mt-1">Members with full profiles get 3.2x more responses.</p>
          <a href="/pricing" class="mt-3 inline-block text-sm font-semibold text-indigo-600 hover:underline">Upgrade Plan</a>
        </div>
      </div>
    </section>

    <section class="mt-8 rounded-2xl bg-white border border-gray-200 p-6 shadow-sm">
      <div class="flex items-center justify-between mb-5">
        <h3 class="text-2xl font-bold text-gray-900">Quick Actions</h3>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="/browse" class="rounded-xl border border-gray-200 p-4 hover:bg-rose-50 transition">
          <p class="text-lg font-bold text-gray-900">Browse Profiles</p>
          <p class="text-sm text-gray-600 mt-1">Discover new matches by filters</p>
        </a>
        <a href="/messages" class="rounded-xl border border-gray-200 p-4 hover:bg-orange-50 transition">
          <p class="text-lg font-bold text-gray-900">Messages</p>
          <p class="text-sm text-gray-600 mt-1">Reply to pending conversations</p>
        </a>
        <a href="/profiles" class="rounded-xl border border-gray-200 p-4 hover:bg-emerald-50 transition">
          <p class="text-lg font-bold text-gray-900">My Profile</p>
          <p class="text-sm text-gray-600 mt-1">Update details and preferences</p>
        </a>
        <a href="/pricing" class="rounded-xl border border-gray-200 p-4 hover:bg-indigo-50 transition">
          <p class="text-lg font-bold text-gray-900">Membership</p>
          <p class="text-sm text-gray-600 mt-1">Compare plans and benefits</p>
        </a>
      </div>
    </section>
  </MainLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name || 'Member');

const recommendedProfiles = [
  { name: 'Priyanka', age: 27, city: 'Pune', profession: 'Software Engineer', compatibility: 92, bgClass: 'bg-gradient-to-br from-rose-100 to-amber-100' },
  { name: 'Aarav', age: 30, city: 'Bengaluru', profession: 'Architect', compatibility: 89, bgClass: 'bg-gradient-to-br from-sky-100 to-indigo-100' },
  { name: 'Neha', age: 26, city: 'Ahmedabad', profession: 'Doctor', compatibility: 94, bgClass: 'bg-gradient-to-br from-emerald-100 to-lime-100' },
  { name: 'Rohit', age: 29, city: 'Jaipur', profession: 'Business Owner', compatibility: 86, bgClass: 'bg-gradient-to-br from-orange-100 to-rose-100' },
  { name: 'Meera', age: 25, city: 'Mumbai', profession: 'CA', compatibility: 88, bgClass: 'bg-gradient-to-br from-purple-100 to-fuchsia-100' },
  { name: 'Karan', age: 31, city: 'Delhi', profession: 'Product Manager', compatibility: 90, bgClass: 'bg-gradient-to-br from-cyan-100 to-teal-100' },
];

const recentActivities = [
  { text: 'Anjali viewed your profile', time: '2 min ago', dot: 'bg-rose-500' },
  { text: 'New message from Karan', time: '10 min ago', dot: 'bg-orange-500' },
  { text: '3 new compatible matches found', time: '35 min ago', dot: 'bg-emerald-500' },
  { text: 'Your profile appeared in 14 searches', time: '1 hour ago', dot: 'bg-indigo-500' },
];
</script>
