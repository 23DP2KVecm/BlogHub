<template>
  <v-container class="py-8">
    <div class="d-flex align-center ga-3 mb-8">
      <v-icon icon="mdi-shield-crown" color="error" size="36" />
      <div>
        <h1 class="text-h4 font-weight-black">Admin panelis</h1>
        <p class="text-body-2 text-medium-emphasis">Sistēmas pārvaldība</p>
      </div>
    </div>

    <v-tabs v-model="tab" color="primary" class="mb-6">
      <v-tab value="users">
        <v-icon start>mdi-account-group</v-icon>
        Lietotāji
      </v-tab>
      <v-tab value="posts">
        <v-icon start>mdi-newspaper</v-icon>
        Raksti
      </v-tab>
      <v-tab value="comments">
        <v-icon start>mdi-comment-multiple</v-icon>
        Komentāri
      </v-tab>
    </v-tabs>

    <v-window v-model="tab">
      <!-- ── USERS ────────────────────────────────────────── -->
      <v-window-item value="users">
        <v-card rounded="xl">
          <v-card-title class="pa-5 pb-3">Visi lietotāji</v-card-title>
          <v-progress-linear v-if="loadingUsers" indeterminate color="primary" />
          <v-data-table :headers="userHeaders" :items="users" :items-per-page="10">
            <template #item.role="{ item }">
              <v-select
                :model-value="item.role?.id"
                :items="roles"
                item-title="nosaukums"
                item-value="id"
                variant="outlined"
                density="compact"
                hide-details
                style="min-width: 150px"
                @update:model-value="updateRole(item.id, $event)"
              />
            </template>
            <template #item.created_at="{ item }">
              {{ fmtDate(item.created_at) }}
            </template>
          </v-data-table>
        </v-card>
      </v-window-item>

      <!-- ── POSTS ────────────────────────────────────────── -->
      <v-window-item value="posts">
        <v-card rounded="xl">
          <v-card-title class="pa-5 pb-3">Visi raksti</v-card-title>
          <v-progress-linear v-if="loadingPosts" indeterminate color="primary" />
          <v-data-table :headers="postHeaders" :items="adminPosts" :items-per-page="10">
            <template #item.virsraksts="{ item }">
              <router-link :to="'/raksti/' + item.slug" class="text-decoration-none text-primary font-weight-medium">
                {{ item.virsraksts }}
              </router-link>
            </template>
            <template #item.statuss="{ item }">
              <v-chip :color="item.statuss === 'publicets' ? 'success' : 'warning'" size="small" label>
                {{ item.statuss }}
              </v-chip>
            </template>
            <template #item.actions="{ item }">
              <v-btn icon="mdi-delete" size="small" variant="text" color="error"
                @click="deletePost(item.id)" />
            </template>
          </v-data-table>
        </v-card>
      </v-window-item>

      <!-- ── COMMENTS ─────────────────────────────────────── -->
      <v-window-item value="comments">
        <v-card rounded="xl">
          <v-card-title class="pa-5 pb-3">Komentāri</v-card-title>
          <v-progress-linear v-if="loadingComments" indeterminate color="primary" />
          <v-data-table :headers="commentHeaders" :items="comments" :items-per-page="10">
            <template #item.saturs="{ item }">
              <span class="text-body-2">{{ item.saturs.slice(0, 80) }}{{ item.saturs.length > 80 ? '…' : '' }}</span>
            </template>
            <template #item.apstiprints="{ item }">
              <v-chip
                :color="item.apstiprints ? 'success' : 'warning'"
                size="small"
                label
                @click="toggleApprove(item)"
                style="cursor: pointer"
              >
                {{ item.apstiprints ? 'Apstiprināts' : 'Gaida' }}
              </v-chip>
            </template>
            <template #item.actions="{ item }">
              <v-btn icon="mdi-delete" size="small" variant="text" color="error"
                @click="deleteComment(item.id)" />
            </template>
          </v-data-table>
        </v-card>
      </v-window-item>
    </v-window>
  </v-container>
</template>

<script lang="ts" setup>
import { ref, onMounted, watch } from 'vue'
import { useApi } from '@/composables/useApi'

const { get, put, patch, del } = useApi()

const tab = ref('users')
const users = ref<any[]>([])
const roles = ref<any[]>([])
const adminPosts = ref<any[]>([])
const comments = ref<any[]>([])
const loadingUsers = ref(false)
const loadingPosts = ref(false)
const loadingComments = ref(false)

const userHeaders = [
  { title: 'Vārds', key: 'name', sortable: true },
  { title: 'E-pasts', key: 'email', sortable: true },
  { title: 'Loma', key: 'role', sortable: false },
  { title: 'Reģistrēts', key: 'created_at', sortable: true },
]
const postHeaders = [
  { title: 'Virsraksts', key: 'virsraksts', sortable: true },
  { title: 'Autors', key: 'user.name', sortable: false },
  { title: 'Statuss', key: 'statuss', sortable: true },
  { title: 'Skatījumi', key: 'skatijumi', sortable: true },
  { title: '', key: 'actions', sortable: false, align: 'end' as const },
]
const commentHeaders = [
  { title: 'Komentārs', key: 'saturs', sortable: false },
  { title: 'Autors', key: 'user.name', sortable: false },
  { title: 'Statuss', key: 'apstiprints', sortable: true },
  { title: '', key: 'actions', sortable: false, align: 'end' as const },
]

function fmtDate(d: string) {
  return d ? new Date(d).toLocaleDateString('lv-LV') : ''
}

async function loadUsers() {
  loadingUsers.value = true
  const [u, r] = await Promise.all([get<any[]>('/admin/users'), get<any[]>('/admin/roles')])
  if (u) users.value = u
  if (r) roles.value = r
  loadingUsers.value = false
}

async function loadPosts() {
  loadingPosts.value = true
  const data = await get<any>('/admin/posts')
  if (data) adminPosts.value = data.data ?? data
  loadingPosts.value = false
}

async function loadComments() {
  loadingComments.value = true
  const data = await get<any>('/admin/comments')
  if (data) comments.value = data.data ?? data
  loadingComments.value = false
}

async function updateRole(userId: number, roleId: number) {
  await put(`/admin/users/${userId}/role`, { role_id: roleId })
  await loadUsers()
}

async function deletePost(id: number) {
  if (confirm('Dzēst šo rakstu?')) {
    await del(`/admin/posts/${id}`)
    adminPosts.value = adminPosts.value.filter((p) => p.id !== id)
  }
}

async function toggleApprove(comment: any) {
  const data = await patch(`/admin/comments/${comment.id}/approve`, {})
  if (data) {
    const idx = comments.value.findIndex((c) => c.id === comment.id)
    if (idx !== -1) comments.value[idx] = data
  }
}

async function deleteComment(id: number) {
  if (confirm('Dzēst šo komentāru?')) {
    await del(`/admin/comments/${id}`)
    comments.value = comments.value.filter((c) => c.id !== id)
  }
}

watch(tab, (t) => {
  if (t === 'posts' && !adminPosts.value.length) loadPosts()
  if (t === 'comments' && !comments.value.length) loadComments()
})

onMounted(loadUsers)
</script>
