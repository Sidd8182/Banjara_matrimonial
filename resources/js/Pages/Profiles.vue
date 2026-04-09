<template>
  <MainLayout>
    <section class="rounded-3xl bg-white border border-gray-200 shadow-sm overflow-hidden">
      <div class="bg-gradient-to-r from-rose-600 via-red-600 to-orange-500 px-6 py-8 sm:px-8 text-white">
        <h1 class="text-2xl sm:text-3xl font-extrabold">Matrimonial Profile Wizard</h1>
        <p class="mt-2 text-white/90 max-w-3xl">
          Har section independently save hota hai. Aap kisi bhi step par jaakar data fill ya update kar sakte ho.
        </p>

        <div class="mt-5 flex flex-wrap gap-4 text-sm">
          <p class="rounded-lg bg-white/20 px-3 py-1.5">Profile ID: <strong>{{ profileIdLabel }}</strong></p>
          <p class="rounded-lg bg-white/20 px-3 py-1.5">Age: <strong>{{ derivedAge || '-' }}</strong></p>
          <p class="rounded-lg bg-white/20 px-3 py-1.5">Height: <strong>{{ derivedHeightFeet || '-' }} ft</strong></p>
        </div>

        <div class="mt-5">
          <div class="h-2.5 w-full max-w-xl rounded-full bg-white/30 overflow-hidden">
            <div class="h-full rounded-full bg-white" :style="{ width: `${completionPercent}%` }"></div>
          </div>
          <p class="mt-2 text-sm text-white/90">{{ completionPercent }}% profile completed</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12">
        <aside class="lg:col-span-4 border-r border-gray-200 bg-rose-50/40 p-5 sm:p-6">
          <div class="space-y-2.5">
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
        </aside>

        <div class="lg:col-span-8 p-5 sm:p-7">
          <div v-if="statusMessage" class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700 text-sm">
            {{ statusMessage }}
          </div>

          <div class="mb-4 rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs text-slate-600">
            Step {{ activeStep }}: <strong>{{ currentStepTitle }}</strong>
            <span v-if="!isEditingStep" class="ml-2 text-emerald-700">Saved mode. Edit karne ke liye Edit Step dabao.</span>
            <span v-else class="ml-2 text-amber-700">Editing mode. Save Step par click karke changes save karo.</span>
          </div>

          <form @submit.prevent="saveCurrentStep" class="space-y-6">
            <fieldset :disabled="!isEditingStep || form.processing" :class="!isEditingStep ? 'opacity-75' : ''" class="space-y-6">
            <div v-if="activeStep === 1" class="space-y-4">
              <h2 class="text-2xl font-bold text-gray-900">Basic Information</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FieldInput label="First Name*" v-model="form.first_name" :error="form.errors.first_name" />
                <FieldInput label="Last Name*" v-model="form.last_name" :error="form.errors.last_name" />
                <FieldSelect label="Gender*" v-model="form.gender" :options="['Male', 'Female', 'Other']" :error="form.errors.gender" />
                <FieldInput label="Date of Birth*" type="date" v-model="form.date_of_birth" :error="form.errors.date_of_birth" />
                <FieldInput label="Height (cm)" type="number" v-model="form.height_cm" :error="form.errors.height_cm" />
                <FieldInput label="Weight (kg)" type="number" step="0.1" v-model="form.weight_kg" :error="form.errors.weight_kg" />
                <FieldSelect label="Marital Status*" v-model="form.marital_status" :options="['Never Married', 'Divorced', 'Widowed']" :error="form.errors.marital_status" />
                <FieldInput label="Mother Tongue*" v-model="form.mother_tongue" :error="form.errors.mother_tongue" />
                <FieldInput label="Religion*" v-model="form.religion" :error="form.errors.religion" />
                <FieldInput label="Caste" v-model="form.caste" :error="form.errors.caste" />
                <FieldInput label="Sub-caste" v-model="form.sub_caste" :error="form.errors.sub_caste" />
                <FieldInput label="Gotra" v-model="form.gotra" :error="form.errors.gotra" />
                <FieldSelect label="Profile Created By*" v-model="form.profile_created_by" :options="['Self', 'Parent', 'Sibling', 'Relative']" :error="form.errors.profile_created_by" />
              </div>
            </div>

            <div v-if="activeStep === 2" class="space-y-4">
              <h2 class="text-2xl font-bold text-gray-900">Location Details</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FieldInput label="Country*" v-model="form.country" :error="form.errors.country" />
                <FieldInput label="State*" v-model="form.state" :error="form.errors.state" />
                <FieldInput label="City*" v-model="form.city" :error="form.errors.city" />
                <FieldInput label="Area / Locality" v-model="form.area_locality" :error="form.errors.area_locality" />
                <FieldInput label="Pincode" v-model="form.pincode" :error="form.errors.pincode" />
                <FieldSelect label="Willing to Relocate" v-model="form.willing_to_relocate" :options="yesNoOptions" :error="form.errors.willing_to_relocate" />
              </div>
              <FieldTextarea label="Current Address" rows="3" v-model="form.current_address" :error="form.errors.current_address" />
              <FieldTextarea label="Permanent Address" rows="3" v-model="form.permanent_address" :error="form.errors.permanent_address" />
            </div>

            <div v-if="activeStep === 3" class="space-y-4">
              <h2 class="text-2xl font-bold text-gray-900">Education & Career</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FieldInput label="Highest Qualification*" v-model="form.highest_qualification" :error="form.errors.highest_qualification" />
                <FieldInput label="Degree" v-model="form.degree" :error="form.errors.degree" />
                <FieldInput label="College / University" v-model="form.college_university" :error="form.errors.college_university" />
                <FieldInput label="Field of Study" v-model="form.field_of_study" :error="form.errors.field_of_study" />
                <FieldSelect label="Occupation*" v-model="form.occupation_type" :options="['Job', 'Business', 'Profession', 'Student', 'Other']" :error="form.errors.occupation_type" />
                <FieldInput label="Company Name" v-model="form.company_name" :error="form.errors.company_name" />
                <FieldInput label="Job Title" v-model="form.job_title" :error="form.errors.job_title" />
                <FieldInput label="Annual Income Range" v-model="form.annual_income_range" :error="form.errors.annual_income_range" placeholder="5-8 LPA" />
                <FieldInput label="Work Location" v-model="form.work_location" :error="form.errors.work_location" />
                <FieldSelect label="Work Type" v-model="form.work_type" :options="['Remote', 'Office', 'Hybrid']" :error="form.errors.work_type" />
              </div>
            </div>

            <div v-if="activeStep === 4" class="space-y-4">
              <h2 class="text-2xl font-bold text-gray-900">Family Details</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FieldInput label="Father Name" v-model="form.father_name" :error="form.errors.father_name" />
                <FieldInput label="Father Occupation" v-model="form.father_occupation" :error="form.errors.father_occupation" />
                <FieldInput label="Mother Name" v-model="form.mother_name" :error="form.errors.mother_name" />
                <FieldInput label="Mother Occupation" v-model="form.mother_occupation" :error="form.errors.mother_occupation" />
                <FieldInput label="Number of Brothers" type="number" v-model="form.brothers_count" :error="form.errors.brothers_count" />
                <FieldInput label="Number of Sisters" type="number" v-model="form.sisters_count" :error="form.errors.sisters_count" />
                <FieldSelect label="Family Type" v-model="form.family_type" :options="['Joint', 'Nuclear']" :error="form.errors.family_type" />
                <FieldSelect label="Family Status" v-model="form.family_status" :options="['Middle Class', 'Upper Middle Class', 'Affluent']" :error="form.errors.family_status" />
                <FieldSelect label="Family Values" v-model="form.family_values" :options="['Traditional', 'Moderate', 'Modern']" :error="form.errors.family_values" />
              </div>
            </div>

            <div v-if="activeStep === 5" class="space-y-4">
              <h2 class="text-2xl font-bold text-gray-900">Lifestyle & Personality</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FieldSelect label="Diet" v-model="form.diet" :options="['Veg', 'Non-Veg', 'Jain']" :error="form.errors.diet" />
                <FieldSelect label="Smoking" v-model="form.smoking" :options="['No', 'Occasionally', 'Yes']" :error="form.errors.smoking" />
                <FieldSelect label="Drinking" v-model="form.drinking" :options="['No', 'Occasionally', 'Yes']" :error="form.errors.drinking" />
                <FieldInput label="Hobbies (comma separated)" v-model="hobbiesText" />
                <FieldInput label="Interests (comma separated)" v-model="interestsText" />
              </div>
              <FieldTextarea label="About Me (max 500 words)" rows="5" v-model="form.about_me" :error="form.errors.about_me" />
            </div>

            <div v-if="activeStep === 6" class="space-y-4">
              <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-2xl font-bold text-gray-900">Horoscope Details (Optional)</h2>
                <button
                  type="button"
                  class="rounded-lg border border-indigo-300 bg-indigo-50 px-4 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 disabled:opacity-60"
                  :disabled="form.processing || autoFetching || !isEditingStep || !astrologyConfig.autoFetchEnabled"
                  @click="autoFetchHoroscope"
                >
                  {{ autoFetching ? 'Fetching...' : 'Auto Fetch (DOB + Time + Place)' }}
                </button>
              </div>
              <p v-if="astrologyConfig.autoFetchEnabled" class="text-xs text-slate-500">
                DOB, time aur place ke basis par Rashi, Nakshatra aur Lagna auto fetch ho sakte hain.
              </p>
              <p v-else class="text-xs text-amber-700">
                Auto fetch disabled hai. Manual entry mode active hai.
              </p>
              <p v-if="autoFetchMessage" class="text-xs text-emerald-700">{{ autoFetchMessage }}</p>
              <p v-if="autoFetchError" class="text-xs text-red-600">{{ autoFetchError }}</p>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FieldInput label="Date of Birth" type="date" v-model="form.horoscope_date_of_birth" :error="form.errors.horoscope_date_of_birth" />
                <FieldInput label="Time of Birth" v-model="form.time_of_birth" :error="form.errors.time_of_birth" />
                <FieldInput label="Place of Birth" v-model="form.place_of_birth" :error="form.errors.place_of_birth" />
                <FieldInput label="State of Birth" v-model="form.birth_state" :error="form.errors.birth_state" />
                <FieldSearchSelect label="Rashi" list-id="rashi-options" v-model="form.rashi" :options="rashiOptions" :error="form.errors.rashi" />
                <FieldSearchSelect label="Nakshatra" list-id="nakshatra-options" v-model="form.nakshatra" :options="nakshatraOptions" :error="form.errors.nakshatra" />
                <FieldInput label="Lagna" v-model="form.lagna" :error="form.errors.lagna" />
                <FieldSelect label="Manglik" v-model="form.manglik" :options="['Yes', 'No', 'Partial']" :error="form.errors.manglik" />
              </div>
              <FileInput label="Horoscope Upload (PDF/Image)" accept=".pdf,.jpg,.jpeg,.png,.webp" :error="form.errors.horoscope_file" @change="onSingleFile('horoscope_file', $event)" />
              <p v-if="form.horoscope_path" class="text-xs text-emerald-700">Existing file available.</p>
            </div>

            <div v-if="activeStep === 7" class="space-y-4">
              <h2 class="text-2xl font-bold text-gray-900">Photos & Media</h2>
              <FileInput label="Profile Picture*" accept=".jpg,.jpeg,.png,.webp" :error="form.errors.profile_picture" @change="onSingleFile('profile_picture', $event)" />
              <FileInput label="Gallery Images (max 10 total)" accept=".jpg,.jpeg,.png,.webp" multiple :error="form.errors.gallery_images" @change="onMultipleFiles('gallery_images', $event)" />
              <FileInput label="Video Intro (Premium)" accept=".mp4,.mov,.webm" :error="form.errors.video_intro" @change="onSingleFile('video_intro', $event)" />
              <FieldSelect label="Privacy Setting" v-model="form.media_privacy" :options="['Public', 'Protected']" :error="form.errors.media_privacy" />

              <div v-if="galleryImages.length" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <img v-for="image in galleryImages" :key="image.id" :src="image.url" class="h-24 w-full object-cover rounded-lg border border-gray-200" alt="Gallery" />
              </div>
            </div>

            <div v-if="activeStep === 8" class="space-y-4">
              <h2 class="text-2xl font-bold text-gray-900">Contact Details (Protected)</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FieldInput label="Mobile Number" v-model="form.contact_mobile" :error="form.errors.contact_mobile" />
                <FieldInput label="Email ID" v-model="form.contact_email" :error="form.errors.contact_email" />
                <FieldInput label="WhatsApp Number" v-model="form.whatsapp_number" :error="form.errors.whatsapp_number" />
                <FieldSelect label="Contact Visibility" v-model="form.contact_visibility" :options="['Public', 'Premium Only']" :error="form.errors.contact_visibility" />
              </div>
            </div>

            <div v-if="activeStep === 9" class="space-y-4">
              <h2 class="text-2xl font-bold text-gray-900">Partner Preferences</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FieldInput label="Age Min" type="number" v-model="form.age_min" :error="form.errors.age_min" />
                <FieldInput label="Age Max" type="number" v-model="form.age_max" :error="form.errors.age_max" />
                <FieldInput label="Height Min (cm)" type="number" v-model="form.height_min_cm" :error="form.errors.height_min_cm" />
                <FieldInput label="Height Max (cm)" type="number" v-model="form.height_max_cm" :error="form.errors.height_max_cm" />
                <FieldInput label="Religion Preference" v-model="form.religion_preference" :error="form.errors.religion_preference" />
                <FieldInput label="Caste Preference" v-model="form.caste_preference" :error="form.errors.caste_preference" />
                <FieldInput label="Location Preference" v-model="form.location_preference" :error="form.errors.location_preference" />
                <FieldInput label="Minimum Qualification" v-model="form.minimum_qualification" :error="form.errors.minimum_qualification" />
                <FieldInput label="Preferred Profession" v-model="form.preferred_profession" :error="form.errors.preferred_profession" />
                <FieldInput label="Income Expectation" v-model="form.income_expectation" :error="form.errors.income_expectation" />
                <FieldSelect label="Diet Preference" v-model="form.diet_preference" :options="['Veg', 'Non-Veg', 'Jain', 'Any']" :error="form.errors.diet_preference" />
                <FieldSelect label="Smoking Preference" v-model="form.smoking_preference" :options="['No', 'Occasionally', 'Yes', 'Any']" :error="form.errors.smoking_preference" />
                <FieldSelect label="Drinking Preference" v-model="form.drinking_preference" :options="['No', 'Occasionally', 'Yes', 'Any']" :error="form.errors.drinking_preference" />
                <FieldSelect label="Manglik Preference" v-model="form.manglik_preference" :options="['Yes', 'No', 'Partial', 'Any']" :error="form.errors.manglik_preference" />
                <FieldSelect label="Willing to Relocate" v-model="form.relocate_preference" :options="yesNoOptions" :error="form.errors.relocate_preference" />
              </div>
              <FieldTextarea label="Expectations" rows="4" v-model="form.expectations" :error="form.errors.expectations" />
            </div>

            <div v-if="activeStep === 10" class="space-y-4">
              <h2 class="text-2xl font-bold text-gray-900">Verification & Trust</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FieldSelect label="ID Proof Type" v-model="form.id_proof_type" :options="['Aadhar', 'PAN', 'Passport', 'Driving License']" :error="form.errors.id_proof_type" />
                <FieldSelect label="Profile Verified Badge" v-model="form.profile_verified_badge" :options="yesNoOptions" :error="form.errors.profile_verified_badge" />
                <FieldSelect label="Photo Verified" v-model="form.photo_verified" :options="yesNoOptions" :error="form.errors.photo_verified" />
                <FieldSelect label="Mobile Verified" v-model="form.mobile_verified" :options="yesNoOptions" :error="form.errors.mobile_verified" />
                <FieldSelect label="Email Verified" v-model="form.email_verified" :options="yesNoOptions" :error="form.errors.email_verified" />
              </div>
              <FileInput label="ID Proof Upload" accept=".pdf,.jpg,.jpeg,.png,.webp" :error="form.errors.id_proof_file" @change="onSingleFile('id_proof_file', $event)" />
            </div>
            </fieldset>

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
                v-if="!isEditingStep"
                type="button"
                class="px-6 py-2.5 rounded-lg border border-indigo-300 text-indigo-700 font-semibold hover:bg-indigo-50 transition"
                :disabled="form.processing"
                @click="startEditingStep"
              >
                Edit Step
              </button>

              <button
                type="submit"
                class="px-6 py-2.5 rounded-lg bg-primary text-white font-semibold hover:bg-red-700 transition disabled:opacity-70"
                :disabled="form.processing || !isEditingStep"
              >
                {{ form.processing ? 'Saving...' : 'Save Step' }}
              </button>

              <button
                type="button"
                class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition"
                :disabled="activeStep === 10 || form.processing"
                @click="activeStep = Math.min(10, activeStep + 1)"
              >
                Next
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
  profileData: {
    type: Object,
    default: () => ({}),
  },
  updateStepUrl: {
    type: String,
    required: true,
  },
  autoFetchHoroscopeUrl: {
    type: String,
    default: '',
  },
  astrologyConfig: {
    type: Object,
    default: () => ({
      enabled: false,
      autoFetchEnabled: false,
      matchmakingEnabled: false,
      normalMode: true,
    }),
  },
  masterData: {
    type: Object,
    default: () => ({
      rashis: [],
      nakshatras: [],
    }),
  },
});

