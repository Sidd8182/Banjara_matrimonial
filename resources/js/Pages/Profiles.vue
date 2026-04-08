<template>
  <MainLayout>
    <section class="rounded-3xl bg-white border border-gray-200 shadow-sm overflow-hidden">
      <div class="bg-gradient-to-r from-rose-600 via-red-600 to-orange-500 px-6 py-8 sm:px-8 text-white">
        <h1 class="text-2xl sm:text-3xl font-extrabold">Complete Your Matrimonial Profile</h1>
        <p class="mt-2 text-white/90 max-w-3xl">
          Step-by-step details bhariye, har step save hota jayega. Complete profile se better quality matches milte hain.
        </p>

        <div class="mt-5">
          <div class="h-2.5 w-full max-w-xl rounded-full bg-white/30 overflow-hidden">
            <div class="h-full rounded-full bg-white" :style="{ width: `${completionPercent}%` }"></div>
          </div>
          <p class="mt-2 text-sm text-white/90">{{ completionPercent }}% profile completed</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
        <aside class="lg:col-span-4 border-r border-gray-200 bg-rose-50/50 p-5 sm:p-6">
          <div class="space-y-3">
            <button
              v-for="step in steps"
              :key="step.id"
              type="button"
              class="w-full text-left rounded-xl border px-4 py-3 transition"
              :class="stepButtonClass(step.id)"
              @click="goToStep(step.id)"
            >
              <p class="text-xs uppercase tracking-wide font-semibold">Step {{ step.id }}</p>
              <p class="font-bold mt-0.5">{{ step.title }}</p>
            </button>
          </div>

          <div class="mt-6 rounded-xl bg-white border border-rose-100 p-4">
            <p class="text-sm font-bold text-gray-900">Suggested Extra Sections</p>
            <ul class="mt-2 space-y-1 text-sm text-gray-600">
              <li>Partner Preferences</li>
              <li>Lifestyle & Hobbies</li>
              <li>Photo Gallery</li>
              <li>Contact & Verification</li>
            </ul>
          </div>
        </aside>

        <div class="lg:col-span-8 p-5 sm:p-7">
          <div v-if="statusMessage" class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700 text-sm">
            {{ statusMessage }}
          </div>

          <form @submit.prevent="saveCurrentStep" class="space-y-6">
            <div v-if="activeStep === 1" class="space-y-4">
              <h2 class="text-2xl font-bold text-gray-900">Basic Details</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FieldSelect label="Profile For" v-model="form.profile_for" :options="profileForOptions" :error="form.errors.profile_for" />
                <FieldSelect label="Marital Status" v-model="form.marital_status" :options="maritalStatusOptions" :error="form.errors.marital_status" />
                <FieldInput label="Date of Birth" type="date" v-model="form.date_of_birth" :error="form.errors.date_of_birth" />
                <FieldInput label="Height (cm)" type="number" v-model="form.height_cm" :error="form.errors.height_cm" />
                <FieldInput label="Religion" v-model="form.religion" :error="form.errors.religion" />
                <FieldInput label="Mother Tongue" v-model="form.mother_tongue" :error="form.errors.mother_tongue" />
              </div>
            </div>

            <div v-if="activeStep === 2" class="space-y-4">
              <h2 class="text-2xl font-bold text-gray-900">Personal Details</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FieldInput label="Current City" v-model="form.current_city" :error="form.errors.current_city" />
                <FieldInput label="Current State" v-model="form.current_state" :error="form.errors.current_state" />
                <FieldInput label="Current Country" v-model="form.current_country" :error="form.errors.current_country" />
                <FieldSelect label="Diet" v-model="form.diet" :options="dietOptions" :error="form.errors.diet" />
                <FieldSelect label="Smoking" v-model="form.smoke" :options="habitOptions" :error="form.errors.smoke" />
                <FieldSelect label="Drinking" v-model="form.drink" :options="habitOptions" :error="form.errors.drink" />
              </div>
              <FieldTextarea label="About Me" v-model="form.about_me" :error="form.errors.about_me" placeholder="Aapke bare mein 40+ characters likhiye..." />
            </div>

            <div v-if="activeStep === 3" class="space-y-4">
              <h2 class="text-2xl font-bold text-gray-900">Professional Details</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FieldInput label="Highest Education" v-model="form.education" :error="form.errors.education" />
                <FieldInput label="Education Detail" v-model="form.education_detail" :error="form.errors.education_detail" />
                <FieldInput label="Occupation" v-model="form.occupation" :error="form.errors.occupation" />
                <FieldInput label="Annual Income" v-model="form.income" :error="form.errors.income" placeholder="e.g. 8-10 LPA" />
                <FieldInput label="Company Name" v-model="form.company_name" :error="form.errors.company_name" />
              </div>
            </div>

            <div v-if="activeStep === 4" class="space-y-4">
              <h2 class="text-2xl font-bold text-gray-900">Family Details</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FieldSelect label="Family Type" v-model="form.family_type" :options="familyTypeOptions" :error="form.errors.family_type" />
                <FieldSelect label="Family Values" v-model="form.family_values" :options="familyValueOptions" :error="form.errors.family_values" />
                <FieldInput label="Father Occupation" v-model="form.father_occupation" :error="form.errors.father_occupation" />
                <FieldInput label="Mother Occupation" v-model="form.mother_occupation" :error="form.errors.mother_occupation" />
                <FieldInput label="Brothers" type="number" v-model="form.brothers" :error="form.errors.brothers" />
                <FieldInput label="Sisters" type="number" v-model="form.sisters" :error="form.errors.sisters" />
              </div>
            </div>

            <div v-if="activeStep === 5" class="space-y-4">
              <h2 class="text-2xl font-bold text-gray-900">Kundli Details</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FieldSelect label="Manglik" v-model="form.manglik" :options="manglikOptions" :error="form.errors.manglik" />
                <FieldInput label="Rashi" v-model="form.rashi" :error="form.errors.rashi" />
                <FieldInput label="Nakshatra" v-model="form.nakshatra" :error="form.errors.nakshatra" />
                <FieldInput label="Time of Birth" v-model="form.time_of_birth" :error="form.errors.time_of_birth" placeholder="e.g. 08:45 AM" />
                <FieldInput label="Place of Birth" v-model="form.place_of_birth" :error="form.errors.place_of_birth" />
                <FieldInput label="Gotra" v-model="form.gotra" :error="form.errors.gotra" />
              </div>
            </div>

            <div class="pt-2 flex flex-wrap gap-3">
              <button
                type="button"
                class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition"
                :disabled="activeStep === 1 || form.processing"
                @click="activeStep = Math.max(1, activeStep - 1)"
              >
                Previous
              </button>

              <button
                type="submit"
                class="px-6 py-2.5 rounded-lg bg-primary text-white font-semibold hover:bg-red-700 transition disabled:opacity-70"
                :disabled="form.processing"
              >
                {{ activeStep === 5 ? 'Save & Finish' : 'Save & Continue' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </section>
  </MainLayout>
</template>

<script setup>
import { computed, defineComponent, h, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
  profile: {
    type: Object,
    default: () => ({}),
  },
});

const page = usePage();

const steps = [
  { id: 1, title: 'Basic Details' },
  { id: 2, title: 'Personal Details' },
  { id: 3, title: 'Professional Details' },
  { id: 4, title: 'Family Details' },
  { id: 5, title: 'Kundli Details' },
];

const activeStep = ref(Math.min(Math.max(Number(props.profile?.profile_completion_step || 1), 1), 5));
const highestSavedStep = ref(Math.min(Math.max(Number(props.profile?.profile_completion_step || 1), 1), 5));

const form = useForm({
  profile_for: props.profile?.profile_for || '',
  marital_status: props.profile?.marital_status || '',
  date_of_birth: props.profile?.date_of_birth || '',
  height_cm: props.profile?.height_cm || '',
  religion: props.profile?.religion || '',
  mother_tongue: props.profile?.mother_tongue || '',
  current_city: props.profile?.current_city || '',
  current_state: props.profile?.current_state || '',
  current_country: props.profile?.current_country || '',
  diet: props.profile?.diet || '',
  smoke: props.profile?.smoke || '',
  drink: props.profile?.drink || '',
  about_me: props.profile?.about_me || '',
  education: props.profile?.education || '',
  education_detail: props.profile?.education_detail || '',
  occupation: props.profile?.occupation || '',
  income: props.profile?.income || '',
  company_name: props.profile?.company_name || '',
  family_type: props.profile?.family_type || '',
  father_occupation: props.profile?.father_occupation || '',
  mother_occupation: props.profile?.mother_occupation || '',
  brothers: props.profile?.brothers ?? '',
  sisters: props.profile?.sisters ?? '',
  family_values: props.profile?.family_values || '',
  manglik: props.profile?.manglik || '',
  rashi: props.profile?.rashi || '',
  nakshatra: props.profile?.nakshatra || '',
  time_of_birth: props.profile?.time_of_birth || '',
  place_of_birth: props.profile?.place_of_birth || '',
  gotra: props.profile?.gotra || '',
});

const profileForOptions = ['Self', 'Son', 'Daughter', 'Brother', 'Sister', 'Friend', 'Relative'];
const maritalStatusOptions = ['Never Married', 'Divorced', 'Widowed', 'Awaiting Divorce'];
const dietOptions = ['Vegetarian', 'Eggetarian', 'Non-Vegetarian', 'Jain', 'Vegan'];
const habitOptions = ['No', 'Occasionally', 'Yes'];
const familyTypeOptions = ['Joint Family', 'Nuclear Family'];
const familyValueOptions = ['Traditional', 'Moderate', 'Liberal'];
const manglikOptions = ['Yes', 'No', 'Anshik', 'Dont Know'];

const statusMessage = computed(() => page.props.flash?.status || '');
const completionPercent = computed(() => Math.round((highestSavedStep.value / 5) * 100));

const stepFields = {
  1: ['profile_for', 'marital_status', 'date_of_birth', 'height_cm', 'religion', 'mother_tongue'],
  2: ['current_city', 'current_state', 'current_country', 'diet', 'smoke', 'drink', 'about_me'],
  3: ['education', 'education_detail', 'occupation', 'income', 'company_name'],
  4: ['family_type', 'father_occupation', 'mother_occupation', 'brothers', 'sisters', 'family_values'],
  5: ['manglik', 'rashi', 'nakshatra', 'time_of_birth', 'place_of_birth', 'gotra'],
};

const pickStepPayload = (step) => {
  const payload = { step };
  stepFields[step].forEach((key) => {
    payload[key] = form[key];
  });
  return payload;
};

const saveCurrentStep = () => {
  const current = activeStep.value;
  form
    .transform(() => pickStepPayload(current))
    .post('/profiles/step', {
      preserveScroll: true,
      onSuccess: () => {
        highestSavedStep.value = Math.max(highestSavedStep.value, current);
        if (current < 5) {
          activeStep.value = current + 1;
        }
      },
    });
};

const goToStep = (step) => {
  activeStep.value = step;
};

const stepButtonClass = (step) => {
  if (activeStep.value === step) {
    return 'border-rose-300 bg-rose-100 text-rose-700';
  }
  if (step <= highestSavedStep.value) {
    return 'border-emerald-200 bg-emerald-50 text-emerald-700';
  }
  return 'border-gray-200 bg-white text-gray-700 hover:border-rose-200 hover:bg-rose-50';
};

const baseInputClass =
  'w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-rose-400 focus:ring-2 focus:ring-rose-100 focus:outline-none';

const FieldInput = defineComponent({
  name: 'FieldInput',
  props: {
    modelValue: { type: [String, Number], default: '' },
    label: { type: String, required: true },
    type: { type: String, default: 'text' },
    placeholder: { type: String, default: '' },
    error: { type: String, default: '' },
  },
  emits: ['update:modelValue'],
  setup(componentProps, { emit }) {
    return () =>
      h('div', [
        h('label', { class: 'block text-sm font-semibold text-gray-700 mb-1.5' }, componentProps.label),
        h('input', {
          type: componentProps.type,
          value: componentProps.modelValue,
          placeholder: componentProps.placeholder,
          class: baseInputClass,
          onInput: (event) => emit('update:modelValue', event.target.value),
        }),
        componentProps.error ? h('p', { class: 'mt-1 text-xs text-red-600' }, componentProps.error) : null,
      ]);
  },
});

const FieldSelect = defineComponent({
  name: 'FieldSelect',
  props: {
    modelValue: { type: String, default: '' },
    label: { type: String, required: true },
    options: { type: Array, default: () => [] },
    error: { type: String, default: '' },
  },
  emits: ['update:modelValue'],
  setup(componentProps, { emit }) {
    return () =>
      h('div', [
        h('label', { class: 'block text-sm font-semibold text-gray-700 mb-1.5' }, componentProps.label),
        h(
          'select',
          {
            value: componentProps.modelValue,
            class: baseInputClass,
            onChange: (event) => emit('update:modelValue', event.target.value),
          },
          [
            h('option', { value: '' }, 'Select'),
            ...componentProps.options.map((option) => h('option', { value: option }, option)),
          ]
        ),
        componentProps.error ? h('p', { class: 'mt-1 text-xs text-red-600' }, componentProps.error) : null,
      ]);
  },
});

const FieldTextarea = defineComponent({
  name: 'FieldTextarea',
  props: {
    modelValue: { type: String, default: '' },
    label: { type: String, required: true },
    placeholder: { type: String, default: '' },
    error: { type: String, default: '' },
  },
  emits: ['update:modelValue'],
  setup(componentProps, { emit }) {
    return () =>
      h('div', [
        h('label', { class: 'block text-sm font-semibold text-gray-700 mb-1.5' }, componentProps.label),
        h('textarea', {
          value: componentProps.modelValue,
          rows: 5,
          placeholder: componentProps.placeholder,
          class: `${baseInputClass} resize-none`,
          onInput: (event) => emit('update:modelValue', event.target.value),
        }),
        componentProps.error ? h('p', { class: 'mt-1 text-xs text-red-600' }, componentProps.error) : null,
      ]);
  },
});
</script>
