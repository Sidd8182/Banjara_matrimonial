<template>
  <div class="min-h-screen bg-slate-100 text-[13px] text-slate-700">
    <div class="grid min-h-screen grid-cols-1 lg:grid-cols-[280px_1fr]">
      <aside class="bg-slate-950 text-slate-200 px-4 py-5 border-r border-slate-800">
        <p class="text-[10px] uppercase tracking-[0.22em] text-cyan-300">Banjara SaaS Console</p>
        <h1 class="mt-1 text-xl font-bold text-white">Super Admin</h1>

        <div class="mt-6 space-y-2">
          <div
            v-for="menu in sidebarMenus"
            :key="menu.label"
            class="rounded-lg border border-slate-800 bg-slate-900/60"
          >
            <button
              type="button"
              class="w-full px-3 py-2.5 flex items-center justify-between text-left text-xs font-semibold text-slate-100 hover:bg-slate-800/80 rounded-lg"
              @click="toggleMenu(menu.label)"
            >
              <span>{{ menu.label }}</span>
              <span class="text-[11px]">{{ openMenus[menu.label] ? '▾' : '▸' }}</span>
            </button>

            <div v-if="openMenus[menu.label]" class="px-2 pb-2 space-y-1">
              <div v-for="item in menu.items" :key="`${menu.label}-${item.label}`">
                <a :href="item.url || '#'" class="block rounded-md px-2.5 py-1.5 text-[12px] text-slate-300 hover:bg-slate-800 hover:text-white">
                  {{ item.label }}
                </a>
                <div v-if="item.children" class="ml-3 mt-1 space-y-1 border-l border-slate-700 pl-2">
                  <a
                    v-for="child in item.children"
                    :key="`${item.label}-${child.label}`"
                    :href="child.url || '#'"
                    class="block rounded-md px-2 py-1 text-[11px] text-slate-400 hover:bg-slate-800 hover:text-slate-100"
                  >
                    {{ child.label }}
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </aside>

      <main class="px-4 sm:px-6 lg:px-7 py-5">
        <header class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm flex items-center justify-between gap-3">
          <div>
            <p class="text-[10px] uppercase tracking-[0.2em] text-slate-500">Super Admin</p>
            <h1 class="text-xl font-bold text-slate-900">Pricing and Features Management</h1>
          </div>
          <form @submit.prevent="logout">
            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition">
              Logout
            </button>
          </form>
        </header>

        <div v-if="statusMessage" class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-emerald-700 text-sm">
          {{ statusMessage }}
        </div>

        <section class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
          <h2 class="text-sm font-bold text-slate-900">Create New Plan</h2>
          <form class="mt-3 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3" @submit.prevent="createPlan">
            <input v-model="createForm.name" type="text" placeholder="Plan name" class="field" />
            <input v-model="createForm.price" type="number" step="0.01" min="0" placeholder="Price" class="field" />
            <select v-model="createForm.billing_cycle" class="field">
              <option value="monthly">Monthly</option>
              <option value="quarterly">Quarterly</option>
              <option value="yearly">Yearly</option>
              <option value="lifetime">Lifetime</option>
            </select>
            <input v-model="createForm.sort_order" type="number" min="0" placeholder="Sort order" class="field" />
            <input v-model="createForm.description" type="text" placeholder="Short description" class="field md:col-span-2 xl:col-span-2" />
            <input v-model="createForm.featuresText" type="text" placeholder="Features comma separated" class="field md:col-span-2" />
            <label class="inline-flex items-center gap-2 text-xs text-slate-600"><input v-model="createForm.is_active" type="checkbox" /> Active</label>
            <label class="inline-flex items-center gap-2 text-xs text-slate-600"><input v-model="createForm.is_visible" type="checkbox" /> Visible</label>
            <label class="inline-flex items-center gap-2 text-xs text-slate-600"><input v-model="createForm.is_recommended" type="checkbox" /> Recommended</label>
            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800" :disabled="creating">
              {{ creating ? 'Creating...' : 'Create Plan' }}
            </button>
          </form>
        </section>

        <section class="mt-4 grid grid-cols-1 xl:grid-cols-2 gap-4">
          <article v-for="plan in localPlans" :key="plan.id" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <input v-model="plan.name" type="text" class="field" />
              <input v-model="plan.price" type="number" step="0.01" min="0" class="field" />
              <select v-model="plan.billing_cycle" class="field">
                <option value="monthly">Monthly</option>
                <option value="quarterly">Quarterly</option>
                <option value="yearly">Yearly</option>
                <option value="lifetime">Lifetime</option>
              </select>
              <input v-model="plan.sort_order" type="number" min="0" class="field" />
              <input v-model="plan.description" type="text" class="field md:col-span-2" placeholder="Description" />
              <div class="md:col-span-2 flex flex-wrap gap-3 text-xs text-slate-600">
                <label class="inline-flex items-center gap-2"><input v-model="plan.is_active" type="checkbox" /> Active</label>
                <label class="inline-flex items-center gap-2"><input v-model="plan.is_visible" type="checkbox" /> Visible</label>
                <label class="inline-flex items-center gap-2"><input v-model="plan.is_recommended" type="checkbox" /> Recommended</label>
              </div>
            </div>

            <div class="mt-3 rounded-xl border border-slate-200 p-3">
              <p class="text-xs font-semibold text-slate-800">Plan Features</p>
              <ul class="mt-2 space-y-1.5">
                <li v-for="feature in plan.features" :key="feature.id" class="flex items-center justify-between gap-2 text-xs">
                  <span class="text-slate-700">{{ feature.feature_text }}</span>
                  <button @click="removeFeature(feature.id)" type="button" class="rounded border border-rose-200 px-2 py-0.5 text-rose-600 hover:bg-rose-50">Remove</button>
                </li>
                <li v-if="!plan.features.length" class="text-xs text-slate-500">No features yet.</li>
              </ul>
              <div class="mt-2 flex gap-2">
                <input v-model="newFeatureByPlan[plan.id]" type="text" placeholder="Add feature" class="field !py-1.5" />
                <button @click="addFeature(plan.id)" type="button" class="rounded border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">Add</button>
              </div>
            </div>

            <div class="mt-3 flex flex-wrap gap-2">
              <button @click="savePlan(plan)" type="button" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-500">Save Changes</button>
              <button @click="deletePlan(plan.id)" type="button" class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">Delete Plan</button>
            </div>
          </article>
        </section>
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps({
  plans: {
    type: Array,
    default: () => [],
  },
  createAction: {
    type: String,
    required: true,
  },
  baseUrl: {
    type: String,
    required: true,
  },
  dashboardUrl: {
    type: String,
    required: true,
  },
  integrationSettingsUrl: {
    type: String,
    required: true,
  },
  subscriptionsUrl: {
    type: String,
    required: true,
  },
  sidebarMenus: {
    type: Array,
    default: () => [],
  },
  logoutAction: {
    type: String,
    required: true,
  },
});