const page = usePage();
const data = props.profileData || {};
const yesNoOptions = ['Yes', 'No'];
const rashiOptions = computed(() => props.masterData?.rashis || []);
const nakshatraOptions = computed(() => props.masterData?.nakshatras || []);
const astrologyConfig = computed(() => props.astrologyConfig || { enabled: false, autoFetchEnabled: false, matchmakingEnabled: false, normalMode: true });
const autoFetching = ref(false);
const autoFetchMessage = ref('');
const autoFetchError = ref('');

const steps = [
  { id: 1, title: 'Basic Information' },
  { id: 2, title: 'Location Details' },
  { id: 3, title: 'Education & Career' },
  { id: 4, title: 'Family Details' },
  { id: 5, title: 'Lifestyle & About Me' },
  { id: 6, title: 'Horoscope Details' },
  { id: 7, title: 'Photos & Media' },
  { id: 8, title: 'Contact Details' },
  { id: 9, title: 'Partner Preferences' },
  { id: 10, title: 'Verification & Trust' },
];

const activeStep = ref(Math.min(Math.max(Number(data.meta?.last_completed_step || 1), 1), 10));
const highestSavedStep = ref(Math.min(Math.max(Number(data.meta?.last_completed_step || 1), 1), 10));
const isEditingStep = ref(activeStep.value > highestSavedStep.value);

