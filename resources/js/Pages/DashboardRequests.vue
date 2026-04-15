<template>
  <UserDashboardLayout active-module="requests">
    <section class="rounded-3xl bg-gradient-to-r from-sky-700 via-indigo-700 to-blue-700 p-6 text-white shadow-lg sm:p-8">
      <p class="text-xs uppercase tracking-[0.2em] text-white/80">Request Management</p>
      <h1 class="mt-2 text-3xl font-extrabold">Request Center</h1>
      <p class="mt-2 text-white/90">
        Incoming and outgoing requests are shown here with pagination for large volume management.
      </p>

      <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="rounded-xl bg-white/15 p-3">
          <p class="text-xs text-white/85">Received Pending</p>
          <p class="text-xl font-bold">{{ stats.received_pending }}</p>
        </div>
        <div class="rounded-xl bg-white/15 p-3">
          <p class="text-xs text-white/85">Received Accepted</p>
          <p class="text-xl font-bold">{{ stats.received_accepted }}</p>
        </div>
        <div class="rounded-xl bg-white/15 p-3">
          <p class="text-xs text-white/85">Sent Pending</p>
          <p class="text-xl font-bold">{{ stats.sent_pending }}</p>
        </div>
        <div class="rounded-xl bg-white/15 p-3">
          <p class="text-xs text-white/85">Sent Rejected</p>
          <p class="text-xl font-bold">{{ stats.sent_rejected }}</p>
        </div>
      </div>
    </section>

    <section class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
      <article class="rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-2">
          <h2 class="px-4 py-3 text-sm font-bold text-gray-900">Incoming Requests</h2>
          <span class="px-4 py-3 text-xs font-semibold text-gray-500">Total: {{ stats.received_total }}</span>
        </div>

        <div class="overflow-x-auto border-y border-gray-200">
          <table class="min-w-full">
            <thead class="bg-gray-50 text-[10px] uppercase tracking-wide text-gray-500">
              <tr>
                <th class="px-3 py-2 text-left">Name</th>
                <th class="px-3 py-2 text-left">City</th>
                <th class="px-3 py-2 text-left">Profession</th>
                <th class="px-3 py-2 text-left">Status</th>
                <th class="px-3 py-2 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-[12px]">
              <tr v-for="request in incomingRows" :key="`incoming-${request.profile_id}`" class="hover:bg-gray-50/70">
                <td class="px-3 py-2.5 font-semibold text-gray-900">{{ request.name }}</td>
                <td class="px-3 py-2.5 text-gray-600">{{ request.city }}</td>
                <td class="px-3 py-2.5 text-gray-600">{{ request.profession }}</td>
                <td class="px-3 py-2.5 text-xs font-semibold" :class="requestStatusClass(request.status)">
                  {{ requestStatusLabel(request.status) }}
                </td>
                <td class="px-3 py-2.5">
                  <div class="flex justify-end gap-2">
                    <a :href="`/profiles/${request.profile_id}/view`" class="rounded border border-gray-300 px-2 py-1 text-[11px] font-semibold text-gray-700 hover:bg-gray-50">View</a>
                    <button
                      v-if="request.status === 'pending'"
                      type="button"
                      class="rounded bg-emerald-600 px-2 py-1 text-[11px] font-semibold text-white hover:bg-emerald-700 disabled:opacity-60"
                      :disabled="loadingMap[request.profile_id]"
                      @click="submitMatchAction(request.profile_id, 'connect')"
                    >
                      Accept
                    </button>
                    <button
                      v-if="request.status === 'pending'"
                      type="button"
                      class="rounded border border-rose-200 px-2 py-1 text-[11px] font-semibold text-rose-700 hover:bg-rose-50 disabled:opacity-60"
                      :disabled="loadingMap[request.profile_id]"
                      @click="rejectIncomingRequest(request.profile_id)"
                    >
                      Reject
                    </button>
                  </div>
                  <p v-if="request.rejection_reason" class="mt-1 text-right text-[11px] text-rose-700">{{ request.rejection_reason }}</p>
                </td>
              </tr>
              <tr v-if="!incomingRows.length">
                <td colspan="5" class="px-3 py-5 text-center text-xs text-gray-500">No incoming requests.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="incomingMeta.last_page > 1" class="mt-4 flex items-center justify-between text-xs">
          <a
            :href="buildPageUrl('incoming_page', incomingMeta.current_page - 1, outgoingMeta.current_page)"
            class="rounded border border-gray-300 px-3 py-1.5 text-gray-700 hover:bg-gray-50"
            :class="incomingMeta.current_page <= 1 ? 'pointer-events-none opacity-50' : ''"
          >
            Previous
          </a>
          <span class="text-gray-600">Page {{ incomingMeta.current_page }} / {{ incomingMeta.last_page }}</span>
          <a
            :href="buildPageUrl('incoming_page', incomingMeta.current_page + 1, outgoingMeta.current_page)"
            class="rounded border border-gray-300 px-3 py-1.5 text-gray-700 hover:bg-gray-50"
            :class="incomingMeta.current_page >= incomingMeta.last_page ? 'pointer-events-none opacity-50' : ''"
          >
            Next
          </a>
        </div>
      </article>

      <article class="rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-2">
          <h2 class="px-4 py-3 text-sm font-bold text-gray-900">Outgoing Requests</h2>
          <span class="px-4 py-3 text-xs font-semibold text-gray-500">Total: {{ stats.sent_total }}</span>
        </div>

        <div class="overflow-x-auto border-y border-gray-200">
          <table class="min-w-full">
            <thead class="bg-gray-50 text-[10px] uppercase tracking-wide text-gray-500">
              <tr>
                <th class="px-3 py-2 text-left">Name</th>
                <th class="px-3 py-2 text-left">City</th>
                <th class="px-3 py-2 text-left">Profession</th>
                <th class="px-3 py-2 text-left">Status</th>
                <th class="px-3 py-2 text-right">View</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-[12px]">
              <tr v-for="request in outgoingRows" :key="`outgoing-${request.profile_id}`" class="hover:bg-gray-50/70">
                <td class="px-3 py-2.5 font-semibold text-gray-900">{{ request.name }}</td>
                <td class="px-3 py-2.5 text-gray-600">{{ request.city }}</td>
                <td class="px-3 py-2.5 text-gray-600">{{ request.profession }}</td>
                <td class="px-3 py-2.5 text-xs font-semibold" :class="requestStatusClass(request.status)">
                  {{ requestStatusLabel(request.status) }}
                </td>
                <td class="px-3 py-2.5 text-right">
                  <a :href="`/profiles/${request.profile_id}/view`" class="rounded border border-gray-300 px-2 py-1 text-[11px] font-semibold text-gray-700 hover:bg-gray-50">Open</a>
                  <p v-if="request.rejection_reason" class="mt-1 text-[11px] text-rose-700">{{ request.rejection_reason }}</p>
                </td>
              </tr>
              <tr v-if="!outgoingRows.length">
                <td colspan="5" class="px-3 py-5 text-center text-xs text-gray-500">No outgoing requests.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="outgoingMeta.last_page > 1" class="mt-4 flex items-center justify-between text-xs">
          <a
            :href="buildPageUrl('outgoing_page', incomingMeta.current_page, outgoingMeta.current_page - 1)"
            class="rounded border border-gray-300 px-3 py-1.5 text-gray-700 hover:bg-gray-50"
            :class="outgoingMeta.current_page <= 1 ? 'pointer-events-none opacity-50' : ''"
          >
            Previous
          </a>
          <span class="text-gray-600">Page {{ outgoingMeta.current_page }} / {{ outgoingMeta.last_page }}</span>
          <a
            :href="buildPageUrl('outgoing_page', incomingMeta.current_page, outgoingMeta.current_page + 1)"
            class="rounded border border-gray-300 px-3 py-1.5 text-gray-700 hover:bg-gray-50"
            :class="outgoingMeta.current_page >= outgoingMeta.last_page ? 'pointer-events-none opacity-50' : ''"
          >
            Next
          </a>
        </div>
      </article>
    </section>
  </UserDashboardLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import UserDashboardLayout from '@/Layouts/UserDashboardLayout.vue';