const page = usePage();
const statusMessage = computed(() => page.props.flash?.status || '');
const creating = ref(false);
const sidebarMenus = computed(() => props.sidebarMenus || []);

const createForm = reactive({
  name: '',
  price: '',
  billing_cycle: 'monthly',
  description: '',
  sort_order: 0,
  is_active: true,
  is_visible: true,
  is_recommended: false,
  featuresText: '',
});

const localPlans = ref(
  props.plans.map((plan) => ({
    ...plan,
    features: plan.features || [],
  }))
);

const newFeatureByPlan = reactive({});

const openMenus = reactive(
  Object.fromEntries((sidebarMenus.value || []).map((menu) => [menu.label, menu.label === 'Subscriptions' || menu.label === 'Platform Settings']))
);

const toggleMenu = (label) => {
  openMenus[label] = !openMenus[label];
};

const createPlan = () => {
  creating.value = true;

  const features = createForm.featuresText
    .split(',')
    .map((item) => item.trim())
    .filter((item) => item.length > 0);

  router.post(props.createAction, {
    name: createForm.name,
    price: createForm.price,
    billing_cycle: createForm.billing_cycle,
    description: createForm.description,
    sort_order: Number(createForm.sort_order || 0),
    is_active: createForm.is_active,
    is_visible: createForm.is_visible,
    is_recommended: createForm.is_recommended,
    features,
  }, {
    preserveScroll: true,
    onFinish: () => {
      creating.value = false;
    },
  });
};

const savePlan = (plan) => {
  router.put(`${props.baseUrl}/pricing-plans/${plan.id}`, {
    name: plan.name,
    price: Number(plan.price || 0),
    billing_cycle: plan.billing_cycle,
    description: plan.description || '',
    sort_order: Number(plan.sort_order || 0),
    is_active: Boolean(plan.is_active),
    is_visible: Boolean(plan.is_visible),
    is_recommended: Boolean(plan.is_recommended),
  }, {
    preserveScroll: true,
  });
};

const deletePlan = (planId) => {
  if (!window.confirm('Delete this plan?')) {
    return;
  }

  router.delete(`${props.baseUrl}/pricing-plans/${planId}`, {
    preserveScroll: true,
  });
};

const addFeature = (planId) => {
  const featureText = (newFeatureByPlan[planId] || '').trim();
  if (!featureText) {
    return;
  }

  router.post(`${props.baseUrl}/pricing-plans/${planId}/features`, {
    feature_text: featureText,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      newFeatureByPlan[planId] = '';
    },
  });
};

const removeFeature = (featureId) => {
  router.delete(`${props.baseUrl}/pricing-plan-features/${featureId}`, {
    preserveScroll: true,
  });
};

const logout = () => {
  router.post(props.logoutAction);
};
</script>

<style scoped>
.field {
  width: 100%;
  border-radius: 0.5rem;
  border: 1px solid #cbd5e1;
  padding: 0.5rem 0.75rem;
  font-size: 0.75rem;
  line-height: 1rem;
  outline: none;
}

.field:focus {
  border-color: #67e8f9;
  box-shadow: 0 0 0 2px rgba(103, 232, 249, 0.35);
}
</style>
