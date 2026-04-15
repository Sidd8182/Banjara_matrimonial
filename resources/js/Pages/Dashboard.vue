<template>
  <UserDashboardLayout active-module="overview">
    <section class="grid grid-cols-2 gap-3 xl:grid-cols-6">
      <article class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
        <p class="text-[11px] text-slate-500">Membership</p>
        <p class="mt-1 text-sm font-bold text-slate-900">{{ membershipBadgeLabel }}</p>
      </article>
      <article class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
        <p class="text-[11px] text-slate-500">Profile Completion</p>
        <p class="mt-1 text-2xl font-bold text-emerald-600">{{ profileCompletionPercent }}%</p>
      </article>
      <article class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
        <p class="text-[11px] text-slate-500">Received Pending</p>
        <p class="mt-1 text-2xl font-bold text-amber-600">{{ requestStats.received_pending }}</p>
      </article>
      <article class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
        <p class="text-[11px] text-slate-500">Sent Pending</p>
        <p class="mt-1 text-2xl font-bold text-sky-600">{{ requestStats.sent_pending }}</p>
      </article>
      <article class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
        <p class="text-[11px] text-slate-500">Sent Accepted</p>
        <p class="mt-1 text-2xl font-bold text-emerald-600">{{ requestStats.sent_accepted }}</p>
      </article>
      <article class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
        <p class="text-[11px] text-slate-500">Sent Rejected</p>
        <p class="mt-1 text-2xl font-bold text-rose-600">{{ requestStats.sent_rejected }}</p>
      </article>
    </section>

    <section class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-3">
      <article class="xl:col-span-2 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
          <h3 class="text-sm font-bold text-slate-900">Recommended Matches Table</h3>
          <a href="/browse" class="text-xs font-semibold text-indigo-600 hover:underline">Open Browse</a>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-slate-50 text-[10px] uppercase tracking-wide text-slate-500">
              <tr>
                <th class="px-4 py-2 text-left">Profile</th>
                <th class="px-4 py-2 text-left">Location</th>
                <th class="px-4 py-2 text-left">Profession</th>
                <th class="px-4 py-2 text-left">Compatibility</th>
                <th class="px-4 py-2 text-left">Request Status</th>
                <th class="px-4 py-2 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-[12px]">
              <tr v-for="profile in recommendedProfiles.slice(0, 12)" :key="profile.id" class="hover:bg-slate-50/70">
                <td class="px-4 py-2.5 font-semibold text-slate-900">
                  <a :href="`/profiles/${profile.id}/view`" class="text-blue-700 hover:underline">
                    {{ profile.name }}<span v-if="profile.age">, {{ profile.age }}</span>
                  </a>
                </td>
                <td class="px-4 py-2.5 text-slate-600">{{ profile.city }}</td>
                <td class="px-4 py-2.5 text-slate-600">{{ profile.profession }}</td>
                <td class="px-4 py-2.5 font-semibold text-emerald-700">{{ profile.compatibility }}%</td>
                <td class="px-4 py-2.5 text-xs font-semibold" :class="requestStatusClass(profile.request_status)">
                  {{ requestStatusLabel(profile.request_status) }}
                </td>
                <td class="px-4 py-2.5">
                  <div class="flex justify-end gap-2">
                    <button
                      type="button"
                      class="rounded-md border border-rose-200 px-3 py-1 text-[11px] font-semibold text-rose-700 hover:bg-rose-50 disabled:opacity-60"
                      :disabled="isActionLoading(profile.id)"
                      @click="submitMatchAction(profile.id, 'skip')"
                    >
                      Skip
                    </button>
                    <button
                      type="button"
                      class="rounded-md bg-indigo-600 px-3 py-1 text-[11px] font-semibold text-white hover:bg-indigo-500 disabled:opacity-60"
                      :disabled="isActionLoading(profile.id)"
                      @click="submitMatchAction(profile.id, 'connect')"
                    >
                      Connect
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!recommendedProfiles.length">
                <td colspan="6" class="px-4 py-6 text-center text-xs text-slate-500">No recommended matches available.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </article>

      <article class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-4 py-3">
          <h3 class="text-sm font-bold text-slate-900">Profile Summary Table</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <tbody class="divide-y divide-slate-100 text-[12px]">
              <tr>
                <td class="px-4 py-2.5 text-slate-500">Name</td>
                <td class="px-4 py-2.5 font-semibold text-slate-900">{{ profileDisplayName }}</td>
              </tr>
              <tr>
                <td class="px-4 py-2.5 text-slate-500">Profile ID</td>
                <td class="px-4 py-2.5 font-semibold text-slate-900">{{ profileIdLabel }}</td>
              </tr>
              <tr>
                <td class="px-4 py-2.5 text-slate-500">Membership Status</td>
                <td class="px-4 py-2.5 font-semibold text-slate-900">{{ membershipStatusLabel }}</td>
              </tr>
              <tr>
                <td class="px-4 py-2.5 text-slate-500">Plan</td>
                <td class="px-4 py-2.5 font-semibold text-slate-900">{{ membershipPlanLabel }}</td>
              </tr>
              <tr>
                <td class="px-4 py-2.5 text-slate-500">Valid Till</td>
                <td class="px-4 py-2.5 font-semibold text-slate-900">{{ membershipEndDateLabel || 'N/A' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </article>
    </section>

    <section class="mt-8 rounded-2xl bg-white border border-gray-200 p-6 shadow-sm">
      <div class="mb-4 flex items-center justify-between gap-3">
        <h3 class="text-2xl font-bold text-gray-900">Request Preview (Tabular)</h3>
        <a
          href="/dashboard/requests"
          class="inline-flex items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100"
        >
          Open Full Request Center
        </a>
      </div>

      <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
        <article class="rounded-xl border border-gray-200">
          <div class="border-b border-gray-200 px-4 py-3">
            <h4 class="text-sm font-bold text-gray-900">Incoming Requests ({{ incomingRequests.length }})</h4>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-gray-50 text-[10px] uppercase tracking-wide text-gray-500">
                <tr>
                  <th class="px-3 py-2 text-left">Name</th>
                  <th class="px-3 py-2 text-left">City</th>
                  <th class="px-3 py-2 text-left">Status</th>
                  <th class="px-3 py-2 text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 text-[12px]">
                <tr v-for="request in incomingRequests" :key="`incoming-${request.profile_id}`">
                  <td class="px-3 py-2">{{ request.name }}</td>
                  <td class="px-3 py-2">{{ request.city }}</td>
                  <td class="px-3 py-2" :class="requestStatusClass(request.status)">{{ requestStatusLabel(request.status) }}</td>
                  <td class="px-3 py-2">
                    <div class="flex justify-end gap-2">
                      <a :href="`/profiles/${request.profile_id}/view`" class="rounded border border-gray-300 px-2 py-1 text-[11px] font-semibold text-gray-700 hover:bg-gray-50">View</a>
                      <button
                        v-if="request.status === 'pending'"
                        type="button"
                        class="rounded bg-emerald-600 px-2 py-1 text-[11px] font-semibold text-white hover:bg-emerald-700 disabled:opacity-60"
                        :disabled="isActionLoading(request.profile_id)"
                        @click="submitMatchAction(request.profile_id, 'connect')"
                      >
                        Accept
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-if="!incomingRequests.length">
                  <td colspan="4" class="px-3 py-4 text-center text-xs text-gray-500">No incoming requests.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </article>

        <article class="rounded-xl border border-gray-200">
          <div class="border-b border-gray-200 px-4 py-3">
            <h4 class="text-sm font-bold text-gray-900">Outgoing Requests ({{ outgoingRequests.length }})</h4>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-gray-50 text-[10px] uppercase tracking-wide text-gray-500">
                <tr>
                  <th class="px-3 py-2 text-left">Name</th>
                  <th class="px-3 py-2 text-left">City</th>
                  <th class="px-3 py-2 text-left">Status</th>
                  <th class="px-3 py-2 text-right">View</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 text-[12px]">
                <tr v-for="request in outgoingRequests" :key="`outgoing-${request.profile_id}`">
                  <td class="px-3 py-2">{{ request.name }}</td>
                  <td class="px-3 py-2">{{ request.city }}</td>
                  <td class="px-3 py-2" :class="requestStatusClass(request.status)">{{ requestStatusLabel(request.status) }}</td>
                  <td class="px-3 py-2 text-right">
                    <a :href="`/profiles/${request.profile_id}/view`" class="rounded border border-gray-300 px-2 py-1 text-[11px] font-semibold text-gray-700 hover:bg-gray-50">Open</a>
                  </td>
                </tr>
                <tr v-if="!outgoingRequests.length">
                  <td colspan="4" class="px-3 py-4 text-center text-xs text-gray-500">No outgoing requests.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </article>
      </div>
    </section>

    <section class="mt-4 rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div class="border-b border-slate-200 px-4 py-3">
        <h3 class="text-sm font-bold text-slate-900">Module Navigation Table</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead class="bg-slate-50 text-[10px] uppercase tracking-wide text-slate-500">
            <tr>
              <th class="px-4 py-2 text-left">Module</th>
              <th class="px-4 py-2 text-left">Purpose</th>
              <th class="px-4 py-2 text-right">Open</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-[12px]">
            <tr>
              <td class="px-4 py-2.5 font-semibold text-slate-900">Profile</td>
              <td class="px-4 py-2.5 text-slate-600">Update personal profile details and media.</td>
              <td class="px-4 py-2.5 text-right"><a href="/profiles" class="text-indigo-700 hover:underline">Open</a></td>
            </tr>
            <tr>
              <td class="px-4 py-2.5 font-semibold text-slate-900">Request Center</td>
              <td class="px-4 py-2.5 text-slate-600">Manage incoming and outgoing connection requests.</td>
              <td class="px-4 py-2.5 text-right"><a href="/dashboard/requests" class="text-indigo-700 hover:underline">Open</a></td>
            </tr>
            <tr>
              <td class="px-4 py-2.5 font-semibold text-slate-900">Membership</td>
              <td class="px-4 py-2.5 text-slate-600">Upgrade plan and manage premium access.</td>
              <td class="px-4 py-2.5 text-right"><a href="/pricing" class="text-indigo-700 hover:underline">Open</a></td>
            </tr>
          </tbody>
        </table>
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
  </UserDashboardLayout>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import UserDashboardLayout from '@/Layouts/UserDashboardLayout.vue';

const props = defineProps({
  subscription: {
    type: Object,
    default: null,
  },
  dashboardProfile: {
    type: Object,
    default: null,
  },
  recommendedProfiles: {
    type: Array,
    default: () => [],
  },
  requestOverview: {
    type: Object,
    default: () => ({
      stats: {},
      incoming: [],
      outgoing: [],
    }),
  },
});

const requestStats = ref({
  received_total: 0,
  received_pending: 0,
  received_accepted: 0,
  received_rejected: 0,
  sent_total: 0,
  sent_pending: 0,
  sent_accepted: 0,
  sent_rejected: 0,
  ...(props.requestOverview?.stats || {}),
});

const incomingRequests = ref(Array.isArray(props.requestOverview?.incoming) ? [...props.requestOverview.incoming] : []);
const outgoingRequests = ref(Array.isArray(props.requestOverview?.outgoing) ? [...props.requestOverview.outgoing] : []);
const actionLoadingMap = ref({});

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

const recommendedProfiles = ref(Array.isArray(props.recommendedProfiles) ? [...props.recommendedProfiles] : []);

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

const isActionLoading = (profileId) => Boolean(actionLoadingMap.value[profileId]);

const deriveOutgoingStatus = (profileId) => {
  const outgoing = outgoingRequests.value.find((item) => Number(item.profile_id) === Number(profileId));
  return outgoing?.status || 'none';
};

const recalculateStats = () => {
  requestStats.value = {
    received_total: incomingRequests.value.length,
    received_pending: incomingRequests.value.filter((item) => item.status === 'pending').length,
    received_accepted: incomingRequests.value.filter((item) => item.status === 'accepted').length,
    received_rejected: incomingRequests.value.filter((item) => item.status === 'rejected').length,
    sent_total: outgoingRequests.value.length,
    sent_pending: outgoingRequests.value.filter((item) => item.status === 'pending').length,
    sent_accepted: outgoingRequests.value.filter((item) => item.status === 'accepted').length,
    sent_rejected: outgoingRequests.value.filter((item) => item.status === 'rejected').length,
  };
};

const syncRecommendedStatuses = () => {
  recommendedProfiles.value = recommendedProfiles.value.map((profile) => {
    const nextStatus = deriveOutgoingStatus(profile.id);
    if (nextStatus === 'none') {
      return profile;
    }

    return {
      ...profile,
      request_status: nextStatus,
    };
  });
};

const submitMatchAction = async (targetProfileId, action, rejectionReason = null) => {
  if (!targetProfileId || !['connect', 'skip'].includes(action)) {
    return;
  }

  actionLoadingMap.value = {
    ...actionLoadingMap.value,
    [targetProfileId]: true,
  };

  try {
    await window.axios.post('/dashboard/match-action', {
      target_profile_id: targetProfileId,
      action,
      rejection_reason: action === 'skip' ? rejectionReason : null,
    });

    if (action === 'connect') {
      const currentOutgoing = outgoingRequests.value.find((item) => Number(item.profile_id) === Number(targetProfileId));
      if (!currentOutgoing) {
        const targetProfile = recommendedProfiles.value.find((item) => Number(item.id) === Number(targetProfileId));
        if (targetProfile) {
          outgoingRequests.value.unshift({
            profile_id: Number(targetProfile.id),
            name: targetProfile.name,
            city: targetProfile.city,
            profession: targetProfile.profession,
            profile_picture_url: targetProfile.profile_picture_url || null,
            requested_at: new Date().toISOString(),
            status: 'pending',
            rejection_reason: null,
          });
        }
      } else {
        currentOutgoing.status = 'pending';
        currentOutgoing.rejection_reason = null;
      }

      const incomingItem = incomingRequests.value.find((item) => Number(item.profile_id) === Number(targetProfileId));
      if (incomingItem && incomingItem.status === 'pending') {
        incomingItem.status = 'accepted';
        incomingItem.rejection_reason = null;
      }
    }

    if (action === 'skip') {
      const outgoingItem = outgoingRequests.value.find((item) => Number(item.profile_id) === Number(targetProfileId));
      if (outgoingItem && outgoingItem.status === 'pending') {
        outgoingItem.status = 'rejected';
        outgoingItem.rejection_reason = rejectionReason || 'No reason provided.';
      }

      const incomingItem = incomingRequests.value.find((item) => Number(item.profile_id) === Number(targetProfileId));
      if (incomingItem && incomingItem.status === 'pending') {
        incomingItem.status = 'rejected';
        incomingItem.rejection_reason = rejectionReason || 'No reason provided.';
      }
    }

    recalculateStats();
    syncRecommendedStatuses();
  } catch (error) {
    const message = error?.response?.data?.message || 'Unable to save request action right now.';
    window.alert(message);
  } finally {
    const nextMap = { ...actionLoadingMap.value };
    delete nextMap[targetProfileId];
    actionLoadingMap.value = nextMap;
  }
};

const rejectIncomingRequest = async (profileId) => {
  const reason = window.prompt('Please provide a rejection reason:');
  if (reason === null) {
    return;
  }

  await submitMatchAction(profileId, 'skip', String(reason || '').trim() || 'No reason provided.');
};

recalculateStats();
syncRecommendedStatuses();

const recentActivities = [
  { text: 'Anjali viewed your profile', time: '2 min ago', dot: 'bg-rose-500' },
  { text: 'New message from Karan', time: '10 min ago', dot: 'bg-orange-500' },
  { text: '3 new compatible matches found', time: '35 min ago', dot: 'bg-emerald-500' },
  { text: 'Your profile appeared in 14 searches', time: '1 hour ago', dot: 'bg-indigo-500' },
];
</script>
