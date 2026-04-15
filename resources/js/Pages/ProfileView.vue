<template>
  <MainLayout>
    <div
      v-if="!isPaidViewer"
      class="mb-4 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900"
    >
      Free member view is active. Full profile details (family and contact information) are available to paid members only.
    </div>
    <div
      v-else
      class="mb-4 rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-900"
    >
      {{ paidBannerText }}
    </div>

    <section :class="heroClass" class="relative overflow-hidden rounded-3xl p-6 sm:p-10 text-white shadow-xl">
      <div class="absolute -right-14 -top-16 h-52 w-52 rounded-full bg-white/10 blur-2xl"></div>
      <div class="absolute -left-12 -bottom-20 h-56 w-56 rounded-full bg-black/10 blur-2xl"></div>

      <div class="relative grid grid-cols-1 gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2">
          <p class="tracking-[0.25em] text-xs uppercase text-white/85">Profile View</p>
          <h1 class="mt-2 text-3xl sm:text-4xl font-black heading-font">{{ titleText }}</h1>
          <p class="mt-3 max-w-2xl text-white/90">{{ subtitleText }}</p>

          <div class="mt-6 flex flex-wrap gap-2">
            <span class="rounded-full border border-white/35 bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide">
              {{ profile.profileId || 'Profile' }}
            </span>
            <span v-if="profile.gender" class="rounded-full border border-white/35 bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide">
              {{ profile.gender }}
            </span>
            <span class="rounded-full border border-white/35 bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide">
              {{ profile.age ? `${profile.age} years` : 'Age N/A' }}
            </span>
            <span
              v-if="kundliLabel"
              class="rounded-full border border-emerald-200 bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-900"
            >
              Match Score {{ kundliLabel }}
            </span>
          </div>
        </div>

        <div class="rounded-2xl border border-white/35 bg-white/10 p-4 backdrop-blur">
          <div class="h-64 w-full rounded-xl border border-white/30 bg-white/10 p-2">
            <img
              :src="profileImage"
              alt="Profile photo"
              class="h-full w-full rounded-lg object-contain"
            />
          </div>
          <div class="mt-4 flex gap-2">
            <button class="flex-1 rounded-lg border border-white/40 bg-white/15 py-2 text-sm font-semibold hover:bg-white/25 transition">
              Skip
            </button>
            <button class="flex-1 rounded-lg bg-white text-gray-900 py-2 text-sm font-bold hover:bg-gray-100 transition">
              Connect
            </button>
          </div>

          <a
            v-if="viewer?.name"
            :href="`/profiles/${profile.id}/match`"
            class="mt-2 block w-full rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2 text-center text-sm font-bold text-emerald-800 hover:bg-emerald-100 transition"
          >
            Match Your Profile
          </a>
        </div>
      </div>
    </section>

    <section class="mt-8 grid grid-cols-1 gap-6 xl:grid-cols-3">
      <article class="xl:col-span-2 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-2xl font-black text-gray-900 heading-font">Basic Details</h2>
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
          <InfoItem label="Name" :value="profile.name || 'N/A'" />
          <InfoItem label="Location" :value="profile.location || 'N/A'" />
          <InfoItem label="Religion" :value="profile.religion || 'N/A'" />
          <InfoItem label="Caste" :value="profile.caste || 'N/A'" />
          <InfoItem label="Height" :value="profile.heightCm ? `${profile.heightCm} cm` : 'N/A'" />
          <InfoItem label="Marital Status" :value="profile.maritalStatus || 'N/A'" />
          <InfoItem label="Mother Tongue" :value="profile.motherTongue || 'N/A'" />
          <InfoItem label="Education" :value="profile.education || 'N/A'" />
        </div>
      </article>

      <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-xl font-black text-gray-900 heading-font">Career Snapshot</h2>
        <div class="mt-4 space-y-3 text-sm">
          <InfoItem label="Profession" :value="profile.profession || 'N/A'" />
          <InfoItem label="Company" :value="profile.company || 'N/A'" />
          <InfoItem label="Income" :value="profile.annualIncome || 'N/A'" />
          <InfoItem label="Field" :value="profile.fieldOfStudy || 'N/A'" />
        </div>
      </article>
    </section>

    <section class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
      <h2 class="text-xl font-black text-gray-900 heading-font">Lifestyle & Horoscope</h2>
      <div class="mt-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3 text-sm">
        <InfoItem label="Diet" :value="profile.diet || 'N/A'" />
        <InfoItem label="Smoking" :value="profile.smoking || 'N/A'" />
        <InfoItem label="Drinking" :value="profile.drinking || 'N/A'" />
        <InfoItem label="Rashi" :value="profile.rashi || 'N/A'" />
        <InfoItem label="Nakshatra" :value="profile.nakshatra || 'N/A'" />
        <InfoItem label="Lagna" :value="profile.lagna || 'N/A'" />
      </div>

      <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50 p-4">
        <p class="text-xs uppercase tracking-wide text-gray-600 font-semibold">About</p>
        <p class="mt-2 text-sm text-gray-700">{{ profile.about || 'No description available.' }}</p>
      </div>
    </section>

    <section v-if="isPaidViewer" class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
      <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-xl font-black text-gray-900 heading-font">Family Details</h2>
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
          <InfoItem label="Father Name" :value="profile.family?.fatherName || 'N/A'" />
          <InfoItem label="Father Occupation" :value="profile.family?.fatherOccupation || 'N/A'" />
          <InfoItem label="Mother Name" :value="profile.family?.motherName || 'N/A'" />
          <InfoItem label="Mother Occupation" :value="profile.family?.motherOccupation || 'N/A'" />
          <InfoItem label="Brothers" :value="stringValue(profile.family?.brothersCount)" />
          <InfoItem label="Sisters" :value="stringValue(profile.family?.sistersCount)" />
          <InfoItem label="Family Type" :value="profile.family?.familyType || 'N/A'" />
          <InfoItem label="Family Status" :value="profile.family?.familyStatus || 'N/A'" />
          <InfoItem label="Family Values" :value="profile.family?.familyValues || 'N/A'" />
        </div>
      </article>

      <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-xl font-black text-gray-900 heading-font">Contact & Address</h2>
        <div class="mt-4 grid grid-cols-1 gap-3 text-sm">
          <InfoItem label="Mobile" :value="profile.contact?.mobile || 'N/A'" />
          <InfoItem label="Email" :value="profile.contact?.email || 'N/A'" />
          <InfoItem label="WhatsApp" :value="profile.contact?.whatsapp || 'N/A'" />
          <InfoItem label="Area / Locality" :value="profile.contact?.areaLocality || 'N/A'" />
          <InfoItem label="Current Address" :value="profile.contact?.currentAddress || 'N/A'" />
          <InfoItem label="Permanent Address" :value="profile.contact?.permanentAddress || 'N/A'" />
        </div>
      </article>
    </section>

    <section class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
      <div class="mb-4 flex items-center justify-between gap-3">
        <h2 class="text-xl font-black text-gray-900 heading-font">Gallery</h2>
        <a href="/browse" class="text-sm font-semibold text-rose-700 hover:underline">Back to Browse</a>
      </div>

      <div v-if="galleryImages.length" class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <button
          v-for="image in galleryImages"
          :key="image.id"
          type="button"
          class="group overflow-hidden rounded-xl border border-gray-200"
          @click="openGalleryModal(image.url)"
        >
          <img
            :src="image.url"
            alt="Gallery image"
            class="h-32 w-full object-cover transition group-hover:scale-105"
          />
        </button>
      </div>
      <p v-else class="text-sm text-gray-500">No gallery images available.</p>
    </section>

    <div
      v-if="isGalleryOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 p-4"
      @click.self="closeGalleryModal"
    >
      <button
        type="button"
        class="absolute right-4 top-4 rounded-md bg-white/15 px-3 py-1.5 text-sm font-semibold text-white hover:bg-white/30"
        @click="closeGalleryModal"
      >
        Close
      </button>
      <img
        :src="activeGalleryUrl"
        alt="Gallery full view"
        class="max-h-[86vh] w-full max-w-5xl rounded-xl border border-white/25 bg-black object-contain"
      />
    </div>
  </MainLayout>
