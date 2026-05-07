<template>
  <v-container class="py-8" style="max-width: 820px">
    <v-progress-linear v-if="loading" indeterminate color="primary" rounded class="mb-6" />

    <div v-if="post">
      <!-- Breadcrumb -->
      <v-breadcrumbs :items="breadcrumbs" density="compact" class="pa-0 mb-4" />

      <!-- Category + date -->
      <div class="d-flex align-center flex-wrap ga-2 mb-4">
        <v-chip
          v-if="post.category"
          :color="post.category.krasa || 'primary'"
          label
          size="small"
        >
          {{ post.category.nosaukums }}
        </v-chip>
        <span class="text-caption text-medium-emphasis d-flex align-center ga-1">
          <v-icon size="13">mdi-calendar-outline</v-icon>
          {{ formatDate(post.publicets_datums) }}
        </span>
        <span class="text-caption text-medium-emphasis d-flex align-center ga-1">
          <v-icon size="13">mdi-eye-outline</v-icon>
          {{ post.skatijumi }} skatījumi
        </span>
      </div>

      <!-- Title -->
      <h1 class="text-h4 font-weight-black mb-5">{{ post.virsraksts }}</h1>

      <!-- Author -->
      <div class="d-flex align-center mb-6">
        <v-avatar :color="avatarColor(post.user?.name)" size="42" class="mr-3">
          <span class="font-weight-bold text-white">{{ initials(post.user?.name) }}</span>
        </v-avatar>
        <div>
          <p class="font-weight-bold text-body-1">{{ post.user?.name }}</p>
          <p class="text-caption text-medium-emphasis">Autors</p>
        </div>
        <v-spacer />
        <!-- Edit button for post owner -->
        <v-btn
          v-if="canEdit"
          variant="outlined"
          size="small"
          :to="`/dashboard/raksti/${post.id}/rediget`"
          prepend-icon="mdi-pencil"
        >
          Rediģēt
        </v-btn>
      </div>

      <!-- Cover image -->
      <v-img
        :src="post.attels_url || `https://picsum.photos/seed/${post.id}/900/400`"
        height="360"
        cover
        rounded="xl"
        class="mb-8"
      />

      <!-- Tags -->
      <div class="d-flex flex-wrap ga-2 mb-6">
        <v-chip
          v-for="tag in post.tags"
          :key="tag.id"
          size="small"
          variant="outlined"
          prepend-icon="mdi-tag-outline"
        >
          {{ tag.nosaukums }}
        </v-chip>
      </div>

      <!-- Content -->
      <div class="post-content text-body-1 mb-12" v-html="post.saturs" />

      <v-divider class="mb-8" />

      <!-- Comments -->
      <section>
        <h2 class="text-h6 font-weight-bold mb-6">
          <v-icon start>mdi-comment-multiple-outline</v-icon>
          Komentāri ({{ approvedComments.length }})
        </h2>

        <div v-for="comment in approvedComments" :key="comment.id" class="mb-4">
          <div class="d-flex ga-3">
            <v-avatar :color="avatarColor(comment.user?.name)" size="36">
              <span class="text-caption font-weight-bold text-white">
                {{ initials(comment.user?.name) }}
              </span>
            </v-avatar>
            <div class="flex-grow-1">
              <div class="d-flex align-center ga-2 mb-1">
                <span class="font-weight-medium text-body-2">{{ comment.user?.name }}</span>
                <span class="text-caption text-medium-emphasis">{{ formatDate(comment.created_at) }}</span>
              </div>
              <v-card variant="tonal" rounded="lg" class="pa-3">
                <p class="text-body-2">{{ comment.saturs }}</p>
              </v-card>
            </div>
          </div>
        </div>

        <!-- Add comment -->
        <div v-if="isLoggedIn()" class="mt-6">
          <h3 class="text-subtitle-1 font-weight-bold mb-3">Pievienot komentāru</h3>
          <v-alert type="info" variant="tonal" rounded="lg" class="mb-3" density="compact">
            Komentāri tiek publicēti pēc moderatora apstiprināšanas.
          </v-alert>
          <v-form ref="commentForm" @submit.prevent="submitComment">
            <v-textarea
              v-model="newComment"
              label="Tavs komentārs"
              variant="outlined"
              rows="3"
              counter="1000"
              :rules="[r => !!r || 'Obligāts lauks', r => r.length >= 5 || 'Vismaz 5 rakstzīmes']"
              class="mb-3"
            />
            <v-btn type="submit" color="primary" :loading="commentLoading" prepend-icon="mdi-send">
              Nosūtīt komentāru
            </v-btn>
          </v-form>
        </div>
        <v-alert v-else variant="tonal" color="primary" rounded="lg" class="mt-6">
          <router-link to="/login" class="text-primary font-weight-medium">Pieslēdzies</router-link>
          , lai atstātu komentāru.
        </v-alert>
      </section>
    </div>

    <div v-else-if="!loading" class="text-center py-16">
      <v-icon size="64" color="medium-emphasis" class="mb-4">mdi-file-alert-outline</v-icon>
      <p class="text-h6 text-medium-emphasis">Raksts nav atrasts</p>
      <v-btn to="/raksti" variant="text" class="mt-3">Atpakaļ uz rakstiem</v-btn>
    </div>
  </v-container>
</template>

<script lang="ts" setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useApi } from '@/composables/useApi'
import { useAuth } from '@/composables/useAuth'

const route = useRoute()
const { loading, get, post } = useApi()
const { user, isLoggedIn } = useAuth()

const postData = ref<any>(null)
const post_ = computed(() => postData.value)
const post = post_

const newComment = ref('')
const commentForm = ref()
const commentLoading = ref(false)

const approvedComments = computed(
  () => postData.value?.komentari?.filter((c: any) => c.apstiprints) ?? []
)

const canEdit = computed(() =>
  user.user && postData.value && (user.user.id === postData.value.user?.id || user.user.role === 'administrators')
)

const breadcrumbs = computed(() => [
  { title: 'Sākums', to: '/' },
  { title: 'Raksti', to: '/raksti' },
  { title: postData.value?.virsraksts ?? '…', disabled: true },
])

const COLORS = ['#1565C0', '#2E7D32', '#AD1457', '#E65100', '#4527A0']

function initials(name?: string) {
  return (name ?? '?').split(' ').map((n) => n[0]).join('').toUpperCase().slice(0, 2)
}
function avatarColor(name?: string) {
  return COLORS[((name ?? '').charCodeAt(0) || 0) % COLORS.length]
}
function formatDate(d?: string) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('lv-LV', { year: 'numeric', month: 'long', day: 'numeric' })
}

async function fetchPost() {
  const data = await get<any>(`/posts/${route.params.slug}`)
  if (data) postData.value = data
}

async function submitComment() {
  const { valid } = await commentForm.value.validate()
  if (!valid) return
  commentLoading.value = true
  await post(`/posts/${postData.value.id}/comments`, { saturs: newComment.value })
  newComment.value = ''
  commentLoading.value = false
}

onMounted(fetchPost)
</script>

<style scoped>
.post-content :deep(p) { margin-bottom: 1.2em; line-height: 1.8; }
.post-content :deep(h2) { font-size: 1.4rem; font-weight: 700; margin: 1.5em 0 0.5em; }
.post-content :deep(h3) { font-size: 1.2rem; font-weight: 600; margin: 1.2em 0 0.4em; }
.post-content :deep(ul), .post-content :deep(ol) { padding-left: 1.5em; margin-bottom: 1em; }
</style>