const form = useForm({
  first_name: data.basic?.first_name || '',
  last_name: data.basic?.last_name || '',
  gender: data.basic?.gender || '',
  date_of_birth: data.basic?.date_of_birth || '',
  height_cm: data.basic?.height_cm || '',
  weight_kg: data.basic?.weight_kg || '',
  marital_status: data.basic?.marital_status || '',
  mother_tongue: data.basic?.mother_tongue || '',
  religion: data.basic?.religion || '',
  caste: data.basic?.caste || '',
  sub_caste: data.basic?.sub_caste || '',
  gotra: data.basic?.gotra || '',
  profile_created_by: data.basic?.profile_created_by || '',

  country: data.location?.country || '',
  state: data.location?.state || '',
  city: data.location?.city || '',
  area_locality: data.location?.area_locality || '',
  pincode: data.location?.pincode || '',
  current_address: data.location?.current_address || '',
  permanent_address: data.location?.permanent_address || '',
  willing_to_relocate: data.location?.willing_to_relocate === null ? '' : (data.location?.willing_to_relocate ? 'Yes' : 'No'),

  highest_qualification: data.education?.highest_qualification || '',
  degree: data.education?.degree || '',
  college_university: data.education?.college_university || '',
  field_of_study: data.education?.field_of_study || '',
  occupation_type: data.career?.occupation_type || '',
  company_name: data.career?.company_name || '',
  job_title: data.career?.job_title || '',
  annual_income_range: data.career?.annual_income_range || '',
  work_location: data.career?.work_location || '',
  work_type: data.career?.work_type || '',

  father_name: data.family?.father_name || '',
  father_occupation: data.family?.father_occupation || '',
  mother_name: data.family?.mother_name || '',
  mother_occupation: data.family?.mother_occupation || '',
  brothers_count: data.family?.brothers_count || '',
  sisters_count: data.family?.sisters_count || '',
  family_type: data.family?.family_type || '',
  family_status: data.family?.family_status || '',
  family_values: data.family?.family_values || '',

  diet: data.lifestyle?.diet || '',
  smoking: data.lifestyle?.smoking || '',
  drinking: data.lifestyle?.drinking || '',
  hobbies: data.lifestyle?.hobbies || [],
  interests: data.lifestyle?.interests || [],
  about_me: data.lifestyle?.about_me || '',

  horoscope_date_of_birth: data.horoscope?.horoscope_date_of_birth || '',
  time_of_birth: data.horoscope?.time_of_birth || '',
  place_of_birth: data.horoscope?.place_of_birth || '',
  birth_state: data.horoscope?.birth_state || '',
  rashi: data.horoscope?.rashi || '',
  nakshatra: data.horoscope?.nakshatra || '',
  lagna: data.horoscope?.lagna || '',
  manglik: data.horoscope?.manglik || '',
  horoscope_file: null,

  profile_picture: null,
  gallery_images: [],
  video_intro: null,
  media_privacy: data.media?.media_privacy || 'Protected',

  contact_mobile: data.contact?.contact_mobile || '',
  contact_email: data.contact?.contact_email || '',
  whatsapp_number: data.contact?.whatsapp_number || '',
  contact_visibility: data.contact?.contact_visibility || 'Premium Only',

  age_min: data.preferences?.age_min || '',
  age_max: data.preferences?.age_max || '',
  height_min_cm: data.preferences?.height_min_cm || '',
  height_max_cm: data.preferences?.height_max_cm || '',
  religion_preference: data.preferences?.religion_preference || '',
  caste_preference: data.preferences?.caste_preference || '',
  location_preference: data.preferences?.location_preference || '',
  minimum_qualification: data.preferences?.minimum_qualification || '',
  preferred_profession: data.preferences?.preferred_profession || '',
  income_expectation: data.preferences?.income_expectation || '',
  diet_preference: data.preferences?.diet_preference || '',
  smoking_preference: data.preferences?.smoking_preference || '',
  drinking_preference: data.preferences?.drinking_preference || '',
  manglik_preference: data.preferences?.manglik_preference || '',
  relocate_preference: data.preferences?.relocate_preference === null ? '' : (data.preferences?.relocate_preference ? 'Yes' : 'No'),
  expectations: data.preferences?.expectations || '',

  profile_verified_badge: data.verification?.profile_verified_badge ? 'Yes' : 'No',
  id_proof_type: data.verification?.id_proof_type || '',
  id_proof_file: null,
  photo_verified: data.verification?.photo_verified ? 'Yes' : 'No',
  mobile_verified: data.verification?.mobile_verified ? 'Yes' : 'No',
  email_verified: data.verification?.email_verified ? 'Yes' : 'No',
});

