<template>
  <!-- ─── Hero ─────────────────────────────────────────────── -->
  <section class="hero-section">
    <v-container class="py-16">
      <v-row justify="center" align="center">
        <v-col cols="12" md="8" lg="6" class="text-center">
          <v-chip color="secondary" variant="tonal" label class="mb-5">
            <v-icon start size="16">mdi-star-four-points</v-icon>
            Latvijas digitālā emuāru platforma
          </v-chip>

          <h1 class="hero-title font-weight-black mb-5">
            Tavs <span class="text-primary">Digitālais</span><br />
            Dienasgrāmatas
          </h1>

          <p class="text-body-1 text-medium-emphasis mb-8 mx-auto hero-subtitle">
            Raksti, publicē un atklāj izcilus emuārus. Pievienojies mūsu kopienai
            un dalies savām domām ar visu pasauli.
          </p>

          <div class="d-flex justify-center ga-3 flex-wrap">
            <v-btn color="primary" size="large" rounded="pill" to="/register" elevation="2">
              <v-icon start>mdi-rocket-launch</v-icon>
              Sākt rakstīt
            </v-btn>
            <v-btn variant="outlined" color="primary" size="large" rounded="pill">
              <v-icon start>mdi-newspaper-variant-outline</v-icon>
              Lasīt rakstus
            </v-btn>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </section>

  <!-- ─── Stats ─────────────────────────────────────────────── -->
  <v-container class="py-8">
    <v-row>
      <v-col v-for="stat in stats" :key="stat.label" cols="6" md="3">
        <v-card variant="tonal" color="primary" rounded="xl" class="text-center pa-5">
          <v-icon :icon="stat.icon" size="36" color="primary" class="mb-2" />
          <p class="text-h5 font-weight-black mb-1">{{ stat.value }}</p>
          <p class="text-caption text-medium-emphasis">{{ stat.label }}</p>
        </v-card>
      </v-col>
    </v-row>
  </v-container>

  <!-- ─── Search & Filters ──────────────────────────────────── -->
  <v-container class="pb-2">
    <v-row justify="center">
      <v-col cols="12" md="7">
        <v-text-field
          v-model="search"
          prepend-inner-icon="mdi-magnify"
          placeholder="Meklēt rakstus pēc nosaukuma vai apraksta…"
          variant="outlined"
          rounded="xl"
          hide-details
          clearable
          @update:model-value="debouncedSearch"
          @click:clear="clearSearch"
        />
      </v-col>
    </v-row>

    <v-row justify="center" class="mt-4">
      <v-col cols="12" class="d-flex justify-center flex-wrap ga-2">
        <v-chip
          :variant="selectedCategory === null ? 'flat' : 'outlined'"
          color="primary"
          label
          @click="selectCategory(null)"
        >
          <v-icon start size="14">mdi-view-grid</v-icon>
          Visi
        </v-chip>
        <v-chip
          v-for="cat in categories"
          :key="cat.id"
          :variant="selectedCategory === cat.id ? 'flat' : 'outlined'"
          :color="cat.krasa || 'primary'"
          label
          @click="selectCategory(cat.id)"
        >
          {{ cat.nosaukums }}
          <v-chip size="x-small" class="ml-2" variant="tonal" v-if="cat.raksti_count">
            {{ cat.raksti_count }}
          </v-chip>
        </v-chip>
      </v-col>
    </v-row>
  </v-container>

  <!-- ─── Posts Grid ────────────────────────────────────────── -->
  <v-container class="py-6">
    <div class="d-flex align-center justify-space-between mb-6 flex-wrap ga-3">
      <h2 class="text-h5 font-weight-bold">
        {{ selectedCategory ? 'Filtrētie raksti' : search ? 'Meklēšanas rezultāti' : 'Jaunākie raksti' }}
      </h2>
      <v-select
        v-model="sortBy"
        :items="sortOptions"
        item-title="label"
        item-value="value"
        variant="outlined"
        density="compact"
        hide-details
        rounded="lg"
        style="max-width: 190px"
        @update:model-value="fetchPosts"
      />
    </div>

    <v-progress-linear v-if="loading" indeterminate color="primary" rounded class="mb-6" />

    <v-row v-if="posts.length">
      <v-col v-for="post in posts" :key="post.id" cols="12" sm="6" lg="4">
        <PostCard :post="post" />
      </v-col>
    </v-row>

    <div v-else-if="!loading" class="text-center py-16">
      <v-icon size="72" color="medium-emphasis" class="mb-4">mdi-file-search-outline</v-icon>
      <p class="text-h6 text-medium-emphasis mb-2">Nav atrasts neviens raksts</p>
      <p class="text-body-2 text-disabled">Mēģini mainīt meklēšanas kritērijus</p>
    </div>

    <div class="d-flex justify-center mt-10" v-if="totalPages > 1">
      <v-pagination
        v-model="page"
        :length="totalPages"
        rounded="circle"
        @update:model-value="fetchPosts"
      />
    </div>
  </v-container>

  <!-- ─── Newsletter ────────────────────────────────────────── -->
  <v-container class="py-10">
    <v-card
      color="primary"
      rounded="xl"
      class="pa-8 pa-md-12 newsletter-card"
      elevation="0"
    >
      <v-row align="center" justify="center">
        <v-col cols="12" md="6" class="text-center text-md-start mb-6 mb-md-0">
          <v-icon icon="mdi-email-fast-outline" size="48" color="white" class="mb-3 d-block" />
          <h2 class="text-h5 font-weight-bold text-white mb-2">
            Saņem jaunākos rakstus
          </h2>
          <p class="text-body-2 newsletter-subtitle">
            Abonē un nekad nepalaid garām interesantu saturu — sūtām ne biežāk kā reizi nedēļā.
          </p>
        </v-col>
        <v-col cols="12" md="5">
          <v-text-field
            v-model="newsletterEmail"
            placeholder="tavs@epasts.lv"
            variant="solo"
            hide-details
            density="comfortable"
            rounded="pill"
            class="mb-3"
            prepend-inner-icon="mdi-email-outline"
          />
          <v-btn
            color="secondary"
            size="large"
            rounded="pill"
            block
            :disabled="!newsletterEmail"
            @click="subscribeNewsletter"
          >
            <v-icon start>mdi-send</v-icon>
            Abonēt bezmaksas
          </v-btn>
        </v-col>
      </v-row>
    </v-card>
  </v-container>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import PostCard from '@/components/PostCard.vue'

