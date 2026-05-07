<template>
  <v-container class="py-8">
    <h1 class="text-h4 font-weight-black mb-2">Kategorijas</h1>
    <p class="text-body-2 text-medium-emphasis mb-8">
      Atklāj rakstus pēc tematiskajām kategorijām.
    </p>

    <v-progress-linear v-if="loading" indeterminate color="primary" rounded class="mb-6" />

    <v-row>
      <v-col
        v-for="cat in categories"
        :key="cat.id"
        cols="12"
        sm="6"
        md="4"
      >
        <v-card
          rounded="xl"
          hover
          @click="selectCategory(cat)"
          class="pa-6 text-center category-card"
          :style="{ borderTop: `4px solid ${cat.krasa || '#1976D2'}` }"
        >
          <v-avatar :color="cat.krasa || 'primary'" size="56" class="mb-4">
            <v-icon color="white" size="28">{{ catIcon(cat.nosaukums) }}</v-icon>
          </v-avatar>
          <h3 class="text-h6 font-weight-bold mb-1">{{ cat.nosaukums }}</h3>
          <p class="text-body-2 text-medium-emphasis mb-3">{{ cat.apraksts || 'Raksti šajā kategorijā' }}</p>
          <v-chip :color="cat.krasa || 'primary'" size="small" label variant="tonal">
            {{ cat.raksti_count ?? 0 }} raksti
          </v-chip>
        </v-card>
      </v-col>
    </v-row>

    <!-- Posts for selected category -->
    <v-dialog v-model="dialog" max-width="900">
      <v-card rounded="xl">
        <v-card-title class="pa-6 pb-3 d-flex align-center justify-space-between">
          <div class="d-flex align-center ga-3">
            <v-avatar :color="selected?.krasa || 'primary'" size="36">
              <v-icon color="white" size="18">{{ catIcon(selected?.nosaukums) }}</v-icon>
            </v-avatar>
            <span>{{ selected?.nosaukums }}</span>
          </div>
          <v-btn icon="mdi-close" variant="text" @click="dialog = false" />
        </v-card-title>

        <v-card-text class="px-6 pb-6">
          <v-progress-linear v-if="postsLoading" indeterminate color="primary" rounded class="mb-4" />
          <v-row v-if="catPosts.length">
            <v-col v-for="post in catPosts" :key="post.id" cols="12" sm="6">
              <PostCard :post="post" />
            </v-col>
          </v-row>
          <p v-else-if="!postsLoading" class="text-center text-medium-emphasis py-8">
            Šajā kategorijā pagaidām nav rakstu.
          </p>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import PostCard from '@/components/PostCard.vue'

const { loading, get } = useApi()
const postsLoading = ref(false)
const categories = ref<any[]>([])
const catPosts = ref<any[]>([])
const selected = ref<any>(null)
const dialog = ref(false)

const ICONS: Record<string, string> = {
  tehnoloģijas: 'mdi-laptop',
  'personīgā izaugsme': 'mdi-sprout',
  mārketings: 'mdi-bullhorn',
  ceļojumi: 'mdi-airplane',
  veselība: 'mdi-heart-pulse',
}

function catIcon(name?: string) {
  if (!name) return 'mdi-tag'
  return ICONS[name.toLowerCase()] ?? 'mdi-tag'
}

async function selectCategory(cat: any) {
  selected.value = cat
  dialog.value = true
  postsLoading.value = true
  const data = await get<any>('/posts', { kategorija: cat.id, kartojums: 'jaunakie' })
  catPosts.value = data?.data ?? data ?? []
  postsLoading.value = false
}

onMounted(async () => {
  const data = await get<any[]>('/categories')
  if (data) categories.value = data
})
</script>

<style scoped>
.category-card { cursor: pointer; transition: transform 0.2s; }
.category-card:hover { transform: translateY(-4px); }
</style>
