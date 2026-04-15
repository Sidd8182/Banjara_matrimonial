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
            <p class="text-[10px] uppercase tracking-[0.2em] text-slate-500">Website Settings</p>
            <h1 class="text-xl font-bold text-slate-900">Pages</h1>
            <p class="text-xs text-slate-500 mt-1">Create dynamic pages for CMS routing and section placement.</p>
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
          <h2 class="text-sm font-bold text-slate-900">Create Page</h2>
          <form class="mt-3 grid grid-cols-1 md:grid-cols-4 gap-3" @submit.prevent="createPage">
            <input v-model="createForm.title" type="text" placeholder="Page title" class="field md:col-span-2" />
            <input v-model="createForm.slug" type="text" placeholder="Slug (optional)" class="field" />
            <input v-model="createForm.sort_order" type="number" min="0" placeholder="Sort order" class="field" />
            <label class="inline-flex items-center gap-2 text-xs text-slate-600 md:col-span-3">
              <input v-model="createForm.is_active" type="checkbox" /> Active
            </label>
            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800" :disabled="creating">
              {{ creating ? 'Creating...' : 'Create Page' }}
            </button>
          </form>
        </section>

        <section class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
          <h2 class="text-sm font-bold text-slate-900">All Pages</h2>
          <div class="mt-3 space-y-3">
            <article v-for="pageItem in localPages" :key="pageItem.id" class="rounded-lg border border-slate-200 p-3">
              <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <input v-model="pageItem.title" class="field md:col-span-2" placeholder="e.g., About Us" />
                <input v-model="pageItem.slug" class="field" placeholder="e.g., about-us" />
                <input v-model="pageItem.sort_order" type="number" min="0" class="field" placeholder="e.g., 1" />
                <label class="inline-flex items-center gap-2 text-xs text-slate-600">
                  <input v-model="pageItem.is_active" type="checkbox" /> Active
                </label>
              </div>
              <div class="mt-3 flex gap-2">
                <button @click="savePage(pageItem)" type="button" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-500">Save</button>
                <button @click="deletePage(pageItem.id)" type="button" class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">Delete</button>
                <a
                  v-if="pageItem.slug"
                  :href="`/pages/${pageItem.slug}`"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="rounded-lg border border-emerald-200 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-50"
                >
                  Preview
                </a>
              </div>
            </article>
            <p v-if="!localPages.length" class="text-xs text-slate-500">No pages yet.</p>
          </div>
        </section>
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps({
  pages: { type: Array, default: () => [] },
  createAction: { type: String, required: true },
  baseUrl: { type: String, required: true },
  sidebarMenus: { type: Array, default: () => [] },
  logoutAction: { type: String, required: true },
});

const page = usePage();
const statusMessage = computed(() => page.props.flash?.status || '');
const sidebarMenus = computed(() => props.sidebarMenus || []);
const creating = ref(false);

const localPages = ref((props.pages || []).map((item) => ({ ...item })));

const createForm = reactive({
  title: '',
  slug: '',
  sort_order: 0,
  is_active: true,
});

const openMenus = reactive(Object.fromEntries((sidebarMenus.value || []).map((menu) => [menu.label, menu.label === 'Website Settings'])));

const toggleMenu = (label) => {
  openMenus[label] = !openMenus[label];
};

const createPage = () => {
  creating.value = true;
  router.post(props.createAction, {
    title: createForm.title,
    slug: createForm.slug,
    sort_order: Number(createForm.sort_order || 0),
    is_active: Boolean(createForm.is_active),
  }, {
    preserveScroll: true,
    onFinish: () => {
      creating.value = false;
    },
  });
};

const savePage = (pageItem) => {
  router.put(`${props.baseUrl}/cms-pages/${pageItem.id}`, {
    title: pageItem.title,
    slug: pageItem.slug,
    sort_order: Number(pageItem.sort_order || 0),
    is_active: Boolean(pageItem.is_active),
  }, { preserveScroll: true });
};

const deletePage = (id) => {
  if (!window.confirm('Delete this page?')) {
    return;
  }

  router.delete(`${props.baseUrl}/cms-pages/${id}`, { preserveScroll: true });
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