const { loading, get } = useApi()

const search = ref('')
const selectedCategory = ref<number | null>(null)
const sortBy = ref('jaunakie')
const page = ref(1)
const totalPages = ref(1)
const posts = ref<any[]>([])
const categories = ref<any[]>([])
const newsletterEmail = ref('')

const sortOptions = [
  { label: 'Jaunākie', value: 'jaunakie' },
  { label: 'Vecākie', value: 'vecakie' },
  { label: 'Populārākie', value: 'popularakie' },
]

const stats = ref([
  { icon: 'mdi-newspaper-variant', value: '—', label: 'Publicēti raksti' },
  { icon: 'mdi-account-group', value: '—', label: 'Reģistrēti autori' },
  { icon: 'mdi-comment-multiple', value: '—', label: 'Komentāri' },
  { icon: 'mdi-eye', value: '—', label: 'Kopējie skatījumi' },
])

// ── Demo data shown when the API is unreachable ──────────────
const demoPosts = [
  {
    id: 1,
    slug: 'ka-izveidot-emuaru',
    virsraksts: 'Kā izveidot personīgu emuāru 2025. gadā',
    ievads:
      'Šajā rakstā aplūkojam labākās platformas un padomus kā sākt rakstīt emuāru Latvijā — no idejas līdz pirmajai publikācijai.',
    publicets_datums: '2025-03-15T10:00:00',
    skatijumi: 342,
    user: { name: 'Anna Kalniņa' },
    category: { nosaukums: 'Tehnoloģijas', krasa: '#1565C0' },
    tags: [
      { id: 1, nosaukums: 'Laravel' },
      { id: 2, nosaukums: 'Vue.js' },
    ],
  },
  {
    id: 2,
    slug: 'rakstisanas-paradumi',
    virsraksts: 'Rakstīšanas ieradumu veidošana',
    ievads:
      'Kā veidot konsekventus rakstīšanas paradumus un uzlabot sava satura kvalitāti. Praktiski padomi no pieredzējušiem bloggeriem.',
    publicets_datums: '2025-03-10T09:00:00',
    skatijumi: 217,
    user: { name: 'Māris Bērziņš' },
    category: { nosaukums: 'Personīgā izaugsme', krasa: '#2E7D32' },
    tags: [{ id: 3, nosaukums: 'Motivācija' }],
  },
  {
    id: 3,
    slug: 'autentiskums-saturaa',
    virsraksts: 'Autentiskuma nozīme saturā',
    ievads:
      'Kāpēc autentisks saturs uzvar pār perfektu saturu — lasītāju viedokļi, reāla pieredze un atklāsmju kopīgošana.',
    publicets_datums: '2025-03-05T14:30:00',
    skatijumi: 189,
    user: { name: 'Zane Liepiņa' },
    category: { nosaukums: 'Mārketings', krasa: '#AD1457' },
    tags: [
      { id: 4, nosaukums: 'Saturs' },
      { id: 5, nosaukums: 'Mārketings' },
    ],
  },
  {
    id: 4,
    slug: 'latvija-digitala-transformacija',
    virsraksts: 'Latvijas digitālā transformācija',
    ievads:
      'Kā Latvija kļūst par vienu no digitāli attīstītākajām valstīm Eiropā — e-pārvalde, jaunuzņēmumi un tehnoloģiju ekosistēma.',
    publicets_datums: '2025-02-28T11:00:00',
    skatijumi: 445,
    user: { name: 'Jānis Ozoliņš' },
    category: { nosaukums: 'Tehnoloģijas', krasa: '#1565C0' },
    tags: [{ id: 6, nosaukums: 'Digitalizācija' }],
  },
  {
    id: 5,
    slug: 'celosana-latvija',
    virsraksts: 'Labākās vietas ceļošanai Latvijā',
    ievads:
      'No Siguldas pilsdrupām līdz Ventspils pludmalēm — iepazīsti Latvijas skaistumu četros gadalaikos.',
    publicets_datums: '2025-02-20T16:00:00',
    skatijumi: 612,
    user: { name: 'Inga Saulīte' },
    category: { nosaukums: 'Ceļojumi', krasa: '#E65100' },
    tags: [
      { id: 7, nosaukums: 'Latvija' },
      { id: 8, nosaukums: 'Ceļojumi' },
    ],
  },
  {
    id: 6,
    slug: 'vesela-uztursanas',
    virsraksts: 'Veselīga uzturēšanās ikdienā',
    ievads:
      'Vienkārši padomi kā uzturēt veselīgu dzīvesveidu pat visaizņemtākajās dienās — uzturs, kustība un miegs.',
    publicets_datums: '2025-02-15T08:30:00',
    skatijumi: 298,
    user: { name: 'Kristīne Vītoliņa' },
    category: { nosaukums: 'Veselība', krasa: '#1B5E20' },
    tags: [
      { id: 9, nosaukums: 'Veselība' },
      { id: 10, nosaukums: 'Sports' },
    ],
  },
]

