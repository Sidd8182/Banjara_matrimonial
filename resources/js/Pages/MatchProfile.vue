<template>
  <UserDashboardLayout active-module="kundli-matching">
    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div class="border-b border-slate-200 px-4 py-3 sm:px-5">
        <p class="text-[11px] uppercase tracking-[0.16em] text-slate-500">Kundli Module</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-900">Kundli Matching Console</h1>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
          <tbody class="divide-y divide-slate-100">
            <tr>
              <td class="px-4 py-2.5 font-semibold text-slate-900">Matched Items</td>
              <td class="px-4 py-2.5 text-slate-700">{{ matchSummary.matchedItems }}/{{ matchSummary.totalItems }}</td>
              <td class="px-4 py-2.5 text-right text-slate-500">Profile parameters with same values</td>
            </tr>
            <tr>
              <td class="px-4 py-2.5 font-semibold text-slate-900">Overall Match</td>
              <td class="px-4 py-2.5 text-slate-700">{{ matchPercent }}%</td>
              <td class="px-4 py-2.5 text-right text-slate-500">Calculated from all matchable fields</td>
            </tr>
            <tr>
              <td class="px-4 py-2.5 font-semibold text-slate-900">Kundli Guna Match</td>
              <td class="px-4 py-2.5 text-slate-700">{{ kundliLabel }}</td>
              <td class="px-4 py-2.5 text-right text-slate-500">Kundli Match: {{ kundliPercentLabel || 'N/A' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <section class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-2">
      <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-slate-500">Your Profile</p>
        <div class="mt-3 flex items-center gap-4">
          <img :src="viewerImage" alt="Your profile" class="h-24 w-24 rounded-xl object-cover border border-slate-200" />
          <div>
            <h2 class="text-2xl font-black text-slate-900 heading-font">
              <a
                v-if="viewerProfile.id"
                :href="`/profiles/${viewerProfile.id}/view`"
                class="hover:underline text-blue-700"
              >
                {{ viewerProfile.name || 'N/A' }}
              </a>
              <span v-else>{{ viewerProfile.name || 'N/A' }}</span>
            </h2>
            <p class="text-sm text-slate-600">{{ viewerProfile.age ? `${viewerProfile.age} years` : 'Age N/A' }}</p>
            <p class="text-sm text-slate-600">{{ viewerProfile.city || 'N/A' }}, {{ viewerProfile.occupation || 'N/A' }}</p>
          </div>
        </div>
      </article>

      <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-slate-500">Profile You Are Matching</p>
        <div class="mt-3 flex items-center gap-4">
          <img :src="targetImage" alt="Target profile" class="h-24 w-24 rounded-xl object-cover border border-slate-200" />
          <div>
            <h2 class="text-2xl font-black text-slate-900 heading-font">
              <a
                v-if="targetProfile.id"
                :href="`/profiles/${targetProfile.id}/view`"
                class="hover:underline text-blue-700"
              >
                {{ targetProfile.name || 'N/A' }}
              </a>
              <span v-else>{{ targetProfile.name || 'N/A' }}</span>
            </h2>
            <p class="text-sm text-slate-600">{{ targetProfile.age ? `${targetProfile.age} years` : 'Age N/A' }}</p>
            <p class="text-sm text-slate-600">{{ targetProfile.city || 'N/A' }}, {{ targetProfile.occupation || 'N/A' }}</p>
          </div>
        </div>
      </article>
    </section>

    <section class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <div class="mb-3 flex items-center justify-between gap-3">
        <h3 class="text-xl font-bold text-slate-900">Ashtakoota Breakdown</h3>
        <div class="flex items-center gap-2">
          <p class="text-sm font-semibold text-slate-700">Total: {{ kundliLabel }}</p>
          <button
            type="button"
            class="rounded-md border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-800 hover:bg-emerald-100 disabled:opacity-60"
            :disabled="refreshingKundli"
            @click="refreshKundli"
          >
            {{ refreshingKundli ? 'Refreshing...' : 'Refresh via API' }}
          </button>
        </div>
      </div>

      <p v-if="kundliError" class="mb-3 rounded-md border border-rose-200 bg-rose-50 px-3 py-2 text-xs text-rose-700">{{ kundliError }}</p>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead>
            <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
              <th class="px-3 py-2">Koota</th>
              <th class="px-3 py-2 text-right">Score</th>
              <th class="px-3 py-2 text-right">Max Points</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="item in kootaBreakdown" :key="item.key || item.label">
              <td class="px-3 py-2 font-semibold text-slate-900">{{ item.label }}</td>
              <td class="px-3 py-2 text-right text-emerald-700 font-bold">{{ Number(item.score || 0).toFixed(2) }}</td>
              <td class="px-3 py-2 text-right text-slate-700">{{ Number(item.max || 0).toFixed(2) }}</td>
            </tr>
            <tr class="bg-slate-50">
              <td class="px-3 py-2 font-black text-slate-900">Total</td>
              <td class="px-3 py-2 text-right font-black text-slate-900">{{ Number(kundliScore || 0).toFixed(2) }}</td>
              <td class="px-3 py-2 text-right font-black text-slate-900">36.00</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <section class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <div class="mb-4 flex items-center justify-between gap-3">
        <h3 class="text-xl font-bold text-slate-900">Profile Item Matching</h3>
        <a :href="`/profiles/${targetProfile.id}/view`" class="text-sm font-semibold text-rose-700 hover:underline">Back to Profile</a>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead>
            <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
              <th class="px-3 py-2">Item</th>
              <th class="px-3 py-2">Your Value</th>
              <th class="px-3 py-2">Their Value</th>
              <th class="px-3 py-2 text-right">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="item in matchItems" :key="item.label" :class="itemRowClass(item)">
              <td class="px-3 py-2 font-semibold text-slate-900">{{ item.label }}</td>
              <td class="px-3 py-2 text-slate-700">{{ item.leftValue }}</td>
              <td class="px-3 py-2 text-slate-700">{{ item.rightValue }}</td>
              <td class="px-3 py-2 text-right">
                <span :class="itemBadgeClass(item)" class="inline-flex rounded-full px-3 py-1 text-xs font-bold">
                  {{ itemStatusText(item) }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </UserDashboardLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import UserDashboardLayout from '@/Layouts/UserDashboardLayout.vue';

const props = defineProps({
  viewerProfile: {
    type: Object,
    required: true,
  },
  targetProfile: {
    type: Object,
    required: true,
  },
  matchSummary: {
    type: Object,
    required: true,
  },
  matchItems: {
    type: Array,
    default: () => [],
  },
  kundli: {
    type: Object,
    default: null,
  },
});

const viewerProfile = computed(() => props.viewerProfile || {});
const targetProfile = computed(() => props.targetProfile || {});
const matchSummary = computed(() => props.matchSummary || { matchedItems: 0, totalItems: 0, percentage: 0 });
const matchItems = computed(() => props.matchItems || []);
const kundliState = ref(props.kundli || null);
const refreshingKundli = ref(false);
const kundliError = ref('');

const matchPercent = computed(() => Number(matchSummary.value.percentage || 0).toFixed(2));

const viewerImage = computed(() => viewerProfile.value.profilePictureUrl || '/images/default-avatar.svg');
const targetImage = computed(() => targetProfile.value.profilePictureUrl || '/images/default-avatar.svg');

const kundliLabel = computed(() => {
  if (!kundliState.value || kundliState.value.guna_score === null || kundliState.value.guna_score === undefined) {
    return 'N/A';
  }

  const total = kundliState.value.guna_total || 36;
  return `${kundliState.value.guna_score}/${total}`;
});

const kundliPercentLabel = computed(() => {
  if (!kundliState.value || kundliState.value.percentage === null || kundliState.value.percentage === undefined) {
    return null;
  }

  return Number(kundliState.value.percentage).toFixed(2);
});

const kootaBreakdown = computed(() => {
  if (!kundliState.value || !Array.isArray(kundliState.value.breakdown)) {
    return [];
  }

  return kundliState.value.breakdown;
});

const kundliScore = computed(() => {
  if (!kundliState.value || kundliState.value.guna_score === null || kundliState.value.guna_score === undefined) {
    return 0;
  }

  return Number(kundliState.value.guna_score || 0);
});

const refreshKundli = async () => {
  kundliError.value = '';
  refreshingKundli.value = true;

  try {
    const response = await window.axios.post('/match-kundli', {
      target_profile_id: targetProfile.value.id,
    });

    kundliState.value = response?.data?.data || null;
  } catch (error) {
    kundliError.value = error?.response?.data?.message || 'Kundli refresh failed.';
  } finally {
    refreshingKundli.value = false;
  }
};

const itemStatusText = (item) => {
  if (!item.hasData) {
    return 'No Data';
  }

  return item.isMatch ? 'Matched' : 'Not Matched';
};

const itemRowClass = (item) => {
  if (!item.hasData) {
    return 'bg-slate-50';
  }

  return item.isMatch ? 'bg-emerald-50/80' : 'bg-rose-50/70';
};

const itemBadgeClass = (item) => {
  if (!item.hasData) {
    return 'bg-slate-200 text-slate-700';
  }

  return item.isMatch ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white';
};
</script>
