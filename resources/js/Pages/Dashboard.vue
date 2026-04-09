<template>
  <MainLayout>
    <section class="rounded-3xl bg-gradient-to-r from-rose-600 via-red-600 to-orange-500 text-white p-6 sm:p-10 shadow-lg">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <div class="lg:col-span-2">
          <p class="text-white/80 text-sm uppercase tracking-wide">Personalized Dashboard</p>
          <h1 class="mt-2 text-3xl sm:text-4xl font-extrabold">Welcome back, {{ userName }}!</h1>
          <p class="mt-3">
            <span class="inline-flex items-center rounded-full border border-white/40 bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white">
              {{ membershipBadgeLabel }}
            </span>
          </p>
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
          <div class="flex items-center gap-3">
            <img
              :src="profileImageUrl"
              alt="Profile"
              class="h-16 w-16 rounded-full border border-gray-200 object-cover"
            />
            <div>
              <h2 class="text-lg font-bold">{{ profileDisplayName }}</h2>
              <p class="text-xs text-gray-500">{{ profileIdLabel }}</p>
            </div>
          </div>
          <p class="text-sm text-gray-600 mt-3">Complete remaining details to boost visibility.</p>
          <div class="mt-4 h-3 w-full rounded-full bg-gray-200 overflow-hidden">
            <div class="h-full bg-gradient-to-r from-emerald-500 to-lime-500" :style="{ width: `${profileCompletionPercent}%` }"></div>
          </div>
          <p class="mt-2 text-sm font-semibold text-emerald-600">{{ profileCompletionPercent }}% completed</p>
          <a href="/profiles" class="mt-4 inline-block w-full text-center rounded-lg bg-primary text-white py-2.5 font-semibold hover:bg-red-700 transition">
            Complete Profile
          </a>
          <a href="#my-media" class="mt-2 inline-block w-full text-center rounded-lg border border-gray-300 text-gray-700 py-2.5 font-semibold hover:bg-gray-50 transition">
            View Uploaded Media
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
      <div class="mb-5 rounded-xl border border-indigo-100 bg-indigo-50 p-4">
        <p class="text-xs uppercase tracking-wide text-indigo-700 font-semibold">Membership Status</p>
        <p class="mt-1 text-lg font-bold text-gray-900">{{ membershipStatusLabel }}</p>
        <p class="mt-1 text-sm text-gray-600">
          {{ membershipPlanLabel }}
          <span v-if="membershipEndDateLabel"> • Valid till {{ membershipEndDateLabel }}</span>
        </p>
      </div>

      <div id="my-media" class="mb-6 rounded-xl border border-gray-200 bg-gray-50 p-4">
        <div class="flex items-center justify-between gap-3 mb-3">
          <h3 class="text-lg font-bold text-gray-900">My Uploaded Media</h3>
          <a href="/profiles" class="text-sm font-semibold text-primary hover:underline">Edit Media</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
          <div class="rounded-lg border border-gray-200 bg-white p-3">
            <div class="mb-2 flex items-center justify-between gap-2">
              <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Photo Gallery ({{ allMediaImages.length }})</p>
              <button
                type="button"
                class="rounded-md border border-indigo-200 px-2.5 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-50 disabled:opacity-50"
                :disabled="!allMediaImages.length"
                @click="openGalleryModal(0)"
              >
                View Slideshow
              </button>
            </div>

            <div v-if="allMediaImages.length" class="grid grid-cols-2 sm:grid-cols-3 gap-2">
              <button
                v-for="(item, index) in allMediaImages.slice(0, 6)"
                :key="item.id"
                type="button"
                class="group relative overflow-hidden rounded-lg border border-gray-200"
                @click="openGalleryModal(index)"
              >
                <img :src="item.url" alt="Gallery" class="h-24 w-full object-cover transition group-hover:scale-105" />
                <span class="absolute inset-0 hidden items-center justify-center bg-black/35 text-xs font-semibold text-white group-hover:flex">View</span>
              </button>
            </div>

            <div v-else class="h-40 rounded-lg border border-dashed border-gray-300 text-gray-500 text-sm flex items-center justify-center">
              Abhi tak image upload nahi hui hai.
            </div>
          </div>

          <div class="rounded-lg border border-gray-200 bg-white p-3">
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Video Intro</p>
            <video v-if="videoIntroUrl" :src="videoIntroUrl" controls class="h-40 w-full rounded-lg border border-gray-200 bg-black"></video>
            <div v-else class="h-40 rounded-lg border border-dashed border-gray-300 text-gray-500 text-sm flex items-center justify-center">
              Video intro upload nahi hua hai.
            </div>
          </div>
        </div>

        <div class="mt-4 rounded-lg border border-gray-200 bg-white p-3">
          <div class="mb-2 flex items-center justify-between gap-2">
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Gallery Images ({{ galleryItems.length }})</p>
            <button
              type="button"
              class="rounded-md border border-indigo-200 px-2.5 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-50 disabled:opacity-50"
              :disabled="!allMediaImages.length"
              @click="openGalleryModal(0)"
            >
              View
            </button>
          </div>
          <div v-if="galleryItems.length" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2">
            <button
              v-for="item in galleryItems"
              :key="item.id"
              type="button"
              class="group relative overflow-hidden rounded-lg border border-gray-200"
              @click="openGalleryByUrl(item.url)"
            >
              <img
                :src="item.url"
                alt="Gallery"
                class="h-24 w-full object-cover transition group-hover:scale-105"
              />
              <span class="absolute inset-0 hidden items-center justify-center bg-black/35 text-xs font-semibold text-white group-hover:flex">View</span>
            </button>
          </div>
          <p v-else class="text-sm text-gray-500">Gallery images upload nahi hui hain.</p>
        </div>
      </div>

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

    <div v-if="isGalleryOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4" @click.self="closeGalleryModal">
      <button
        type="button"
        class="absolute right-4 top-4 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold text-white hover:bg-white/30"
        @click="closeGalleryModal"
      >
        Close
      </button>

      <div class="w-full max-w-5xl">
        <div
          class="rounded-xl bg-black p-2"
          @touchstart="onModalTouchStart"
          @touchend="onModalTouchEnd"
        >
          <img :src="activeGalleryImage?.url" alt="Gallery Preview" class="max-h-[75vh] w-full rounded-lg object-contain" />
        </div>

        <div class="mt-3 flex items-center justify-between">
          <button type="button" class="rounded-md bg-white/15 px-3 py-2 text-sm font-semibold text-white hover:bg-white/30" @click="prevGalleryImage">
            Previous
          </button>
          <div class="flex items-center gap-2">
            <p class="text-sm text-white">{{ activeMediaIndex + 1 }} / {{ allMediaImages.length }}</p>
            <button
              type="button"
              class="rounded-md bg-white/15 px-2.5 py-1 text-xs font-semibold text-white hover:bg-white/30"
              @click="toggleAutoPlay"
            >
              {{ isAutoPlayEnabled ? 'Pause Auto' : 'Auto Play' }}
            </button>
          </div>
          <button type="button" class="rounded-md bg-white/15 px-3 py-2 text-sm font-semibold text-white hover:bg-white/30" @click="nextGalleryImage">
            Next
          </button>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
  subscription: {
    type: Object,
    default: null,
  },
  dashboardProfile: {
    type: Object,
    default: null,
  },
});

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name || 'Member');
const membershipStatusLabel = computed(() => {
  const status = (props.subscription?.status || 'inactive').toLowerCase();

  if (status === 'active') return 'Active Premium Membership';
  if (status === 'pending') return 'Payment Pending';
  if (status === 'failed') return 'Payment Failed';
  if (status === 'expired') return 'Expired Membership';

  return 'No Active Membership';
});