</template>

<script setup>
import { computed, h, onBeforeUnmount, onMounted, ref } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
  viewer: {
    type: Object,
    default: () => ({}),
  },
  viewMode: {
    type: String,
    default: 'neutral',
  },
  profile: {
    type: Object,
    required: true,
  },
  viewerAccess: {
    type: Object,
    default: () => ({
      isPaid: false,
      planName: null,
    }),
  },
  kundliMatch: {
    type: Object,
    default: null,
  },
});

const profile = computed(() => props.profile || {});

const heroClass = computed(() => {
  if (props.viewMode === 'male_viewing_female') {
    return 'bg-gradient-to-r from-rose-600 via-red-500 to-orange-500';
  }

  if (props.viewMode === 'female_viewing_male') {
    return 'bg-gradient-to-r from-sky-700 via-indigo-700 to-blue-700';
  }

  return 'bg-gradient-to-r from-emerald-700 via-teal-700 to-cyan-700';
});

const titleText = computed(() => {
  if (props.viewMode === 'male_viewing_female') {
    return 'Her Profile Overview';
  }

  if (props.viewMode === 'female_viewing_male') {
    return 'His Profile Overview';
  }

  return 'Profile Overview';
});

const subtitleText = computed(() => {
  if (props.viewMode === 'male_viewing_female') {
    return 'Elegant layout tuned for male viewer exploring female profile details.';
  }

  if (props.viewMode === 'female_viewing_male') {
    return 'Confident layout tuned for female viewer exploring male profile details.';
  }

  return 'Profile details and compatibility context in a clean neutral view.';
});

