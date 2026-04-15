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
            <h1 class="text-xl font-bold text-slate-900">Pages Section</h1>
            <p class="text-xs text-slate-500 mt-1">Map sections to one or multiple pages with display order.</p>
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
          <h2 class="text-sm font-bold text-slate-900">FAQ Manager (Home / FAQ Page)</h2>
          <form class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3" @submit.prevent="createFaq">
            <input v-model="faqForm.question" class="field" placeholder="Question" />
            <input v-model="faqForm.sort_order" type="number" min="0" class="field" placeholder="Display order" />
            <textarea v-model="faqForm.answer" rows="3" class="field md:col-span-2" placeholder="Answer"></textarea>
            <div class="md:col-span-2">
              <p class="text-[11px] font-semibold text-slate-700 mb-1">Show on pages</p>
              <div class="flex flex-wrap gap-3">
                <label v-for="target in targetPageOptions" :key="`faq-${target}`" class="inline-flex items-center gap-2 text-xs text-slate-600">
                  <input type="checkbox" :checked="faqForm.target_pages.includes(target)" @change="toggleFaqTarget(target)" /> {{ target }}
                </label>
              </div>
            </div>
            <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-600" :disabled="creatingFaq">
              {{ creatingFaq ? 'Adding...' : 'Add FAQ' }}
            </button>
          </form>

          <div class="mt-4 space-y-2">
            <article v-for="faq in faqSections" :key="`faq-row-${faq.id}`" class="rounded-lg border border-slate-200 p-3">
              <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Question</p>
              <input v-model="faq.title" class="field mt-1" placeholder="Question" />
              <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mt-2">Answer</p>
              <textarea v-model="faq.body" rows="3" class="field mt-1" placeholder="Answer"></textarea>

              <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2">
                <input v-model="faq.sort_order" type="number" min="0" class="field" placeholder="Display order" />
                <label class="inline-flex items-center gap-2 text-xs text-slate-600">
                  <input v-model="faq.is_active" type="checkbox" /> Active
                </label>
              </div>

              <div class="mt-2">
                <p class="text-[11px] font-semibold text-slate-700 mb-1">Show on pages</p>
                <div class="flex flex-wrap gap-3">
                  <label v-for="target in targetPageOptions" :key="`faq-edit-${faq.id}-${target}`" class="inline-flex items-center gap-2 text-xs text-slate-600">
                    <input type="checkbox" :checked="(faq.target_pages || []).includes(target)" @change="toggleEditTarget(faq, target)" /> {{ target }}
                  </label>
                </div>
              </div>

              <div class="mt-2 flex gap-2">
                <button @click="saveSection(faq)" type="button" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-500">Save</button>
                <button @click="deleteSection(faq.id)" type="button" class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">Delete</button>
              </div>
            </article>
            <p v-if="!faqSections.length" class="text-xs text-slate-500">No FAQ added yet.</p>
          </div>
        </section>

        <section class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
          <h2 class="text-sm font-bold text-slate-900">Create Section Item</h2>
          <form class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3" @submit.prevent="createSection">
            <input v-model="createForm.section_name" class="field" placeholder="Section name (e.g. FAQs)" />
            <select v-model="createForm.section_type" class="field">
              <option value="faq">faq</option>
              <option value="html">html</option>
            </select>
            <select v-model="createForm.cms_page_id" class="field">
              <option value="">No page link</option>
              <option v-for="item in pages" :key="item.id" :value="item.id">{{ item.title }}</option>
            </select>
            <input v-model="createForm.sort_order" type="number" min="0" class="field" placeholder="Sort order" />
            <input v-model="createForm.title" class="field md:col-span-2" placeholder="Title / Question" />
            <textarea v-model="createForm.body" rows="3" class="field md:col-span-2" placeholder="Body / Answer"></textarea>
            <div class="md:col-span-2">
              <p class="text-[11px] font-semibold text-slate-700 mb-1">Show on pages</p>
              <div class="flex flex-wrap gap-3">
                <label v-for="target in targetPageOptions" :key="`create-${target}`" class="inline-flex items-center gap-2 text-xs text-slate-600">
                  <input type="checkbox" :checked="createForm.target_pages.includes(target)" @change="toggleCreateTarget(target)" /> {{ target }}
                </label>
              </div>
            </div>
            <label class="inline-flex items-center gap-2 text-xs text-slate-600 md:col-span-2">
              <input v-model="createForm.is_active" type="checkbox" /> Active
            </label>
            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800" :disabled="creating">
              {{ creating ? 'Creating...' : 'Create Section' }}
            </button>
          </form>
        </section>

        <section class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
          <h2 class="text-sm font-bold text-slate-900">All Section</h2>
          <div class="mt-3 space-y-3">
            <article v-for="section in localSections" :key="section.id" class="rounded-lg border border-slate-200 p-3">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <input v-model="section.section_name" class="field" placeholder="e.g., FAQs" />
                <select v-model="section.section_type" class="field">
                  <option value="faq">faq</option>
                  <option value="html">html</option>
                </select>
                <select v-model="section.cms_page_id" class="field">
                  <option :value="null">No page link</option>
                  <option v-for="item in pages" :key="`edit-${section.id}-${item.id}`" :value="item.id">{{ item.title }}</option>
                </select>
                <input v-model="section.sort_order" type="number" min="0" class="field" placeholder="e.g., 10" />
                <input v-model="section.title" class="field md:col-span-2" placeholder="e.g., How does profile verification work?" />
                <textarea v-model="section.body" rows="3" class="field md:col-span-2" placeholder="e.g., Upload valid documents and complete contact verification to get the verified badge."></textarea>
                <div class="md:col-span-2">
                  <p class="text-[11px] font-semibold text-slate-700 mb-1">Show on pages</p>
                  <div class="flex flex-wrap gap-3">
                    <label v-for="target in targetPageOptions" :key="`edit-${section.id}-${target}`" class="inline-flex items-center gap-2 text-xs text-slate-600">
                      <input type="checkbox" :checked="(section.target_pages || []).includes(target)" @change="toggleEditTarget(section, target)" /> {{ target }}
                    </label>
                  </div>
                </div>
                <label class="inline-flex items-center gap-2 text-xs text-slate-600 md:col-span-2">
                  <input v-model="section.is_active" type="checkbox" /> Active
                </label>
              </div>
              <div class="mt-3 flex gap-2">
                <button @click="saveSection(section)" type="button" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-500">Save</button>
                <button @click="deleteSection(section.id)" type="button" class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">Delete</button>
              </div>
            </article>
            <p v-if="!localSections.length" class="text-xs text-slate-500">No sections yet.</p>
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
  sections: { type: Array, default: () => [] },
  pages: { type: Array, default: () => [] },
  targetPageOptions: { type: Array, default: () => [] },
  createAction: { type: String, required: true },
  baseUrl: { type: String, required: true },
  sidebarMenus: { type: Array, default: () => [] },
  logoutAction: { type: String, required: true },
});

