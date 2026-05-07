<template>
  <v-app-bar elevation="1" color="surface" app>
    <v-container class="d-flex align-center pa-0">
      <router-link to="/" class="text-decoration-none d-flex align-center">
        <v-icon icon="mdi-book-open-variant" color="primary" size="32" class="mr-2" />
        <span class="text-h6 font-weight-bold text-primary">Blog</span>
        <span class="text-h6 font-weight-bold">Hub</span>
      </router-link>

      <v-spacer />

      <div class="d-none d-md-flex align-center ga-1">
        <v-btn variant="text" to="/">Sākums</v-btn>
        <v-btn variant="text" to="/raksti">Raksti</v-btn>
        <v-btn variant="text" to="/kategorijas">Kategorijas</v-btn>
        <v-btn variant="text" to="/statistika">Statistika</v-btn>
        <v-btn v-if="isAdmin()" variant="text" to="/admin" color="error">
          <v-icon start size="16">mdi-shield-crown</v-icon>
          Admin
        </v-btn>
      </div>

      <v-spacer />

      <div class="d-flex align-center ga-2">
        <v-btn icon @click="toggleTheme" :title="isDark ? 'Gaišais režīms' : 'Tumšais režīms'">
          <v-icon>{{ isDark ? 'mdi-white-balance-sunny' : 'mdi-weather-night' }}</v-icon>
        </v-btn>

        <!-- Logged in -->
        <template v-if="isLoggedIn()">
          <v-btn variant="tonal" color="primary" class="d-none d-sm-flex" to="/dashboard">
            <v-icon start>mdi-pencil</v-icon>
            Mans konts
          </v-btn>
          <v-menu>
            <template #activator="{ props }">
              <v-avatar v-bind="props" :color="avatarColor" size="36" class="cursor-pointer">
                <span class="text-caption font-weight-bold text-white">{{ initials }}</span>
              </v-avatar>
            </template>
            <v-list density="compact" min-width="180">
              <v-list-item :subtitle="user.user?.email">
                <template #title>
                  <span class="font-weight-bold">{{ user.user?.name }}</span>
                </template>
              </v-list-item>
              <v-divider />
              <v-list-item to="/dashboard" prepend-icon="mdi-view-dashboard" title="Mans konts" />
              <v-list-item to="/dashboard/raksti/izveidot" prepend-icon="mdi-plus" title="Jauns raksts" />
              <v-divider />
              <v-list-item prepend-icon="mdi-logout" title="Iziet" @click="handleLogout" class="text-error" />
            </v-list>
          </v-menu>
        </template>

        <!-- Not logged in -->
        <template v-else>
          <v-btn variant="outlined" color="primary" class="d-none d-sm-flex" to="/login">
            Pieslēgties
          </v-btn>
          <v-btn color="primary" class="d-none d-sm-flex" to="/register">
            Reģistrēties
          </v-btn>
        </template>

        <v-btn icon class="d-md-none" @click="drawer = !drawer">
          <v-icon>mdi-menu</v-icon>
        </v-btn>
      </div>
    </v-container>
  </v-app-bar>

  <v-navigation-drawer v-model="drawer" temporary location="right">
    <v-list nav>
      <v-list-item to="/" prepend-icon="mdi-home" title="Sākums" />
      <v-list-item to="/raksti" prepend-icon="mdi-newspaper" title="Raksti" />
      <v-list-item to="/kategorijas" prepend-icon="mdi-tag-multiple" title="Kategorijas" />
      <v-list-item to="/statistika" prepend-icon="mdi-chart-bar" title="Statistika" />
      <v-list-item v-if="isAdmin()" to="/admin" prepend-icon="mdi-shield-crown" title="Admin panelis" />
      <v-divider class="my-2" />
      <template v-if="isLoggedIn()">
        <v-list-item to="/dashboard" prepend-icon="mdi-view-dashboard" title="Mans konts" />
        <v-list-item to="/dashboard/raksti/izveidot" prepend-icon="mdi-plus" title="Jauns raksts" />
        <v-list-item prepend-icon="mdi-logout" title="Iziet" @click="handleLogout" />
      </template>
      <template v-else>
        <v-list-item to="/login" prepend-icon="mdi-login" title="Pieslēgties" />
        <v-list-item to="/register" prepend-icon="mdi-account-plus" title="Reģistrēties" />
      </template>
    </v-list>
  </v-navigation-drawer>
</template>

<script lang="ts" setup>
import { ref, computed } from 'vue'
import { useTheme } from 'vuetify'
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import { useApi } from '@/composables/useApi'

const drawer = ref(false)
const theme = useTheme()
const router = useRouter()
const isDark = ref(theme.global.current.value.dark)
const { user, logout, isLoggedIn, isAdmin } = useAuth()
const { post } = useApi()

const COLORS = ['#1565C0', '#2E7D32', '#AD1457', '#E65100', '#4527A0']

const initials = computed(() => {
  const name = user.user?.name ?? ''
  return name.split(' ').map((n) => n[0]).join('').toUpperCase().slice(0, 2)
})

const avatarColor = computed(() => {
  const name = user.user?.name ?? ''
  return COLORS[name.charCodeAt(0) % COLORS.length]
})

function toggleTheme() {
  theme.global.name.value = isDark.value ? 'blogLight' : 'blogDark'
  isDark.value = !isDark.value
}

async function handleLogout() {
  await post('/auth/logout', {})
  logout()
  router.push('/')
}
</script>
