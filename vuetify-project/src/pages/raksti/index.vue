<template>
  <v-container class="py-8">
    <h1 class="text-h4 font-weight-black mb-2">Visi raksti</h1>
    <p class="text-body-2 text-medium-emphasis mb-6">
      {{ totalCount }} raksti kopā
    </p>

    <!-- ─── Advanced filter panel ────────────────────────────── -->
    <v-card rounded="xl" class="mb-8 pa-5" variant="tonal" color="surface-variant">
      <p class="text-subtitle-2 font-weight-bold mb-4">
        <v-icon start size="16">mdi-filter-variant</v-icon>
        Detalizēta meklēšana
      </p>

      <v-row dense>
        <!-- Text search -->
        <v-col cols="12" md="4">
          <v-text-field
            v-model="filters.meklet"
            label="Meklēt pēc nosaukuma vai apraksta"
            prepend-inner-icon="mdi-magnify"
            variant="outlined"
            density="compact"
            clearable
            hide-details
            @update:model-value="debouncedFetch"
          />
        </v-col>

        <!-- Category checkboxes -->
        <v-col cols="12" md="4">
          <p class="text-caption font-weight-medium mb-1">Kategorijas</p>
          <div class="d-flex flex-wrap ga-2">
            <v-checkbox
              v-for="cat in categories"
              :key="cat.id"
              v-model="filters.kategorijas"
              :value="cat.id"
              :label="cat.nosaukums"
              density="compact"
              hide-details
              @update:model-value="applyFilters"
            />
          </div>
        </v-col>

        <!-- Date range -->
        <v-col cols="12" md="4">
          <v-row dense>
            <v-col cols="6">
              <v-text-field
                v-model="filters.datums_no"
                label="No datuma"
                type="date"
                variant="outlined"
                density="compact"
                hide-details
                @update:model-value="applyFilters"
              />
            </v-col>
            <v-col cols="6">
              <v-text-field
                v-model="filters.datums_lidz"
                label="Līdz datumam"
                type="date"
                variant="outlined"
                density="compact"
                hide-details
                @update:model-value="applyFilters"
              />
            </v-col>
          </v-row>

          <v-row dense class="mt-2">
            <v-col cols="12">
              <v-text-field
                v-model.number="filters.min_skatijumi"
                label="Min. skatījumi"
                type="number"
                min="0"
                variant="outlined"
                density="compact"
                hide-details
                prepend-inner-icon="mdi-eye-outline"
                @update:model-value="debouncedFetch"
              />
            </v-col>
          </v-row>
        </v-col>
      </v-row>

      <div class="d-flex align-center justify-space-between mt-4">
        <v-btn variant="text" size="small" @click="clearFilters" prepend-icon="mdi-filter-remove">
          Dzēst filtrus
        </v-btn>
        <div class="d-flex align-center ga-3">
          <span class="text-caption text-medium-emphasis">Kārtot pēc:</span>
          <v-select
            v-model="filters.kartojums"
            :items="sortOptions"
            item-title="label"
            item-value="value"
            variant="outlined"
            density="compact"
            hide-details
            rounded="lg"
            style="min-width: 170px"
            @update:model-value="applyFilters"
          />
        </div>
      </div>
    </v-card>

    <!-- ─── Results ─────────────────────────────────────────── -->
    <v-progress-linear v-if="loading" indeterminate color="primary" rounded class="mb-6" />

    <v-row v-if="posts.length">
      <v-col v-for="post in posts" :key="post.id" cols="12" sm="6" lg="4">
        <PostCard :post="post" />
      </v-col>
    </v-row>

    <div v-else-if="!loading" class="text-center py-16">
      <v-icon size="72" color="medium-emphasis" class="mb-4">mdi-file-search-outline</v-icon>
      <p class="text-h6 text-medium-emphasis">Nav atrasts neviens raksts</p>
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
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import PostCard from '@/components/PostCard.vue'

const { loading, get } = useApi()

const posts = ref<any[]>([])
const categories = ref<any[]>([])
const page = ref(1)
const totalPages = ref(1)
const totalCount = ref(0)

const filters = ref({
  meklet: '',
  kategorijas: [] as number[],
  datums_no: '',
  datums_lidz: '',
  min_skatijumi: '',
  kartojums: 'jaunakie',
})

const sortOptions = [
  { label: 'Jaunākie', value: 'jaunakie' },
  { label: 'Vecākie', value: 'vecakie' },
  { label: 'Populārākie', value: 'popularakie' },
]

async function fetchPosts() {
  const params: Record<string, string | number> = {
    lapa: page.value,
    kartojums: filters.value.kartojums,
  }
  if (filters.value.meklet) params.meklet = filters.value.meklet
  if (filters.value.kategorijas.length === 1) params.kategorija = filters.value.kategorijas[0]!
  if (filters.value.datums_no) params.datums_no = filters.value.datums_no
  if (filters.value.datums_lidz) params.datums_lidz = filters.value.datums_lidz
  if (filters.value.min_skatijumi) params.min_skatijumi = filters.value.min_skatijumi

  const data = await get<any>('/posts', params)
  if (data) {
    posts.value = data.data ?? data
    totalPages.value = data.last_page ?? 1
    totalCount.value = data.total ?? posts.value.length
  }
}

async function fetchCategories() {
  const data = await get<any[]>('/categories')
  if (data) categories.value = data
}

function applyFilters() {
  page.value = 1
  fetchPosts()
}

function clearFilters() {
  filters.value = { meklet: '', kategorijas: [], datums_no: '', datums_lidz: '', min_skatijumi: '', kartojums: 'jaunakie' }
  page.value = 1
  fetchPosts()
}

let timer: ReturnType<typeof setTimeout>
function debouncedFetch() {
  clearTimeout(timer)
  timer = setTimeout(applyFilters, 380)
}

onMounted(() => {
  fetchPosts()
  fetchCategories()
})
</script>
