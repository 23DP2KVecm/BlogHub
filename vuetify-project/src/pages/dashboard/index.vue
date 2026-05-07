<template>
  <v-container class="py-8">
    <div class="d-flex align-center justify-space-between mb-8 flex-wrap ga-3">
      <div>
        <h1 class="text-h4 font-weight-black">Mans konts</h1>
        <p class="text-body-2 text-medium-emphasis mt-1">
          Labdien, <strong>{{ user.user?.name }}</strong>! Šeit vari pārvaldīt savus rakstus.
        </p>
      </div>
      <v-btn color="primary" to="/dashboard/raksti/izveidot" prepend-icon="mdi-plus" size="large" rounded="lg">
        Jauns raksts
      </v-btn>
    </div>

    <!-- Stats row -->
    <v-row class="mb-8">
      <v-col cols="6" md="3" v-for="s in myStats" :key="s.label">
        <v-card variant="tonal" :color="s.color" rounded="xl" class="pa-4 text-center">
          <v-icon :icon="s.icon" size="28" :color="s.color" class="mb-2" />
          <p class="text-h5 font-weight-black">{{ s.value }}</p>
          <p class="text-caption">{{ s.label }}</p>
        </v-card>
      </v-col>
    </v-row>

    <!-- Posts table -->
    <v-card rounded="xl">
      <v-card-title class="pa-5 pb-3 d-flex align-center justify-space-between">
        <span>Mani raksti</span>
        <v-progress-circular v-if="loading" size="20" indeterminate color="primary" />
      </v-card-title>

      <v-data-table
        :headers="headers"
        :items="posts"
        :loading="loading"
        :items-per-page="10"
        rounded="xl"
      >
        <template #item.virsraksts="{ item }">
          <router-link
            :to="'/raksti/' + item.slug"
            class="text-decoration-none font-weight-medium text-primary"
          >
            {{ item.virsraksts }}
          </router-link>
        </template>

        <template #item.statuss="{ item }">
          <v-chip
            :color="item.statuss === 'publicets' ? 'success' : item.statuss === 'melnraksts' ? 'warning' : 'default'"
            size="small"
            label
          >
            {{ statusLabel(item.statuss) }}
          </v-chip>
        </template>

        <template #item.category="{ item }">
          <v-chip
            v-if="item.category"
            :color="item.category.krasa"
            size="small"
            label
          >
            {{ item.category.nosaukums }}
          </v-chip>
          <span v-else class="text-disabled">—</span>
        </template>

        <template #item.skatijumi="{ item }">
          <span class="d-flex align-center ga-1">
            <v-icon size="14">mdi-eye-outline</v-icon>
            {{ item.skatijumi }}
          </span>
        </template>

        <template #item.actions="{ item }">
          <div class="d-flex ga-1">
            <v-btn
              icon="mdi-pencil"
              size="small"
              variant="text"
              color="primary"
              :to="`/dashboard/raksti/${item.id}/rediget`"
            />
            <v-btn
              icon="mdi-delete"
              size="small"
              variant="text"
              color="error"
              @click="confirmDelete(item)"
            />
          </div>
        </template>
      </v-data-table>
    </v-card>

    <!-- Delete confirm dialog -->
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card rounded="xl">
        <v-card-title class="pa-5">Dzēst rakstu?</v-card-title>
        <v-card-text class="px-5 pb-3">
          Vai esi pārliecināts, ka vēlies dzēst rakstu
          <strong>„{{ deleteTarget?.virsraksts }}"</strong>? Šo darbību nevar atcelt.
        </v-card-text>
        <v-card-actions class="pa-5 pt-0">
          <v-spacer />
          <v-btn variant="text" @click="deleteDialog = false">Atcelt</v-btn>
          <v-btn color="error" variant="flat" :loading="deleting" @click="doDelete">Dzēst</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script lang="ts" setup>
import { ref, computed, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import { useAuth } from '@/composables/useAuth'

const { loading, get, del } = useApi()
const { user } = useAuth()

const posts = ref<any[]>([])
const deleteDialog = ref(false)
const deleteTarget = ref<any>(null)
const deleting = ref(false)

const headers = [
  { title: 'Virsraksts', key: 'virsraksts', sortable: true },
  { title: 'Kategorija', key: 'category', sortable: false },
  { title: 'Statuss', key: 'statuss', sortable: true },
  { title: 'Skatījumi', key: 'skatijumi', sortable: true },
  { title: 'Darbības', key: 'actions', sortable: false, align: 'end' as const },
]

const myStats = computed(() => [
  { icon: 'mdi-newspaper', value: posts.value.length, label: 'Kopā raksti', color: 'primary' },
  {
    icon: 'mdi-check-circle',
    value: posts.value.filter((p) => p.statuss === 'publicets').length,
    label: 'Publicēti',
    color: 'success',
  },
  {
    icon: 'mdi-pencil',
    value: posts.value.filter((p) => p.statuss === 'melnraksts').length,
    label: 'Melnraksti',
    color: 'warning',
  },
  {
    icon: 'mdi-eye',
    value: posts.value.reduce((s, p) => s + (p.skatijumi ?? 0), 0),
    label: 'Skatījumi',
    color: 'info',
  },
])

function statusLabel(s: string) {
  return s === 'publicets' ? 'Publicēts' : s === 'melnraksts' ? 'Melnraksts' : 'Arhivēts'
}

function confirmDelete(post: any) {
  deleteTarget.value = post
  deleteDialog.value = true
}

async function doDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  const ok = await del(`/dashboard/posts/${deleteTarget.value.id}`)
  if (ok) {
    posts.value = posts.value.filter((p) => p.id !== deleteTarget.value.id)
    deleteDialog.value = false
  }
  deleting.value = false
}

async function fetchMyPosts() {
  const data = await get<any>('/dashboard/posts')
  if (data) posts.value = data.data ?? data
}

onMounted(fetchMyPosts)
</script>