const hobbiesText = ref((data.lifestyle?.hobbies || []).join(', '));
const interestsText = ref((data.lifestyle?.interests || []).join(', '));

const statusMessage = computed(() => page.props.flash?.status || '');
const completionPercent = computed(() => Math.round((highestSavedStep.value / 10) * 100));
const profileIdLabel = computed(() => data.meta?.profile_id || 'Auto Generated');
const currentStepTitle = computed(() => steps.find((step) => step.id === activeStep.value)?.title || 'Profile Step');

const derivedAge = computed(() => {
  if (!form.date_of_birth) {
    return data.meta?.age || '';
  }

  const dob = new Date(form.date_of_birth);
  if (Number.isNaN(dob.getTime())) {
    return '';
  }

  const today = new Date();
  let age = today.getFullYear() - dob.getFullYear();
  const m = today.getMonth() - dob.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
    age -= 1;
  }
  return age;
});

const derivedHeightFeet = computed(() => {
  const cm = Number(form.height_cm || 0);
  if (!cm) {
    return data.meta?.height_feet || '';
  }
  return (cm / 30.48).toFixed(2);
});

const galleryImages = computed(() => data.media?.gallery || []);

const normalizeCommaSeparated = (text) =>
  (text || '')
    .split(',')
    .map((value) => value.trim())
    .filter((value) => value.length > 0);