const props = defineProps({
  requestOverview: {
    type: Object,
    default: () => ({
      stats: {},
    }),
  },
  incoming: {
    type: Object,
    default: () => ({
      data: [],
      meta: {},
    }),
  },
  outgoing: {
    type: Object,
    default: () => ({
      data: [],
      meta: {},
    }),
  },
});

const stats = computed(() => ({
  received_total: 0,
  received_pending: 0,
  received_accepted: 0,
  received_rejected: 0,
  sent_total: 0,
  sent_pending: 0,
  sent_accepted: 0,
  sent_rejected: 0,
  ...(props.requestOverview?.stats || {}),
}));

const incomingRows = computed(() => props.incoming?.data || []);
const outgoingRows = computed(() => props.outgoing?.data || []);

const incomingMeta = computed(() => ({
  current_page: 1,
  last_page: 1,
  ...(props.incoming?.meta || {}),
}));

const outgoingMeta = computed(() => ({
  current_page: 1,
  last_page: 1,
  ...(props.outgoing?.meta || {}),
}));

const loadingMap = ref({});

const requestStatusLabel = (status) => {
  const current = String(status || 'none').toLowerCase();
  if (current === 'accepted') return 'Accepted';
  if (current === 'rejected') return 'Rejected';
  if (current === 'pending') return 'Pending';
  if (current === 'skipped') return 'Skipped';
  return 'Not Sent';
};

const requestStatusClass = (status) => {
  const current = String(status || 'none').toLowerCase();
  if (current === 'accepted') return 'text-emerald-700';
  if (current === 'rejected') return 'text-rose-700';
  if (current === 'pending') return 'text-sky-700';
  if (current === 'skipped') return 'text-amber-700';
  return 'text-gray-600';
};

const buildPageUrl = (source, incomingPage, outgoingPage) => {
  const incoming = Math.max(1, Number(incomingPage || 1));
  const outgoing = Math.max(1, Number(outgoingPage || 1));

  const params = new URLSearchParams();
  params.set('incoming_page', String(incoming));
  params.set('outgoing_page', String(outgoing));

  return `/dashboard/requests?${params.toString()}`;
};

const submitMatchAction = async (targetProfileId, action, rejectionReason = null) => {
  if (!targetProfileId || !['connect', 'skip'].includes(action)) {
    return;
  }

  loadingMap.value = {
    ...loadingMap.value,
    [targetProfileId]: true,
  };

  try {
    await window.axios.post('/dashboard/match-action', {
      target_profile_id: targetProfileId,
      action,
      rejection_reason: action === 'skip' ? rejectionReason : null,
    });

    window.location.reload();
  } catch (error) {
    const message = error?.response?.data?.message || 'Unable to save request action right now.';
    window.alert(message);
  } finally {
    const nextMap = { ...loadingMap.value };
    delete nextMap[targetProfileId];
    loadingMap.value = nextMap;
  }
};

const rejectIncomingRequest = async (profileId) => {
  const reason = window.prompt('Please provide a rejection reason:');
  if (reason === null) {
    return;
  }

  await submitMatchAction(profileId, 'skip', String(reason || '').trim() || 'No reason provided.');
};
</script>
