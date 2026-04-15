<template>
  <MainLayout>
    <section class="full-bleed bg-gradient-to-br from-red-600 via-rose-600 to-orange-500 text-white relative overflow-hidden">
      <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-white/10"></div>
      <div class="absolute -left-16 -bottom-20 h-56 w-56 rounded-full bg-black/10"></div>

      <div class="section-shell py-8 sm:py-12">
        <div class="relative grid grid-cols-1 lg:grid-cols-5 gap-6 lg:gap-8 items-start">
        <div class="lg:col-span-3">
          <p class="inline-flex rounded-full border border-white/40 px-4 py-1 text-xs font-semibold tracking-wide uppercase">
            24+ Years of Matchmaking Trust
          </p>
          <h1 class="mt-4 text-3xl sm:text-5xl font-bold leading-tight">
            The most trusted
            <span class="text-amber-200">matrimony platform</span>
            for Indian families
          </h1>
          <p class="mt-4 text-white/90 text-base sm:text-lg max-w-2xl">
            Create your profile, set preferences, and connect with verified matches.
            Built for serious relationship seekers with privacy and family values at the center.
          </p>

          <div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="rounded-xl bg-white/15 backdrop-blur p-3">
              <p class="text-xl font-bold">40L+</p>
              <p class="text-xs text-white/90">Profiles</p>
            </div>
            <div class="rounded-xl bg-white/15 backdrop-blur p-3">
              <p class="text-xl font-bold">10L+</p>
              <p class="text-xs text-white/90">Success Matches</p>
            </div>
            <div class="rounded-xl bg-white/15 backdrop-blur p-3">
              <p class="text-xl font-bold">100%</p>
              <p class="text-xs text-white/90">Mobile Verified</p>
            </div>
            <div class="rounded-xl bg-white/15 backdrop-blur p-3">
              <p class="text-xl font-bold">24x7</p>
              <p class="text-xs text-white/90">Support Team</p>
            </div>
          </div>
        </div>

        <div class="lg:col-span-2 bg-white text-gray-900 rounded-2xl p-5 sm:p-6 shadow-xl">
          <h2 class="text-xl font-bold">Find your perfect match</h2>
          <p class="text-sm text-gray-600 mt-1">Create profile in 30 seconds</p>

          <div class="mt-4 space-y-3">
            <select class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
              <option>Profile created for</option>
              <option>Myself</option>
              <option>Daughter</option>
              <option>Son</option>
              <option>Sister</option>
              <option>Brother</option>
              <option>Relative</option>
            </select>
            <input type="text" placeholder="Full name" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40" />
            <input type="tel" placeholder="Mobile number" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40" />
            <input type="password" placeholder="Create password" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40" />
          </div>

          <button class="mt-4 w-full rounded-lg bg-primary text-white px-4 py-2.5 font-semibold hover:bg-red-700 transition">Register Free</button>
          <p class="mt-3 text-xs text-gray-500">By registering, you agree to Terms and Privacy Policy.</p>
        </div>
        </div>
      </div>
    </section>

    <section class="mt-10 mb-10">
      <div class="rounded-3xl border border-rose-100 bg-gradient-to-r from-rose-50 via-amber-50 to-sky-50 p-5 sm:p-7 shadow-sm">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-2xl sm:text-3xl font-bold text-gray-900">Featured Profiles</h3>
            <p class="text-sm text-gray-600 mt-1">Fresh profiles from verified members looking for serious relationships.</p>
          </div>
          <a href="/browse" class="hidden sm:inline-block text-sm font-semibold text-primary hover:underline">View all profiles</a>
        </div>

        <div class="profile-slider-mask">
          <div class="profile-slider-track">
            <article
              v-for="profile in slidingProfiles"
              :key="profile.slideKey"
              class="profile-slide-card rounded-2xl border border-gray-200 bg-white p-4 shadow-sm"
            >
              <img
                v-if="profile.profilePictureUrl"
                :src="profile.profilePictureUrl"
                :alt="profile.name"
                class="w-full rounded-xl bg-gray-100 object-cover object-top"
                style="aspect-ratio: 4 / 5;"
              />
              <div
                v-else
                class="w-full rounded-xl"
                :class="profile.bgClass"
                style="aspect-ratio: 4 / 5;"
              ></div>
              <h4 class="mt-3 text-lg font-bold text-gray-900">
                <a :href="profile.profileUrl" class="hover:underline text-blue-700">
                  {{ profile.name }}<span v-if="profile.age">, {{ profile.age }}</span>
                </a>
              </h4>
              <p class="text-sm text-gray-600 mt-1">{{ profile.city }}, {{ profile.profession }}</p>
              <a
                :href="profile.profileUrl"
                class="mt-3 block w-full rounded-lg px-3 py-2 text-center text-sm font-semibold transition"
                :class="profile.buttonClass"
              >
                View Profile
              </a>
            </article>
          </div>
        </div>
      </div>
    </section>

    <section class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="rounded-xl border border-rose-200 bg-gradient-to-br from-rose-50 to-orange-50 p-4">
        <p class="text-sm font-semibold text-gray-900">Verified Profiles</p>
        <p class="text-sm text-gray-600 mt-1">Every profile goes through strict mobile and account checks.</p>
      </div>
      <div class="rounded-xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-lime-50 p-4">
        <p class="text-sm font-semibold text-gray-900">Assisted Matchmaking</p>
        <p class="text-sm text-gray-600 mt-1">Dedicated relationship advisors for faster quality matches.</p>
      </div>
      <div class="rounded-xl border border-sky-200 bg-gradient-to-br from-sky-50 to-indigo-50 p-4">
        <p class="text-sm font-semibold text-gray-900">Privacy Controls</p>
        <p class="text-sm text-gray-600 mt-1">Hide contact details and manage who can view your profile.</p>
      </div>
    </section>

    <section class="full-bleed mt-12 bg-gradient-to-r from-emerald-50 via-teal-50 to-sky-50 border-y border-[#d6e6d8] overflow-hidden">
      <div class="section-shell">
      <div class="grid grid-cols-1 lg:grid-cols-2 items-stretch">
        <div class="p-6 sm:p-10 lg:p-12">
          <div class="flex items-center gap-3">
            <div class="h-12 w-12 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xl">◎</div>
            <div>
              <p class="text-3xl font-bold text-gray-900 leading-none">Assisted Service</p>
              <p class="text-lg text-gray-700 mt-1">Personalised matchmaking service</p>
            </div>
          </div>

          <h3 class="mt-8 text-3xl sm:text-4xl font-bold text-gray-900 leading-tight">
            Find your match
            <span class="text-emerald-600">10x faster</span>
          </h3>
          <p class="mt-3 text-lg text-gray-700 max-w-xl">
            Personalized matchmaking service through expert Relationship Manager.
          </p>

          <div class="mt-8 grid grid-cols-3 gap-3 sm:gap-5 max-w-xl">
            <div class="text-center">
              <div class="h-14 w-14 mx-auto rounded-xl border-2 border-[#b9d6bc] bg-[#eff7ef] flex items-center justify-center text-2xl">🪪</div>
              <p class="mt-3 text-sm sm:text-base text-gray-900">Guaranteed matches</p>
            </div>
            <div class="text-center">
              <div class="h-14 w-14 mx-auto rounded-xl border-2 border-[#b9d6bc] bg-[#eff7ef] flex items-center justify-center text-2xl">💬</div>
              <p class="mt-3 text-sm sm:text-base text-gray-900">Better response</p>
            </div>
            <div class="text-center">
              <div class="h-14 w-14 mx-auto rounded-xl border-2 border-[#b9d6bc] bg-[#eff7ef] flex items-center justify-center text-2xl">⏱</div>
              <p class="mt-3 text-sm sm:text-base text-gray-900">Save time & effort</p>
            </div>
          </div>

          <a href="/browse" class="inline-flex mt-8 w-full sm:w-auto min-w-[220px] items-center justify-center rounded-full bg-orange-500 px-8 py-3 text-white text-xl font-semibold hover:bg-orange-600 transition">
            Know More
            <span class="ml-2">→</span>
          </a>
        </div>

        <div class="relative min-h-[360px] lg:min-h-full bg-gradient-to-b from-[#edf3ee] to-[#e5efe6] flex items-end justify-center">
          <div class="absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-[#dbe8dd] to-transparent"></div>
          <div class="relative w-full max-w-sm lg:max-w-md px-6 pt-10">
            <div class="mx-auto h-[420px] rounded-t-[190px] rounded-b-none bg-gradient-to-b from-gray-700 to-gray-900 border border-gray-500/30 shadow-2xl"></div>
          </div>
        </div>
      </div>
      </div>
    </section>

    <section class="full-bleed mt-12 bg-gradient-to-b from-white to-rose-50 py-10 sm:py-14">
      <div class="section-shell">
        <p class="text-center text-sm sm:text-base font-semibold tracking-wide text-gray-600 uppercase">Success Stories</p>
        <h3 class="text-center text-3xl sm:text-5xl font-bold text-gray-900 leading-tight">
          Millions have found their life partner at BanjaraMatrimony!
        </h3>

        <div
          class="relative mt-8 rounded-3xl bg-white border border-rose-100 p-4 sm:p-7 lg:p-8 shadow-md cursor-grab active:cursor-grabbing select-none"
          @mousedown="startDrag"
          @mousemove="onDragMove"
          @mouseup="endDrag"
          @mouseleave="handleDragLeave"
          @touchstart="startDrag"
          @touchmove="onDragMove"
          @touchend="endDrag"
          @mouseenter="stopAutoStories"
        >
          <span class="absolute -left-3 -top-3 inline-flex h-8 w-8 items-center justify-center rounded-full bg-black text-white text-xl leading-none">“</span>
          <span class="absolute -right-2 -top-2 text-2xl text-orange-400">☺</span>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-10 items-center">
            <div>
              <p class="text-base sm:text-xl lg:text-2xl leading-snug text-gray-700">
                "{{ activeStory.quote }}"
                <a href="#" class="text-orange-500 hover:underline whitespace-nowrap">Read more</a>
              </p>
              <p class="mt-8 text-3xl font-bold text-gray-800">{{ activeStory.couple }}</p>
              <p class="text-3xl text-gray-700 mt-1">BanjaraMatrimony</p>
            </div>

            <div class="flex justify-center lg:justify-end">
              <div class="h-[260px] sm:h-[340px] w-full max-w-[540px] rounded-3xl overflow-hidden shadow-md" :class="activeStory.imageBgClass">
                <div class="h-full w-full bg-black/10"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-8 flex items-center justify-center gap-3 sm:gap-4">
          <button
            class="h-12 w-12 rounded-full border-2 border-rose-300 text-rose-600 bg-white hover:bg-rose-50 transition"
            @click="prevStory"
            aria-label="Previous story"
          >
            ←
          </button>

          <button
            v-for="(story, index) in stories"
            :key="story.id"
            class="h-12 w-12 sm:h-14 sm:w-14 rounded-full border-2 flex items-center justify-center text-sm font-semibold transition"
            :class="index === activeStoryIndex ? 'border-orange-500 text-orange-600 bg-white shadow-sm' : 'border-transparent text-gray-600 bg-white/80 hover:bg-white'"
            @click="selectStory(index)"
          >
            {{ index + 1 }}
          </button>

          <button
            class="h-12 w-12 rounded-full border-2 border-rose-200 text-rose-500 bg-white hover:bg-rose-50 transition"
            @click="nextStory"
            aria-label="Next story"
          >
            →
          </button>
        </div>
      </div>
    </section>

    <section class="full-bleed mt-12 bg-gradient-to-r from-gray-900 to-gray-800 text-white">
      <div class="section-shell py-8 sm:py-10">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
        <div class="md:col-span-2">
          <h3 class="text-2xl sm:text-3xl font-bold">Speed up your partner search with our app</h3>
          <p class="mt-2 text-gray-200">Get instant notifications, chat updates, and match recommendations on mobile.</p>
        </div>
        <div class="flex flex-col sm:flex-row md:flex-col gap-3">
          <a href="#" class="rounded-lg bg-white text-gray-900 px-4 py-2 text-center font-semibold hover:bg-gray-100 transition">Download for Android</a>
          <a href="#" class="rounded-lg border border-white/50 px-4 py-2 text-center font-semibold hover:bg-white/10 transition">Download for iOS</a>
        </div>
      </div>
      </div>
    </section>

    <section class="mt-[50px] mb-[50px]">
      <div class="mx-auto max-w-5xl rounded-2xl border border-emerald-100 bg-gradient-to-br from-white via-white to-emerald-50 p-5 sm:p-10 shadow-sm">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-emerald-300 text-white text-xl font-bold">?</span>
          <h3 class="text-2xl sm:text-4xl font-extrabold text-gray-900">Frequently Asked Questions</h3>
        </div>

        <div class="mt-8 space-y-3">
          <div
            v-for="(faq, index) in faqs"
            :key="faq.question"
            class="rounded-xl border border-gray-300 bg-white"
          >
            <button
              class="w-full px-5 py-3.5 text-left flex items-center justify-between gap-4"
              @click="toggleFaq(index)"
              type="button"
            >
              <span class="text-base sm:text-xl font-medium text-gray-900">{{ faq.question }}</span>
              <span
                class="text-3xl leading-none text-gray-400 transition-transform duration-200"
                :class="activeFaqIndex === index ? 'rotate-45' : ''"
              >
                +
              </span>
            </button>

            <div v-if="activeFaqIndex === index" class="px-5 pb-4">
              <p class="text-sm sm:text-base text-gray-600">{{ faq.answer }}</p>
            </div>
          </div>
        </div>

        <p class="mt-10 text-lg text-gray-800">
          Looking for something else? Refer to our
          <a href="#" class="font-semibold text-orange-500 hover:underline">detailed FAQs</a>
          page
        </p>
      </div>
    </section>
  </MainLayout>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
  featuredProfiles: {
    type: Array,
    default: () => [],
  },
  faqs: {
    type: Array,
    default: () => [],
  },
});