const boolOrNull = (value) => {
  if (value === 'Yes' || value === true) {
    return true;
  }
  if (value === 'No' || value === false) {
    return false;
  }
  return null;
};

const stepFields = {
  1: ['first_name', 'last_name', 'gender', 'date_of_birth', 'height_cm', 'weight_kg', 'marital_status', 'mother_tongue', 'religion', 'caste', 'sub_caste', 'gotra', 'profile_created_by'],
  2: ['country', 'state', 'city', 'area_locality', 'pincode', 'current_address', 'permanent_address', 'willing_to_relocate'],
  3: ['highest_qualification', 'degree', 'college_university', 'field_of_study', 'occupation_type', 'company_name', 'job_title', 'annual_income_range', 'work_location', 'work_type'],
  4: ['father_name', 'father_occupation', 'mother_name', 'mother_occupation', 'brothers_count', 'sisters_count', 'family_type', 'family_status', 'family_values'],
  5: ['diet', 'smoking', 'drinking', 'hobbies', 'interests', 'about_me'],
  6: ['horoscope_date_of_birth', 'time_of_birth', 'place_of_birth', 'birth_state', 'rashi', 'nakshatra', 'lagna', 'manglik', 'horoscope_file'],
  7: ['profile_picture', 'gallery_images', 'video_intro', 'media_privacy'],
  8: ['contact_mobile', 'contact_email', 'whatsapp_number', 'contact_visibility'],
  9: ['age_min', 'age_max', 'height_min_cm', 'height_max_cm', 'religion_preference', 'caste_preference', 'location_preference', 'minimum_qualification', 'preferred_profession', 'income_expectation', 'diet_preference', 'smoking_preference', 'drinking_preference', 'manglik_preference', 'relocate_preference', 'expectations'],
  10: ['profile_verified_badge', 'id_proof_type', 'id_proof_file', 'photo_verified', 'mobile_verified', 'email_verified'],
};