const demoCategories = [
  { id: 1, nosaukums: 'Tehnoloģijas', krasa: '#1565C0', raksti_count: 2 },
  { id: 2, nosaukums: 'Personīgā izaugsme', krasa: '#2E7D32', raksti_count: 1 },
  { id: 3, nosaukums: 'Mārketings', krasa: '#AD1457', raksti_count: 1 },
  { id: 4, nosaukums: 'Ceļojumi', krasa: '#E65100', raksti_count: 1 },
  { id: 5, nosaukums: 'Veselība', krasa: '#1B5E20', raksti_count: 1 },
]

const demoStats = [
  { icon: 'mdi-newspaper-variant', value: '48', label: 'Publicēti raksti' },
  { icon: 'mdi-account-group', value: '12', label: 'Reģistrēti autori' },
  { icon: 'mdi-comment-multiple', value: '196', label: 'Komentāri' },
  { icon: 'mdi-eye', value: '8 400', label: 'Kopējie skatījumi' },
]

// ── Data fetching ─────────────────────────────────────────────
async function fetchPosts() {
  const params: Record<string, string | number> = {
    lapa: page.value,
    kartojums: sortBy.value,
  }
  if (search.value) params.meklet = search.value
  if (selectedCategory.value) params.kategorija = selectedCategory.value

  const data = await get<any>('/posts', params)

  if (data) {
    posts.value = data.data ?? data
    totalPages.value = data.last_page ?? 1
  } else {
    let filtered = [...demoPosts]
    if (selectedCategory.value) {
      const catName = demoCategories.find((c) => c.id === selectedCategory.value)?.nosaukums
      filtered = filtered.filter((p) => p.category?.nosaukums === catName)
    }
    if (search.value) {
      const q = search.value.toLowerCase()
      filtered = filtered.filter(
        (p) =>
          p.virsraksts.toLowerCase().includes(q) || p.ievads.toLowerCase().includes(q),
      )
    }
    if (sortBy.value === 'popularakie') {
      filtered.sort((a, b) => b.skatijumi - a.skatijumi)
    } else if (sortBy.value === 'vecakie') {
      filtered.sort(
        (a, b) =>
          new Date(a.publicets_datums).getTime() - new Date(b.publicets_datums).getTime(),
      )
    }
    posts.value = filtered
    totalPages.value = 1
  }
}