const profiles = [
  {
    id: 1,
    name: 'Priyanka',
    age: 27,
    profession: 'Software Engineer',
    city: 'Pune',
    about: 'Family-oriented, loves books and meaningful conversations.',
    bgClass: 'bg-gradient-to-br from-rose-100 to-amber-100',
    buttonClass: 'border border-rose-300 text-rose-700 bg-rose-50 hover:bg-rose-100',
  },
  {
    id: 2,
    name: 'Rahul',
    age: 30,
    profession: 'Product Manager',
    city: 'Bengaluru',
    about: 'Calm, ambitious, and values traditional family culture.',
    bgClass: 'bg-gradient-to-br from-sky-100 to-indigo-100',
    buttonClass: 'border border-sky-300 text-sky-700 bg-sky-50 hover:bg-sky-100',
  },
  {
    id: 3,
    name: 'Neha',
    age: 26,
    profession: 'Doctor',
    city: 'Ahmedabad',
    about: 'Simple lifestyle, caring nature, and career-focused.',
    bgClass: 'bg-gradient-to-br from-emerald-100 to-lime-100',
    buttonClass: 'border border-emerald-300 text-emerald-700 bg-emerald-50 hover:bg-emerald-100',
  },
  {
    id: 4,
    name: 'Amit',
    age: 29,
    profession: 'Civil Engineer',
    city: 'Jaipur',
    about: 'Respectful and sincere, looking for a compatible partner.',
    bgClass: 'bg-gradient-to-br from-orange-100 to-rose-100',
    buttonClass: 'border border-orange-300 text-orange-700 bg-orange-50 hover:bg-orange-100',
  },
  {
    id: 5,
    name: 'Meera',
    age: 25,
    profession: 'Chartered Accountant',
    city: 'Mumbai',
    about: 'Modern outlook with strong values and family bonding.',
    bgClass: 'bg-gradient-to-br from-purple-100 to-fuchsia-100',
    buttonClass: 'border border-purple-300 text-purple-700 bg-purple-50 hover:bg-purple-100',
  },
];