const membershipBadgeLabel = computed(() => {
  if (props.subscription?.status === 'active' && props.subscription?.plan?.name) {
    return `${props.subscription.plan.name} Member`;
  }

  if (props.subscription?.status === 'pending') {
    return 'Payment Pending';
  }

  return 'Free Member';
});

const membershipPlanLabel = computed(() => {
  return props.subscription?.plan?.name
    ? `Plan: ${props.subscription.plan.name}`
    : 'Plan: Free User';
});

const membershipEndDateLabel = computed(() => {
  if (!props.subscription?.ends_at) {
    return '';
  }

  return new Date(props.subscription.ends_at).toLocaleDateString('en-IN', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  });
});

const profilePictureMediaUrl = computed(() => props.dashboardProfile?.profile_picture_url || '');

const profileImageUrl = computed(() => {
  return props.dashboardProfile?.profile_picture_url || '/images/default-avatar.svg';
});

const videoIntroUrl = computed(() => props.dashboardProfile?.video_intro_url || '');
const galleryItems = computed(() => props.dashboardProfile?.gallery || []);
const allMediaImages = computed(() => {
  const items = [];

  if (profilePictureMediaUrl.value) {
    items.push({
      id: 'profile-picture',
      url: profilePictureMediaUrl.value,
    });
  }

  (galleryItems.value || []).forEach((item) => {
    if (!item?.url) {
      return;
    }

    if (items.some((existing) => existing.url === item.url)) {
      return;
    }

    items.push({
      id: `gallery-${item.id}`,
      url: item.url,
    });
  });

  return items;
});

