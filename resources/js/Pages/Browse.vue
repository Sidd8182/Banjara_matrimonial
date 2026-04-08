<template>
  <MainLayout>
    <div>
      <!-- Search & Filter Section -->
      <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Browse Profiles</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
          <!-- Age Range -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Age Range</label>
            <select v-model="filters.ageRange" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-transparent">
              <option value="">All Ages</option>
              <option value="18-25">18-25</option>
              <option value="26-35">26-35</option>
              <option value="36-45">36-45</option>
              <option value="45+">45+</option>
            </select>
          </div>

          <!-- Religion -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Religion</label>
            <select v-model="filters.religion" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-transparent">
              <option value="">All Religions</option>
              <option value="Hindu">Hindu</option>
              <option value="Muslim">Muslim</option>
              <option value="Christian">Christian</option>
              <option value="Sikh">Sikh</option>
              <option value="Others">Others</option>
            </select>
          </div>

          <!-- Location -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
            <input v-model="filters.location" type="text" placeholder="City..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-transparent" />
          </div>

          <!-- Caste -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Caste</label>
            <select v-model="filters.caste" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-transparent">
              <option value="">All Castes</option>
              <option value="General">General</option>
              <option value="OBC">OBC</option>
              <option value="SC">SC</option>
              <option value="ST">ST</option>
            </select>
          </div>

          <!-- Search Button -->
          <div class="flex items-end">
            <button @click="handleSearch" class="w-full btn-primary">
              🔍 Search
            </button>
          </div>
        </div>
      </div>

      <!-- Profiles Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div v-for="profile in profiles" :key="profile.id" class="bg-white rounded-lg shadow overflow-hidden hover:shadow-xl transition">
          <!-- Profile Image -->
          <div class="w-full h-40 bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
            <span class="text-5xl">👤</span>
          </div>

          <!-- Profile Info -->
          <div class="p-4">
            <h3 class="font-bold text-gray-900 mb-1">{{ profile.name }}</h3>
            <p class="text-sm text-gray-600 mb-2">{{ profile.age }}</p>
            <p class="text-xs text-gray-500 mb-3">{{ profile.location }} • {{ profile.religion }}</p>

            <!-- Profile Details -->
            <div class="text-xs text-gray-600 mb-3 space-y-1">
              <p><span class="font-semibold">Height:</span> {{ profile.height }}</p>
              <p><span class="font-semibold">Occupation:</span> {{ profile.occupation }}</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
              <button @click="skipProfile(profile.id)" class="flex-1 px-3 py-2 border border-gray-300 text-gray-600 rounded text-sm hover:bg-gray-50 transition">
                ✕
              </button>
              <button @click="likeProfile(profile.id)" class="flex-1 px-3 py-2 bg-primary text-white rounded text-sm hover:bg-red-700 transition">
                ❤️
              </button>
              <button @click="viewProfile(profile.id)" class="flex-1 px-3 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition">
                👁️
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="profiles.length === 0" class="bg-white rounded-lg shadow p-12 text-center">
        <p class="text-gray-600 text-lg">No profiles found. Try adjusting your filters.</p>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { reactive, ref } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';

const filters = reactive({
  ageRange: '',
  religion: '',
  location: '',
  caste: '',
});

const profiles = ref([
  { id: 1, name: 'Priya Sharma', age: '25 years', location: 'Mumbai', religion: 'Hindu', height: '5\'5"', occupation: 'Software Engineer' },
  { id: 2, name: 'Anjali Patel', age: '23 years', location: 'Delhi', religion: 'Hindu', height: '5\'4"', occupation: 'Doctor' },
  { id: 3, name: 'Neha Gupta', age: '27 years', location: 'Bangalore', religion: 'Hindu', height: '5\'6"', occupation: 'Designer' },
  { id: 4, name: 'Kavya Singh', age: '24 years', location: 'Pune', religion: 'Hindu', height: '5\'5"', occupation: 'Finance' },
  { id: 5, name: 'Meera Verma', age: '26 years', location: 'Hyderabad', religion: 'Hindu', height: '5\'4"', occupation: 'Manager' },
  { id: 6, name: 'Isha Malhotra', age: '22 years', location: 'Gurgaon', religion: 'Hindu', height: '5\'7"', occupation: 'Student' },
  { id: 7, name: 'Divya Nair', age: '28 years', location: 'Chennai', religion: 'Hindu', height: '5\'5"', occupation: 'Architect' },
  { id: 8, name: 'Pooja Reddy', age: '25 years', location: 'Hyderabad', religion: 'Hindu', height: '5\'5"', occupation: 'HR' },
]);

const handleSearch = () => {
  console.log('Filters applied:', filters);
  // Filter profiles based on filters
};

const likeProfile = (profileId) => {
  console.log('Liked profile:', profileId);
};

const skipProfile = (profileId) => {
  console.log('Skipped profile:', profileId);
  profiles.value = profiles.value.filter(p => p.id !== profileId);
};

const viewProfile = (profileId) => {
  console.log('View profile:', profileId);
};
</script>
