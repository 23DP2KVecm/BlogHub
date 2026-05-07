<template>
  <v-form ref="formRef" @submit.prevent="emit('submit', form)">
    <v-row>
      <v-col cols="12">
        <v-text-field
          v-model="form.virsraksts"
          label="Virsraksts *"
          variant="outlined"
          :rules="[r => !!r || 'Obligāts lauks', r => r.length <= 200 || 'Maks. 200 rakstzīmes']"
          counter="200"
        />
      </v-col>

      <v-col cols="12" md="6">
        <v-select
          v-model="form.category_id"
          :items="categories"
          item-title="nosaukums"
          item-value="id"
          label="Kategorija"
          variant="outlined"
          clearable
          prepend-inner-icon="mdi-tag-outline"
        />
      </v-col>

      <v-col cols="12" md="6">
        <v-select
          v-model="form.statuss"
          :items="statusOptions"
          item-title="label"
          item-value="value"
          label="Statuss *"
          variant="outlined"
          :rules="[r => !!r || 'Obligāts lauks']"
          prepend-inner-icon="mdi-list-status"
        />
      </v-col>

      <v-col cols="12">
        <v-textarea
          v-model="form.ievads"
          label="Ievads (īss apraksts)"
          variant="outlined"
          rows="3"
          counter="500"
          hint="Parādās rakstu sarakstā un meklēšanā"
          persistent-hint
          :rules="[r => !r || r.length <= 500 || 'Maks. 500 rakstzīmes']"
        />
      </v-col>

      <v-col cols="12">
        <v-label class="text-body-2 font-weight-medium mb-2 d-block">
          Saturs *
        </v-label>
        <v-textarea
          v-model="form.saturs"
          label="Raksti šeit..."
          variant="outlined"
          rows="14"
          :rules="[r => !!r || 'Obligāts lauks', r => r.length >= 10 || 'Vismaz 10 rakstzīmes']"
          hint="HTML tagi ir atļauti: <p>, <h2>, <h3>, <ul>, <li>, <strong>, <em>"
          persistent-hint
        />
      </v-col>

      <v-col cols="12">
        <v-text-field
          v-model="form.attels_url"
          label="Vāka attēla URL (neobligāts)"
          variant="outlined"
          prepend-inner-icon="mdi-image-outline"
          placeholder="https://example.com/image.jpg"
        />
      </v-col>

      <v-col cols="12">
        <v-select
          v-model="form.birkas"
          :items="tags"
          item-title="nosaukums"
          item-value="id"
          label="Birkas"
          variant="outlined"
          multiple
          chips
          closable-chips
          prepend-inner-icon="mdi-tag-multiple-outline"
        />
      </v-col>

      <v-col cols="12" class="d-flex justify-end ga-3">
        <v-btn variant="text" @click="emit('cancel')">Atcelt</v-btn>
        <v-btn
          type="submit"
          color="primary"
          :loading="loading"
          size="large"
          rounded="lg"
          prepend-icon="mdi-content-save"
        >
          {{ submitLabel }}
        </v-btn>
      </v-col>
    </v-row>
  </v-form>
</template>

<script lang="ts" setup>
import { ref, watch } from 'vue'
import { useApi } from '@/composables/useApi'

const props = defineProps<{
  initial?: Record<string, any>
  submitLabel?: string
  loading?: boolean
}>()

const emit = defineEmits<{
  submit: [form: Record<string, any>]
  cancel: []
}>()

const { get } = useApi()
const formRef = ref()
const categories = ref<any[]>([])
const tags = ref<any[]>([])

const form = ref({
  virsraksts: '',
  ievads: '',
  saturs: '',
  attels_url: '',
  category_id: null as number | null,
  statuss: 'melnraksts' as string,
  birkas: [] as number[],
})

const statusOptions = [
  { label: 'Melnraksts', value: 'melnraksts' },
  { label: 'Publicēt', value: 'publicets' },
]

watch(
  () => props.initial,
  (v) => {
    if (v) {
      form.value = {
        virsraksts: v.virsraksts ?? '',
        ievads: v.ievads ?? '',
        saturs: v.saturs ?? '',
        attels_url: v.attels_url ?? '',
        category_id: v.category_id ?? null,
        statuss: v.statuss ?? 'melnraksts',
        birkas: v.tags?.map((t: any) => t.id) ?? [],
      }
    }
  },
  { immediate: true }
)

async function validate() {
  return formRef.value?.validate()
}

defineExpose({ validate })

async function fetchMeta() {
  const [cats, birkas] = await Promise.all([
    get<any[]>('/categories'),
    get<any[]>('/tags'),
  ])
  if (cats) categories.value = cats
  if (birkas) tags.value = birkas
}

fetchMeta()
</script>
