<template>
  <v-container class="py-8">
    <h1 class="text-h4 font-weight-black mb-2">Statistika</h1>
    <p class="text-body-2 text-medium-emphasis mb-8">
      Platformas datu analīze — COUNT, SUM, AVG un GROUP BY apkopojumi.
    </p>

    <!-- Summary cards -->
    <v-row class="mb-8">
      <v-col cols="6" md="3" v-for="s in stats?.kopsavilkums ? summaryCards : []" :key="s.label">
        <v-card variant="tonal" :color="s.color" rounded="xl" class="pa-5 text-center">
          <v-icon :icon="s.icon" size="36" :color="s.color" class="mb-2" />
          <p class="text-h5 font-weight-black mb-1">{{ s.value }}</p>
          <p class="text-caption text-medium-emphasis">{{ s.label }}</p>
        </v-card>
      </v-col>
    </v-row>

    <v-row>
      <!-- Bar chart: posts by category -->
      <v-col cols="12" md="6">
        <v-card rounded="xl" class="pa-5">
          <h2 class="text-subtitle-1 font-weight-bold mb-1">
            <v-icon start size="18" color="primary">mdi-chart-bar</v-icon>
            Raksti pa kategorijām
          </h2>
          <p class="text-caption text-medium-emphasis mb-4">COUNT(*) GROUP BY kategorija</p>
          <canvas ref="barCanvas" height="260" />
        </v-card>
      </v-col>

      <!-- Line chart: posts by month -->
      <v-col cols="12" md="6">
        <v-card rounded="xl" class="pa-5">
          <h2 class="text-subtitle-1 font-weight-bold mb-1">
            <v-icon start size="18" color="success">mdi-chart-line</v-icon>
            Raksti šajā nedēļā
          </h2>
          <p class="text-caption text-medium-emphasis mb-4">COUNT(*) GROUP BY diena</p>
          <canvas ref="lineCanvas" height="260" />
        </v-card>
      </v-col>

      <!-- Top 5 popular posts -->
      <v-col cols="12" md="12">
        <v-card rounded="xl" class="pa-5">
          <h2 class="text-subtitle-1 font-weight-bold mb-4">
            <v-icon start size="18" color="warning">mdi-fire</v-icon>
            Populārākie raksti (TOP 5)
          </h2>
          <p class="text-caption text-medium-emphasis mb-4">ORDER BY skatijumi DESC LIMIT 5</p>
          <v-list density="compact">
            <v-list-item
              v-for="(post, i) in stats?.populari ?? []"
              :key="post.id"
              :to="'/raksti/' + post.slug"
              rounded="lg"
            >
              <template #prepend>
                <v-avatar :color="rankColor(i)" size="30" class="mr-3">
                  <span class="text-caption font-weight-black text-white">{{ Number(i) + 1 }}</span>
                </v-avatar>
              </template>
              <v-list-item-title class="text-body-2 font-weight-medium">
                {{ post.virsraksts }}
              </v-list-item-title>
              <template #append>
                <span class="text-caption text-medium-emphasis d-flex align-center ga-1">
                  <v-icon size="13">mdi-eye</v-icon>
                  {{ post.skatijumi }}
                </span>
              </template>
            </v-list-item>
          </v-list>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script lang="ts" setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useApi } from '@/composables/useApi'

const { get } = useApi()

const stats = ref<any>(null)
const barCanvas = ref<HTMLCanvasElement | null>(null)
const lineCanvas = ref<HTMLCanvasElement | null>(null)

const summaryCards = computed(() => {
  const k = stats.value?.kopsavilkums
  if (!k) return []
  return [
    { icon: 'mdi-newspaper-variant', value: k.raksti, label: 'Publicēti raksti', color: 'primary' },
    { icon: 'mdi-account-group', value: k.autori, label: 'Reģistrēti lietotāji', color: 'success' },
    { icon: 'mdi-comment-multiple', value: k.komentari, label: 'Apstiprināti komentāri', color: 'info' },
    {
      icon: 'mdi-eye',
      value: k.skatijumi >= 1000 ? `${(k.skatijumi / 1000).toFixed(1)}k` : k.skatijumi,
      label: 'Kopējie skatījumi',
      color: 'warning',
    },
  ]
})

const RANK_COLORS = ['#FFD700', '#C0C0C0', '#CD7F32', '#1565C0', '#2E7D32']
function rankColor(i: number | string) {
  return RANK_COLORS[+i] ?? '#9E9E9E'
}

async function buildCharts() {
  let Chart: any
  try {
    Chart = (await import('chart.js/auto')).default
  } catch {
    console.warn('chart.js nav instalēts. Palaidi: npm install chart.js')
    return
  }

  const kat = stats.value?.paKategorijam ?? []
  const men = stats.value?.paMenešiem ?? []

  if (barCanvas.value) {
    new Chart(barCanvas.value, {
      type: 'bar',
      data: {
        labels: kat.map((k: any) => k.nosaukums),
        datasets: [{
          label: 'Rakstu skaits',
          data: kat.map((k: any) => k.skaits),
          backgroundColor: kat.map((k: any) => k.krasa ?? '#1565C0'),
          borderRadius: 6,
        }],
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
      },
    })
  }

  if (lineCanvas.value) {
    new Chart(lineCanvas.value, {
      type: 'line',
      data: {
        labels: men.map((m: any) => m.menesis),
        datasets: [{
          label: 'Publicēti raksti',
          data: men.map((m: any) => m.skaits),
          borderColor: '#1565C0',
          backgroundColor: 'rgba(21,101,192,0.12)',
          tension: 0.4,
          fill: true,
          pointRadius: 5,
        }],
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
      },
    })
  }

}

onMounted(async () => {
  const data = await get<any>('/stats')
  if (data) stats.value = data
  await nextTick()
  buildCharts()
})
</script>
