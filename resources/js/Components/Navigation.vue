<template>
  <nav class="bg-white shadow-md sticky top-0 z-50" @mouseleave="handleNavMouseLeave">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <div class="flex items-center">
          <a href="/" class="text-2xl font-bold text-primary">
            💍 Banjara
          </a>
        </div>

        <!-- Navigation Links -->
        <div class="hidden md:flex space-x-8 items-center">
          <a href="/" class="text-gray-700 hover:text-primary transition">Home</a>

          <div class="relative" @mouseenter="openMegaMenu('Mother Tongue')">
            <button
              class="text-gray-700 hover:text-primary transition inline-flex items-center gap-1"
              type="button"
              @click="toggleMegaMenu"
            >
              Browse
              <span class="text-xs">▾</span>
            </button>
          </div>

          <a href="/pricing" class="text-gray-700 hover:text-primary transition">Pricing</a>
          <div class="relative" @mouseenter="openMatchesMenu">
            <button
              class="text-gray-700 hover:text-primary transition inline-flex items-center gap-1"
              type="button"
              @click="toggleMatchesMenu"
            >
              Matches
              <span class="text-xs">▾</span>
            </button>

            <transition name="mega-menu">
              <div
                v-if="isMatchesMenuOpen"
                class="absolute left-0 top-full mt-2 w-52 rounded-lg border border-rose-100 bg-white shadow-lg"
                @mouseenter="isMatchesMenuOpen = true"
                @mouseleave="closeMatchesMenu"
              >
                <div class="p-2">
                  <a
                    href="/browse?searchType=basic"
                    class="block rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-rose-50 hover:text-rose-600"
                    @click="closeMatchesMenu"
                  >
                    Basic Search
                  </a>
                  <a
                    href="/browse?searchType=advanced"
                    class="block rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-rose-50 hover:text-rose-600"
                    @click="closeMatchesMenu"
                  >
                    Advance Search
                  </a>
                </div>
              </div>
            </transition>
          </div>
          <a href="/messages" class="text-gray-700 hover:text-primary transition">Messages</a>
        </div>

        <!-- Auth Buttons -->
        <div class="hidden md:flex space-x-3 items-center">
          <template v-if="isAuthenticated">
            <a href="/dashboard" class="px-4 py-2 text-primary border border-primary rounded-lg hover:bg-red-50 transition">
              Dashboard
            </a>
            <Link
              href="/logout"
              method="post"
              as="button"
              class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-red-700 transition"
            >
              Logout
            </Link>
          </template>
          <template v-else>
            <a href="/login" class="px-4 py-2 text-primary border border-primary rounded-lg hover:bg-red-50 transition">
              Login
            </a>
            <a href="/register" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-red-700 transition">
              Register
            </a>
          </template>
        </div>

        <!-- Mobile Menu Button -->
        <button class="md:hidden p-2 rounded-md border border-gray-200" @click="isMobileMenuOpen = !isMobileMenuOpen">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              :d="isMobileMenuOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'"
            />
          </svg>
        </button>
      </div>

      <div v-if="isMobileMenuOpen" class="md:hidden border-t border-gray-100 py-4">
        <div class="flex flex-col gap-3">
          <a href="/" class="text-gray-700 hover:text-primary transition">Home</a>
          <button
            class="text-gray-700 hover:text-primary transition inline-flex items-center justify-between"
            type="button"
            @click="isMobileBrowseOpen = !isMobileBrowseOpen"
          >
            <span>Browse Profiles By</span>
            <span class="ml-2 text-xs">{{ isMobileBrowseOpen ? '▴' : '▾' }}</span>
          </button>

          <div v-if="isMobileBrowseOpen" class="rounded-xl border border-slate-200 bg-slate-50 p-3">
            <div class="grid grid-cols-1 gap-2">
              <button
                v-for="category in browseCategories"
                :key="`mobile-${category}`"
                type="button"
                class="w-full rounded-md px-3 py-2 text-left text-sm font-semibold transition"
                :class="mobileActiveCategory === category ? 'bg-rose-500 text-white' : 'bg-white text-slate-700 hover:bg-rose-50'"
                @click="mobileActiveCategory = category"
              >
                {{ category }}
              </button>
            </div>

            <div class="mt-3 grid grid-cols-2 gap-2">
              <a
                v-for="item in mobileActiveCategoryItems"
                :key="`mobile-item-${mobileActiveCategory}-${item}`"
                :href="buildBrowseLink(mobileActiveCategory, item)"
                class="rounded-md bg-white px-2 py-2 text-sm text-slate-700 hover:bg-rose-50"
                @click="isMobileMenuOpen = false"
              >
                {{ item }}
              </a>
            </div>
          </div>

          <a href="/pricing" class="text-gray-700 hover:text-primary transition">Pricing</a>
          <button
            class="text-gray-700 hover:text-primary transition inline-flex items-center justify-between"
            type="button"
            @click="isMobileMatchesOpen = !isMobileMatchesOpen"
          >
            <span>Matches</span>
            <span class="ml-2 text-xs">{{ isMobileMatchesOpen ? '▴' : '▾' }}</span>
          </button>

          <div v-if="isMobileMatchesOpen" class="rounded-xl border border-slate-200 bg-slate-50 p-3">
            <div class="grid grid-cols-1 gap-2">
              <a
                href="/browse?searchType=basic"
                class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-rose-50"
                @click="isMobileMenuOpen = false"
              >
                Basic Search
              </a>
              <a
                href="/browse?searchType=advanced"
                class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-rose-50"
                @click="isMobileMenuOpen = false"
              >
                Advance Search
              </a>
            </div>
          </div>
          <a href="/messages" class="text-gray-700 hover:text-primary transition">Messages</a>
        </div>
        <div v-if="isAuthenticated" class="mt-4 grid grid-cols-2 gap-3">
          <a href="/dashboard" class="px-4 py-2 text-center text-primary border border-primary rounded-lg hover:bg-red-50 transition">
            Dashboard
          </a>
          <Link
            href="/logout"
            method="post"
            as="button"
            class="px-4 py-2 text-center bg-primary text-white rounded-lg hover:bg-red-700 transition"
          >
            Logout
          </Link>
        </div>
        <div v-else class="mt-4 grid grid-cols-2 gap-3">
          <a href="/login" class="px-4 py-2 text-center text-primary border border-primary rounded-lg hover:bg-red-50 transition">
            Login
          </a>
          <a href="/register" class="px-4 py-2 text-center bg-primary text-white rounded-lg hover:bg-red-700 transition">
            Register
          </a>
        </div>
      </div>
    </div>

    <transition name="mega-menu">
      <div
        v-if="isMegaMenuOpen"
        class="hidden md:block absolute left-0 top-full w-full border-t border-rose-100"
        @mouseenter="isMegaMenuOpen = true"
        @mouseleave="closeMegaMenu"
      >
        <div class="bg-gradient-to-b from-white to-rose-50/80 backdrop-blur text-slate-800 shadow-xl">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
            <div class="grid grid-cols-12 gap-6">
              <div class="col-span-3 border-r border-slate-200 pr-4">
                <ul class="space-y-1">
                  <li v-for="category in browseCategories" :key="category">
                    <button
                      class="w-full flex items-center justify-between px-3 py-2.5 text-left rounded-md transition"
                      :class="activeCategory === category ? 'bg-rose-500 text-white' : 'hover:bg-rose-100 text-slate-700'"
                      @mouseenter="handleCategoryHover(category)"
                      @click="handleCategoryClick(category)"
                      type="button"
                    >
                      <span class="font-semibold">{{ category }}</span>
                      <span>›</span>
                    </button>
                  </li>
                </ul>
              </div>

              <div class="col-span-9">
                <div class="grid grid-cols-3 gap-4">
                  <a
                    v-for="item in activeCategoryItems"
                    :key="`${activeCategory}-${item}`"
                    :href="buildBrowseLink(activeCategory, item)"
                    class="text-left px-2 py-1.5 rounded text-slate-700 hover:text-rose-600 hover:bg-rose-100 transition"
                    @click="closeMegaMenu"
                  >
                    {{ item }}
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </nav>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const isMobileMenuOpen = ref(false);
const isMobileBrowseOpen = ref(false);
const isMobileMatchesOpen = ref(false);
const isMegaMenuOpen = ref(false);
const isMatchesMenuOpen = ref(false);
const activeCategory = ref('Mother Tongue');
const mobileActiveCategory = ref('Mother Tongue');
const page = usePage();
const isAuthenticated = computed(() => Boolean(page.props.auth?.user));