const pickStepPayload = (step) => {
  const payload = { step };

  stepFields[step].forEach((key) => {
    payload[key] = form[key];
  });

  if (step === 2) {
    payload.willing_to_relocate = boolOrNull(form.willing_to_relocate);
  }

  if (step === 5) {
    payload.hobbies = normalizeCommaSeparated(hobbiesText.value);
    payload.interests = normalizeCommaSeparated(interestsText.value);
  }

  if (step === 9) {
    payload.relocate_preference = boolOrNull(form.relocate_preference);
  }

  if (step === 10) {
    payload.profile_verified_badge = boolOrNull(form.profile_verified_badge) || false;
    payload.photo_verified = boolOrNull(form.photo_verified) || false;
    payload.mobile_verified = boolOrNull(form.mobile_verified) || false;
    payload.email_verified = boolOrNull(form.email_verified) || false;
  }

  return payload;
};

const saveCurrentStep = () => {
  const current = activeStep.value;
  form
    .transform(() => pickStepPayload(current))
    .post(props.updateStepUrl, {
      preserveScroll: true,
      forceFormData: true,
      onSuccess: () => {
        highestSavedStep.value = Math.max(highestSavedStep.value, current);
        isEditingStep.value = false;
      },
    });
};

const autoFetchHoroscope = async () => {
  autoFetchMessage.value = '';
  autoFetchError.value = '';

  if (!props.autoFetchHoroscopeUrl || !astrologyConfig.value.autoFetchEnabled) {
    autoFetchError.value = 'Auto fetch is not enabled.';
    return;
  }

  if (!form.horoscope_date_of_birth || !form.time_of_birth || !form.place_of_birth) {
    autoFetchError.value = 'Date, time aur place of birth required hai auto fetch ke liye.';
    return;
  }

  autoFetching.value = true;
  try {
    const response = await window.axios.post(props.autoFetchHoroscopeUrl, {
      horoscope_date_of_birth: form.horoscope_date_of_birth,
      time_of_birth: form.time_of_birth,
      place_of_birth: form.place_of_birth,
      birth_state: form.birth_state,
    });

    const payload = response?.data?.data || {};
    if (payload.rashi) {
      form.rashi = payload.rashi;
    }
    if (payload.nakshatra) {
      form.nakshatra = payload.nakshatra;
    }
    if (payload.lagna) {
      form.lagna = payload.lagna;
    }

    autoFetchMessage.value = response?.data?.message || 'Horoscope details fetched.';
  } catch (error) {
    autoFetchError.value = error?.response?.data?.message || 'Auto fetch failed. Aap manual entry continue kar sakte ho.';
  } finally {
    autoFetching.value = false;
  }
};