const isGalleryOpen = ref(false);
const activeMediaIndex = ref(0);
const activeGalleryImage = computed(() => allMediaImages.value[activeMediaIndex.value] || null);
const isAutoPlayEnabled = ref(true);
const modalTouchStartX = ref(0);
let autoPlayTimer = null;

const openGalleryModal = (index = 0) => {
  if (!allMediaImages.value.length) {
    return;
  }

  activeMediaIndex.value = Math.min(Math.max(index, 0), allMediaImages.value.length - 1);
  isGalleryOpen.value = true;
};

const openGalleryByUrl = (url) => {
  const index = allMediaImages.value.findIndex((item) => item.url === url);
  openGalleryModal(index >= 0 ? index : 0);
};

const closeGalleryModal = () => {
  isGalleryOpen.value = false;
};

const nextGalleryImage = () => {
  if (!allMediaImages.value.length) {
    return;
  }

  activeMediaIndex.value = (activeMediaIndex.value + 1) % allMediaImages.value.length;
};

const prevGalleryImage = () => {
  if (!allMediaImages.value.length) {
    return;
  }

  activeMediaIndex.value = (activeMediaIndex.value - 1 + allMediaImages.value.length) % allMediaImages.value.length;
};

const handleModalKeydown = (event) => {
  if (!isGalleryOpen.value) {
    return;
  }

  if (event.key === 'Escape') {
    closeGalleryModal();
    return;
  }

  if (event.key === 'ArrowRight') {
    nextGalleryImage();
    return;
  }

  if (event.key === 'ArrowLeft') {
    prevGalleryImage();
  }
};

const startAutoPlay = () => {
  stopAutoPlay();

  if (!isGalleryOpen.value || !isAutoPlayEnabled.value || allMediaImages.value.length < 2) {
    return;
  }

  autoPlayTimer = setInterval(() => {
    nextGalleryImage();
  }, 2500);
};

const stopAutoPlay = () => {
  if (autoPlayTimer) {
    clearInterval(autoPlayTimer);
    autoPlayTimer = null;
  }
};

const toggleAutoPlay = () => {
  isAutoPlayEnabled.value = !isAutoPlayEnabled.value;
  startAutoPlay();
};

const onModalTouchStart = (event) => {
  modalTouchStartX.value = event.changedTouches?.[0]?.clientX || 0;
};

const onModalTouchEnd = (event) => {
  const endX = event.changedTouches?.[0]?.clientX || 0;
  const delta = endX - modalTouchStartX.value;

  if (Math.abs(delta) < 35) {
    return;
  }

  if (delta < 0) {
    nextGalleryImage();
  } else {
    prevGalleryImage();
  }
};

watch([isGalleryOpen, isAutoPlayEnabled, allMediaImages], () => {
  startAutoPlay();
});

watch(activeMediaIndex, () => {
  if (isGalleryOpen.value && isAutoPlayEnabled.value) {
    startAutoPlay();
  }
});

onMounted(() => {
  window.addEventListener('keydown', handleModalKeydown);
});

onBeforeUnmount(() => {
  window.removeEventListener('keydown', handleModalKeydown);
  stopAutoPlay();
});

const profileCompletionPercent = computed(() => {
  const lastStep = Number(props.dashboardProfile?.last_completed_step || 1);
  const percent = Math.round((Math.min(Math.max(lastStep, 1), 10) / 10) * 100);
  return percent;
});

const profileDisplayName = computed(() => props.dashboardProfile?.full_name || userName.value);
const profileIdLabel = computed(() => props.dashboardProfile?.profile_id ? `ID: ${props.dashboardProfile.profile_id}` : 'ID: Not generated yet');

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