const effectiveProfiles = computed(() => {
  if (props.featuredProfiles?.length) {
    return props.featuredProfiles;
  }

  return profiles;
});

const slidingProfiles = computed(() => {
  const list = effectiveProfiles.value;
  const duplicated = [...list, ...list];

  return duplicated.map((profile, index) => ({
    ...profile,
    slideKey: `${profile.id}-${index}`,
    profileUrl: profile.profileUrl || `/profiles/${profile.id}/view`,
    profilePictureUrl: profile.profilePictureUrl || null,
  }));
});

const stories = [
  {
    id: 1,
    couple: 'Soumya Sankar & Arjun Madhu',
    quote:
      "Soumya and Arjun connected through BanjaraMatrimony and bonded over shared values. From their first conversation, both felt an instant comfort and clarity about their future together.",
    imageBgClass: 'bg-gradient-to-br from-amber-300 via-orange-300 to-rose-300',
  },
  {
    id: 2,
    couple: 'Priyajit & Monisa',
    quote:
      "We were both serious about marriage and family compatibility. The platform helped us discover each other's profile quickly, and our families connected very smoothly.",
    imageBgClass: 'bg-gradient-to-br from-cyan-300 via-sky-300 to-indigo-300',
  },
  {
    id: 3,
    couple: 'Bichu & Athira',
    quote:
      "Our first meeting was short, but meaningful. The guidance, filters, and genuine profiles on BanjaraMatrimony made our partner search simple and trustworthy.",
    imageBgClass: 'bg-gradient-to-br from-emerald-300 via-teal-300 to-lime-300',
  },
  {
    id: 4,
    couple: 'Vysakh & Pooja',
    quote:
      "We connected through profile interests and quickly realized we had similar life goals. The entire journey was secure, respectful, and family-friendly.",
    imageBgClass: 'bg-gradient-to-br from-violet-300 via-fuchsia-300 to-pink-300',
  },
  {
    id: 5,
    couple: 'Ranjana & Souvik',
    quote:
      "What started as a profile view turned into a beautiful bond. The quality of matches and privacy controls gave us confidence from day one.",
    imageBgClass: 'bg-gradient-to-br from-slate-300 via-zinc-300 to-neutral-300',
  },
];