const browseCategories = [
  'Mother Tongue',
  'Caste',
  'Religion',
  'City',
  'Occupation',
  'State',
  'NRI',
];

const browseData = {
  'Mother Tongue': [
    'Bihari', 'Bengali', 'Hindi Delhi', 'Hindi', 'Gujarati', 'Kannada',
    'Malayalam', 'Marathi', 'Oriya', 'Punjabi', 'Rajasthani', 'Tamil',
    'Telugu', 'Hindi UP', 'Hindi MP', 'Konkani', 'Himachali', 'Haryanvi',
    'Assamese', 'Kashmiri', 'Sikkim Nepali', 'Tulu',
  ],
  Caste: [
    'Agarwal', 'Rajput', 'Yadav', 'Brahmin', 'Jat', 'Kayastha',
    'Khatri', 'Baniya', 'Kurmi', 'Nair', 'Reddy', 'Vokkaliga',
    'Maratha', 'Patel', 'Nadar', 'Chettiar', 'Ezhava', 'SC',
    'ST', 'OBC', 'Jain', 'Sikh',
  ],
  Religion: [
    'Hindu', 'Muslim', 'Christian', 'Sikh', 'Jain', 'Buddhist',
    'Parsi', 'Jewish', 'Inter-Religion', 'No Preference',
  ],
  City: [
    'Delhi', 'Mumbai', 'Bengaluru', 'Hyderabad', 'Chennai', 'Pune',
    'Jaipur', 'Ahmedabad', 'Lucknow', 'Kolkata', 'Indore', 'Chandigarh',
    'Noida', 'Gurgaon', 'Patna', 'Bhopal', 'Surat', 'Nagpur',
    'Coimbatore', 'Visakhapatnam', 'Vadodara', 'Kanpur',
  ],
  Occupation: [
    'IT Software', 'Teacher', 'CA Accountant', 'Businessman', 'Doctors Nurse',
    'Govt. Services', 'Lawyers', 'Defence', 'IAS', 'Cyber Network Security',
    'Engineers', 'Hotels Hospitality Professional', 'NGO Social Services',
    'Nurse', 'Police',
  ],
  State: [
    'Delhi', 'Maharashtra', 'Karnataka', 'Tamil Nadu', 'Gujarat', 'Rajasthan',
    'Uttar Pradesh', 'Madhya Pradesh', 'Bihar', 'Punjab', 'Haryana', 'West Bengal',
    'Kerala', 'Odisha', 'Assam', 'Telangana',
  ],
  NRI: [
    'United States', 'Canada', 'United Kingdom', 'Australia', 'New Zealand',
    'UAE', 'Singapore', 'Germany', 'France', 'Netherlands', 'Ireland',
    'Qatar', 'Kuwait', 'Saudi Arabia',
  ],
};