const goToStep = (step) => {
  activeStep.value = step;
  isEditingStep.value = step > highestSavedStep.value;
  form.clearErrors();
};

const startEditingStep = () => {
  isEditingStep.value = true;
};

const onSingleFile = (key, event) => {
  const file = event.target.files?.[0] || null;
  form[key] = file;
};

const onMultipleFiles = (key, event) => {
  const files = event.target.files ? Array.from(event.target.files) : [];
  form[key] = files;
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
    step: { type: String, default: 'any' },
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
          step: componentProps.step,
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
    modelValue: { type: [String, Boolean], default: '' },
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
    rows: { type: String, default: '4' },
    error: { type: String, default: '' },
  },
  emits: ['update:modelValue'],
  setup(componentProps, { emit }) {
    return () =>
      h('div', [
        h('label', { class: 'block text-sm font-semibold text-gray-700 mb-1.5' }, componentProps.label),
        h('textarea', {
          value: componentProps.modelValue,
          rows: componentProps.rows,
          class: `${baseInputClass} resize-none`,
          onInput: (event) => emit('update:modelValue', event.target.value),
        }),
        componentProps.error ? h('p', { class: 'mt-1 text-xs text-red-600' }, componentProps.error) : null,
      ]);
  },
});

const FieldSearchSelect = defineComponent({
  name: 'FieldSearchSelect',
  props: {
    modelValue: { type: String, default: '' },
    label: { type: String, required: true },
    listId: { type: String, required: true },
    options: { type: Array, default: () => [] },
    error: { type: String, default: '' },
  },
  emits: ['update:modelValue'],
  setup(componentProps, { emit }) {
    return () =>
      h('div', [
        h('label', { class: 'block text-sm font-semibold text-gray-700 mb-1.5' }, componentProps.label),
        h('input', {
          value: componentProps.modelValue,
          list: componentProps.listId,
          class: baseInputClass,
          placeholder: `Search ${componentProps.label}`,
          onInput: (event) => emit('update:modelValue', event.target.value),
        }),
        h(
          'datalist',
          { id: componentProps.listId },
          (componentProps.options || []).map((option) => h('option', { value: option }, option))
        ),
        componentProps.error ? h('p', { class: 'mt-1 text-xs text-red-600' }, componentProps.error) : null,
      ]);
  },
});

const FileInput = defineComponent({
  name: 'FileInput',
  props: {
    label: { type: String, required: true },
    accept: { type: String, default: '' },
    multiple: { type: Boolean, default: false },
    error: { type: String, default: '' },
  },
  emits: ['change'],
  setup(componentProps, { emit }) {
    return () =>
      h('div', [
        h('label', { class: 'block text-sm font-semibold text-gray-700 mb-1.5' }, componentProps.label),
        h('input', {
          type: 'file',
          accept: componentProps.accept,
          multiple: componentProps.multiple,
          class: `${baseInputClass} file:mr-3 file:rounded file:border-0 file:bg-rose-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-rose-700`,
          onChange: (event) => emit('change', event),
        }),
        componentProps.error ? h('p', { class: 'mt-1 text-xs text-red-600' }, componentProps.error) : null,
      ]);
  },
});
</script>