const activeStoryIndex = ref(0);
const isDraggingStory = ref(false);
const dragStartX = ref(0);
const dragCurrentX = ref(0);
const storyAutoTimer = ref(null);

const activeStory = computed(() => stories[activeStoryIndex.value]);

const prevStory = () => {
  activeStoryIndex.value = activeStoryIndex.value === 0 ? stories.length - 1 : activeStoryIndex.value - 1;
};

const nextStory = () => {
  activeStoryIndex.value = activeStoryIndex.value === stories.length - 1 ? 0 : activeStoryIndex.value + 1;
};

const selectStory = (index) => {
  activeStoryIndex.value = index;
  restartAutoStories();
};

const fallbackFaqs = [
  {
    question: 'How to register on Banjara Matrimony?',
    answer: 'Click Register, fill profile details, verify your mobile number, and complete your partner preferences to start receiving matches.',
  },
  {
    question: 'What are some of the top-notch safety measures in Banjara Matrimony?',
    answer: 'We use mobile verification, profile moderation, privacy controls, and reporting tools to keep interactions safer and more reliable.',
  },
  {
    question: 'What is Banjara Matrimony "Prime"?',
    answer: 'Prime is a trusted-member experience where verified profiles get better visibility and meaningful responses from serious users.',
  },
  {
    question: 'Why choose Banjara Matrimony Premium Membership plan?',
    answer: 'Premium helps with direct communication, higher visibility, and advanced filters so you can find suitable matches faster.',
  },
  {
    question: 'What are the features of Banjara Matrimony?',
    answer: 'Core features include verified profiles, preference filters, interest/chat tools, privacy controls, and assisted matchmaking support.',
  },
  {
    question: 'How can I verify my profile on Banjara Matrimony?',
    answer: 'Complete mobile verification and submit requested details/documents from profile settings to receive a verified profile status.',
  },
  {
    question: 'What is Assisted Service from Banjara Matrimony?',
    answer: 'Assisted Service provides a relationship manager who helps shortlist matches, coordinate responses, and save your time.',
  },
];