const page = usePage();
const statusMessage = computed(() => page.props.flash?.status || '');
const sidebarMenus = computed(() => props.sidebarMenus || []);
const pages = computed(() => props.pages || []);
const targetPageOptions = computed(() => props.targetPageOptions || []);
const creating = ref(false);
const creatingFaq = ref(false);

const localSections = ref((props.sections || []).map((item) => ({
  ...item,
  target_pages: Array.isArray(item.target_pages) ? [...item.target_pages] : [],
})));

const faqSections = computed(() => {
  return localSections.value
    .filter((item) => (item.section_type || '').toLowerCase() === 'faq')
    .sort((a, b) => Number(a.sort_order || 0) - Number(b.sort_order || 0));
});

const faqForm = reactive({
  question: '',
  answer: '',
  target_pages: ['home', 'faq'],
  sort_order: 0,
});

const createForm = reactive({
  cms_page_id: '',
  section_name: 'FAQs',
  section_type: 'faq',
  title: '',
  body: '',
  target_pages: ['home'],
  sort_order: 0,
  is_active: true,
});

const openMenus = reactive(Object.fromEntries((sidebarMenus.value || []).map((menu) => [menu.label, menu.label === 'Website Settings'])));

const toggleMenu = (label) => {
  openMenus[label] = !openMenus[label];
};

const toggleCreateTarget = (target) => {
  if (createForm.target_pages.includes(target)) {
    createForm.target_pages = createForm.target_pages.filter((item) => item !== target);
    return;
  }
  createForm.target_pages = [...createForm.target_pages, target];
};

const toggleFaqTarget = (target) => {
  if (faqForm.target_pages.includes(target)) {
    faqForm.target_pages = faqForm.target_pages.filter((item) => item !== target);
    return;
  }
  faqForm.target_pages = [...faqForm.target_pages, target];
};

const createFaq = () => {
  creatingFaq.value = true;

  router.post(props.createAction, {
    cms_page_id: null,
    section_name: 'FAQs',
    section_type: 'faq',
    title: faqForm.question,
    body: faqForm.answer,
    target_pages: faqForm.target_pages,
    sort_order: Number(faqForm.sort_order || 0),
    is_active: true,
  }, {
    preserveScroll: true,
    onFinish: () => {
      creatingFaq.value = false;
    },
  });
};

const toggleEditTarget = (section, target) => {
  const current = Array.isArray(section.target_pages) ? section.target_pages : [];
  if (current.includes(target)) {
    section.target_pages = current.filter((item) => item !== target);
    return;
  }
  section.target_pages = [...current, target];
};

const createSection = () => {
  creating.value = true;
  router.post(props.createAction, {
    cms_page_id: createForm.cms_page_id || null,
    section_name: createForm.section_name,
    section_type: createForm.section_type,
    title: createForm.title,
    body: createForm.body,
    target_pages: createForm.target_pages,
    sort_order: Number(createForm.sort_order || 0),
    is_active: Boolean(createForm.is_active),
  }, {
    preserveScroll: true,
    onFinish: () => {
      creating.value = false;
    },
  });
};

const saveSection = (section) => {
  router.put(`${props.baseUrl}/cms-sections/${section.id}`, {
    cms_page_id: section.cms_page_id || null,
    section_name: section.section_name,
    section_type: section.section_type,
    title: section.title,
    body: section.body,
    target_pages: section.target_pages || [],
    sort_order: Number(section.sort_order || 0),
    is_active: Boolean(section.is_active),
  }, { preserveScroll: true });
};

const deleteSection = (id) => {
  if (!window.confirm('Delete this section item?')) {
    return;
  }

  router.delete(`${props.baseUrl}/cms-sections/${id}`, { preserveScroll: true });
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