const isPaidViewer = computed(() => !!props.viewerAccess?.isPaid);
const paidBannerText = computed(() => {
  const plan = props.viewerAccess?.planName;
  if (plan) {
    return `Paid member access active (${plan}). Full profile unlocked.`;
  }
  return 'Paid member access active. Full profile unlocked.';
});

const kundliLabel = computed(() => {
  if (!props.kundliMatch || props.kundliMatch.guna_score === null || props.kundliMatch.guna_score === undefined) {
    return null;
  }

  const total = props.kundliMatch.guna_total || 36;
  return `${props.kundliMatch.guna_score}/${total}`;
});

const profileImage = computed(() => profile.value.profilePictureUrl || '/images/default-avatar.svg');
const galleryImages = computed(() => Array.isArray(profile.value.gallery) ? profile.value.gallery : []);
const isGalleryOpen = ref(false);
const activeGalleryUrl = ref('');

const openGalleryModal = (url) => {
  if (!url) {
    return;
  }

  activeGalleryUrl.value = url;
  isGalleryOpen.value = true;
};

const closeGalleryModal = () => {
  isGalleryOpen.value = false;
};

const stringValue = (value) => {
  if (value === null || value === undefined || value === '') {
    return 'N/A';
  }
  return String(value);
};

const handleEscape = (event) => {
  if (event.key === 'Escape' && isGalleryOpen.value) {
    closeGalleryModal();
  }
};

onMounted(() => {
  window.addEventListener('keydown', handleEscape);
});

onBeforeUnmount(() => {
  window.removeEventListener('keydown', handleEscape);
});

const InfoItem = (propsItem) => {
  return h('div', { class: 'rounded-lg border border-gray-200 p-3' }, [
    h('p', { class: 'text-xs uppercase tracking-wide text-gray-500 font-semibold' }, propsItem.label),
    h('p', { class: 'mt-1 text-sm font-semibold text-gray-900 break-words' }, propsItem.value),
  ]);
};
</script>

<style scoped>
.heading-font {
  font-family: 'Merriweather', 'Georgia', serif;
}
</style>
