<template>
  <UserDashboardLayout active-module="kundli-history">
    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div class="border-b border-slate-200 px-4 py-3 sm:px-5">
        <p class="text-[11px] uppercase tracking-[0.16em] text-slate-500">Kundli Module</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-900">Kundli Match History</h1>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
          <tbody class="divide-y divide-slate-100">
            <tr>
              <td class="px-4 py-2.5 font-semibold text-slate-900">Total Saved Matches</td>
              <td class="px-4 py-2.5 text-slate-700">{{ rowsList.length }}</td>
              <td class="px-4 py-2.5 text-right">
                <a href="/browse" class="text-indigo-700 hover:underline">Browse Profiles</a>
              </td>
            </tr>
            <tr>
              <td class="px-4 py-2.5 font-semibold text-slate-900">Best Score</td>
              <td class="px-4 py-2.5 text-slate-700">{{ bestScoreLabel }}</td>
              <td class="px-4 py-2.5 text-right text-slate-500">Highest guna score in your history</td>
            </tr>
            <tr>
              <td class="px-4 py-2.5 font-semibold text-slate-900">Best Match Percentage</td>
              <td class="px-4 py-2.5 text-slate-700">{{ bestPercentLabel }}</td>
              <td class="px-4 py-2.5 text-right text-slate-500">Top compatibility percentage in saved results</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <section class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <div class="mb-4 flex items-center justify-between gap-3">
        <h2 class="text-xl font-bold text-slate-900">Saved Matches</h2>
      </div>

      <div v-if="rowsList.length" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead>
            <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
              <th class="px-3 py-2">Profile</th>
              <th class="px-3 py-2">Match Score</th>
              <th class="px-3 py-2">Kundli %</th>
              <th class="px-3 py-2">Calculated At</th>
              <th class="px-3 py-2 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="item in rowsList" :key="item.id" class="hover:bg-slate-50">
              <td class="px-3 py-2">
                <div class="flex items-center gap-3">
                  <img
                    :src="item.targetProfilePictureUrl || '/images/default-avatar.svg'"
                    alt="Profile"
                    class="h-12 w-12 rounded-lg border border-slate-200 object-cover"
                  />
                  <div>
                    <p class="font-semibold text-slate-900">
                      <a v-if="item.targetProfileUrl" :href="item.targetProfileUrl" class="hover:underline text-blue-700">
                        {{ item.targetName }}
                      </a>
                      <span v-else>{{ item.targetName }}</span>
                    </p>
                    <p class="text-xs text-slate-600">
                      <span v-if="item.targetAge">{{ item.targetAge }} years, </span>{{ item.targetLocation }}
                    </p>
                  </div>
                </div>
              </td>
              <td class="px-3 py-2">
                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800">
                  {{ scoreLabel(item) }}
                </span>
              </td>
              <td class="px-3 py-2 text-sm font-semibold text-slate-700">{{ percentLabel(item) }}</td>
              <td class="px-3 py-2 text-sm text-slate-600">{{ item.computedAt || 'N/A' }}</td>
              <td class="px-3 py-2">
                <div class="flex justify-end gap-2">
                  <a
                    v-if="item.targetProfileUrl"
                    :href="item.targetProfileUrl"
                    class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100"
                  >
                    View
                  </a>
                  <a
                    v-if="item.matchUrl"
                    :href="item.matchUrl"
                    class="rounded-md bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-800"
                  >
                    Open Match
                  </a>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="rounded-xl border border-slate-200 bg-slate-50 p-6 text-center text-sm text-slate-600">
        No kundli match history has been saved yet. Open a profile from Browse to start calculations.
      </div>
    </section>
  </UserDashboardLayout>
</template>

<script setup>
import { computed } from 'vue';
import UserDashboardLayout from '@/Layouts/UserDashboardLayout.vue';

const props = defineProps({
  rows: {
    type: Array,
    default: () => [],
  },
});

const rowsList = computed(() => props.rows || []);

const bestScoreLabel = computed(() => {
  const withScore = rowsList.value
    .filter((item) => item?.gunaScore !== null && item?.gunaScore !== undefined)
    .sort((a, b) => Number(b.gunaScore) - Number(a.gunaScore));

  if (!withScore.length) {
    return 'N/A';
  }

  return scoreLabel(withScore[0]);
});

const bestPercentLabel = computed(() => {
  const withPercent = rowsList.value
    .filter((item) => item?.percentage !== null && item?.percentage !== undefined)
    .sort((a, b) => Number(b.percentage) - Number(a.percentage));

  if (!withPercent.length) {
    return 'N/A';
  }

  return percentLabel(withPercent[0]);
});

const scoreLabel = (item) => {
  if (item.gunaScore === null || item.gunaScore === undefined) {
    return 'N/A';
  }

  return `${item.gunaScore}/${item.gunaTotal || 36}`;
};

const percentLabel = (item) => {
  if (item.percentage === null || item.percentage === undefined) {
    return 'N/A';
  }

  return `${Number(item.percentage).toFixed(2)}%`;
};
</script>