const activeCategoryItems = computed(() => browseData[activeCategory.value] || []);
const mobileActiveCategoryItems = computed(() => browseData[mobileActiveCategory.value] || []);

const openMegaMenu = (category) => {
  activeCategory.value = category;
  isMegaMenuOpen.value = true;
};

const toggleMegaMenu = () => {
  isMegaMenuOpen.value = !isMegaMenuOpen.value;
};

const closeMegaMenu = () => {
  isMegaMenuOpen.value = false;
};

const openMatchesMenu = () => {
  isMatchesMenuOpen.value = true;
};

const toggleMatchesMenu = () => {
  isMatchesMenuOpen.value = !isMatchesMenuOpen.value;
};

const closeMatchesMenu = () => {
  isMatchesMenuOpen.value = false;
};

const handleNavMouseLeave = () => {
  closeMegaMenu();
  closeMatchesMenu();
};

const handleCategoryHover = (category) => {
  activeCategory.value = category;
};

const handleCategoryClick = (category) => {
  activeCategory.value = category;
};

const buildBrowseLink = (category, item) => {
  const params = new URLSearchParams({ category, value: item });
  return `/browse?${params.toString()}`;
};
</script>

<style scoped>
.mega-menu-enter-active,
.mega-menu-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.mega-menu-enter-from,
.mega-menu-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