const faqs = computed(() => {
  if (props.faqs?.length) {
    return props.faqs;
  }

  return fallbackFaqs;
});

const activeFaqIndex = ref(null);

const toggleFaq = (index) => {
  activeFaqIndex.value = activeFaqIndex.value === index ? null : index;
};

const startAutoStories = () => {
  stopAutoStories();
  storyAutoTimer.value = setInterval(() => {
    nextStory();
  }, 4500);
};

const stopAutoStories = () => {
  if (storyAutoTimer.value) {
    clearInterval(storyAutoTimer.value);
    storyAutoTimer.value = null;
  }
};

const restartAutoStories = () => {
  stopAutoStories();
  startAutoStories();
};

const getClientX = (event) => {
  if (event.touches && event.touches.length > 0) {
    return event.touches[0].clientX;
  }
  if (event.changedTouches && event.changedTouches.length > 0) {
    return event.changedTouches[0].clientX;
  }
  return event.clientX;
};

const startDrag = (event) => {
  isDraggingStory.value = true;
  dragStartX.value = getClientX(event);
  dragCurrentX.value = dragStartX.value;
  stopAutoStories();
};

const onDragMove = (event) => {
  if (!isDraggingStory.value) return;
  dragCurrentX.value = getClientX(event);
};

const endDrag = () => {
  if (!isDraggingStory.value) return;

  const diff = dragCurrentX.value - dragStartX.value;
  const threshold = 60;

  if (Math.abs(diff) > threshold) {
    if (diff < 0) {
      nextStory();
    } else {
      prevStory();
    }
  }

  isDraggingStory.value = false;
  restartAutoStories();
};

const handleDragLeave = () => {
  if (isDraggingStory.value) {
    endDrag();
  }
  startAutoStories();
};

onMounted(() => {
  startAutoStories();
});

onBeforeUnmount(() => {
  stopAutoStories();
});
</script>
