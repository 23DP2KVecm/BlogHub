<template>
  <v-container class="py-8" style="max-width: 860px">
    <v-btn variant="text" to="/dashboard" prepend-icon="mdi-arrow-left" class="mb-4">
      Atpakaļ
    </v-btn>
    <h1 class="text-h4 font-weight-black mb-2">Jauns raksts</h1>
    <p class="text-body-2 text-medium-emphasis mb-8">
      Uzraksti un publicē jaunu emuāra rakstu.
    </p>

    <v-alert v-if="error" type="error" variant="tonal" rounded="xl" class="mb-6">
      {{ error }}
    </v-alert>

    <v-card rounded="xl" class="pa-6">
      <PostForm
        submit-label="Publicēt rakstu"
        :loading="loading"
        @submit="handleCreate"
        @cancel="router.push('/dashboard')"
      />
    </v-card>
  </v-container>
</template>

<script lang="ts" setup>
import { useRouter } from 'vue-router'
import { useApi } from '@/composables/useApi'
import PostForm from '@/components/PostForm.vue'

const router = useRouter()
const { loading, error, post } = useApi()

async function handleCreate(form: Record<string, any>) {
  const data = await post<any>('/dashboard/posts', form)
  if (data) {
    router.push(`/raksti/${data.slug}`)
  }
}
</script>
