<template>
  <v-card height="100%" class="d-flex flex-column post-card" :to="'/raksti/' + post.slug" hover>
    <v-img
      :src="post.attels_url || `https://picsum.photos/seed/${post.id}/600/320`"
      height="200"
      cover
      class="flex-grow-0"
    >
      <v-chip
        v-if="post.category"
        class="ma-2"
        :color="post.category.krasa || 'primary'"
        size="small"
        label
      >
        {{ post.category.nosaukums }}
      </v-chip>
    </v-img>

    <v-card-text class="flex-grow-1 pb-2">
      <div class="d-flex align-center mb-2 text-caption text-medium-emphasis ga-3">
        <span class="d-flex align-center ga-1">
          <v-icon size="13">mdi-calendar-outline</v-icon>
          {{ formatDate(post.publicets_datums) }}
        </span>
        <span class="d-flex align-center ga-1">
          <v-icon size="13">mdi-eye-outline</v-icon>
          {{ post.skatijumi ?? 0 }}
        </span>
      </div>

      <h3 class="text-subtitle-1 font-weight-bold mb-2 line-clamp-2">
        {{ post.virsraksts }}
      </h3>

      <p class="text-body-2 text-medium-emphasis line-clamp-3">
        {{ post.ievads }}
      </p>
    </v-card-text>

    <v-card-actions class="px-4 pb-4 pt-0">
      <v-avatar size="30" :color="avatarColor(post.user?.name)" class="mr-2">
        <span class="text-caption font-weight-bold text-white">{{ initials(post.user?.name) }}</span>
      </v-avatar>
      <span class="text-caption font-weight-medium">{{ post.user?.name }}</span>
      <v-spacer />
      <v-chip
        v-for="tag in post.tags?.slice(0, 2)"
        :key="tag.id"
        size="x-small"
        variant="outlined"
        class="ml-1"
      >
        {{ tag.nosaukums }}
      </v-chip>
    </v-card-actions>
  </v-card>
</template>

<script lang="ts" setup>
defineProps<{
  post: {
    id: number
    slug: string
    virsraksts: string
    ievads?: string
    attels_url?: string
    publicets_datums?: string
    skatijumi?: number
    user?: { name: string }
    category?: { nosaukums: string; krasa?: string }
    tags?: { id: number; nosaukums: string }[]
  }
}>()

const AVATAR_COLORS = ['#1565C0', '#2E7D32', '#AD1457', '#E65100', '#4527A0', '#00695C']

function formatDate(dateStr?: string) {
  if (!dateStr) return '—'
  return new Date(dateStr).toLocaleDateString('lv-LV', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

function initials(name?: string): string {
  if (!name) return '?'
  return name
    .split(' ')
    .map((n) => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

function avatarColor(name?: string): string {
  if (!name) return AVATAR_COLORS[0] as string
  const idx = name.charCodeAt(0) % AVATAR_COLORS.length
  return AVATAR_COLORS[idx] as string
}
</script>

<style scoped>
.post-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.post-card:hover {
  transform: translateY(-4px);
}
.line-clamp-2 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}
.line-clamp-3 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
}
</style>
