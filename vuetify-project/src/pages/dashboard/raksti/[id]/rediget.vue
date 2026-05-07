<template>
  <v-container class="py-8" style="max-width: 860px">
    <v-btn variant="text" to="/dashboard" prepend-icon="mdi-arrow-left" class="mb-4">
      Atpakaļ
    </v-btn>
    <h1 class="text-h4 font-weight-black mb-2">Rediģēt rakstu</h1>
    <p class="text-body-2 text-medium-emphasis mb-8">Veic izmaiņas savā rakstā.</p>

    <v-progress-linear v-if="fetchLoading" indeterminate color="primary" rounded class="mb-6" />

    <v-alert v-if="error" type="error" variant="tonal" rounded="xl" class="mb-6">
      {{ error }}
    </v-alert>

    <v-card v-if="post" rounded="xl" class="pa-6">
      <PostForm
        :initial="post"
        submit-label="Saglabāt izmaiņas"
        :loading="saveLoading"
        @submit="handleUpdate"
        @cancel="router.push('/dashboard')"
      />
    </v-card>
  </v-container>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useApi } from '@/composables/useApi'
import PostForm from '@/components/PostForm.vue'

const route = useRoute()
const router = useRouter()
const { get, put, loading: fetchLoading, error } = useApi()
const saveLoading = ref(false)

const post = ref<any>(null)

async function fetchPost() {
  const data = await get<any>(`/dashboard/posts`)
  if (data) {
    const all = data.data ?? data
    post.value = all.find((p: any) => String(p.id) === String(route.params.id))
  }
}

async function handleUpdate(form: Record<string, any>) {
  saveLoading.value = true
  const data = await put<any>(`/dashboard/posts/${route.params.id}`, form)
  saveLoading.value = false
  if (data) {
    router.push(`/raksti/${data.slug}`)
  }
}

onMounted(fetchPost)
</script>