async function fetchCategories() {
  const data = await get<any[]>('/categories')
  categories.value = data ?? demoCategories
}

async function fetchStats() {
  const data = await get<any>('/stats')
  if (data) {
    stats.value = [
      { icon: 'mdi-newspaper-variant', value: String(data.raksti ?? 0), label: 'Publicēti raksti' },
      { icon: 'mdi-account-group', value: String(data.autori ?? 0), label: 'Reģistrēti autori' },
      {
        icon: 'mdi-comment-multiple',
        value: String(data.komentari ?? 0),
        label: 'Komentāri',
      },
      {
        icon: 'mdi-eye',
        value:
          data.skatijumi >= 1000
            ? `${Math.floor(data.skatijumi / 1000)} k`
            : String(data.skatijumi ?? 0),
        label: 'Kopējie skatījumi',
      },
    ]
  } else {
    stats.value = demoStats
  }
}

// ── Interaction helpers ───────────────────────────────────────
let searchTimer: ReturnType<typeof setTimeout>

function debouncedSearch() {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    page.value = 1
    fetchPosts()
  }, 380)
}

function clearSearch() {
  search.value = ''
  page.value = 1
  fetchPosts()
}

function selectCategory(id: number | null) {
  selectedCategory.value = id
  page.value = 1
  fetchPosts()
}

function subscribeNewsletter() {
  newsletterEmail.value = ''
}

onMounted(() => {
  fetchPosts()
  fetchCategories()
  fetchStats()
})
</script>

<style scoped>
.hero-section {
  background: linear-gradient(
    135deg,
    rgba(var(--v-theme-primary), 0.07) 0%,
    rgba(var(--v-theme-secondary), 0.04) 100%
  );
  border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.hero-title {
  font-size: clamp(2rem, 5vw, 3.5rem);
  line-height: 1.15;
}

.hero-subtitle {
  max-width: 520px;
}

.newsletter-card {
  background: linear-gradient(135deg, rgb(var(--v-theme-primary)) 0%, #1976d2 100%) !important;
}

.newsletter-subtitle {
  color: rgba(255, 255, 255, 0.8);
}
</style>
