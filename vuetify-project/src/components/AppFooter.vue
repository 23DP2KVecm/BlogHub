<template>
  <v-footer color="surface" class="mt-12 py-10 border-t">
    <v-container>
      <v-row>
        <v-col cols="12" md="4" class="mb-6 mb-md-0">
          <div class="d-flex align-center mb-3">
            <v-icon icon="mdi-book-open-variant" color="primary" size="28" class="mr-2" />
            <span class="text-h6 font-weight-bold text-primary">Blog</span>
            <span class="text-h6 font-weight-bold">Hub</span>
          </div>
          <p class="text-body-2 text-medium-emphasis">
            Latvijas digitālā dienasgrāmatu platforma. Dalies ar savām domām un atklājumiem ar visu pasauli.
          </p>
          <div class="d-flex ga-1 mt-4">
            <v-btn icon size="small" variant="text" href="https://github.com" target="_blank">
              <v-icon>mdi-github</v-icon>
            </v-btn>
            <v-btn icon size="small" variant="text">
              <v-icon>mdi-twitter</v-icon>
            </v-btn>
            <v-btn icon size="small" variant="text">
              <v-icon>mdi-facebook</v-icon>
            </v-btn>
          </div>
        </v-col>

        <v-col cols="6" md="2">
          <p class="text-subtitle-2 font-weight-bold mb-3">Navigācija</p>
          <div class="d-flex flex-column ga-2">
            <router-link to="/" class="text-body-2 text-medium-emphasis text-decoration-none link-hover">Sākums</router-link>
            <router-link to="/raksti" class="text-body-2 text-medium-emphasis text-decoration-none link-hover">Raksti</router-link>
            <router-link to="/kategorijas" class="text-body-2 text-medium-emphasis text-decoration-none link-hover">Kategorijas</router-link>
            <router-link to="/par-mums" class="text-body-2 text-medium-emphasis text-decoration-none link-hover">Par mums</router-link>
          </div>
        </v-col>

        <v-col cols="6" md="2">
          <p class="text-subtitle-2 font-weight-bold mb-3">Konts</p>
          <div class="d-flex flex-column ga-2">
            <router-link to="/login" class="text-body-2 text-medium-emphasis text-decoration-none link-hover">Pieslēgties</router-link>
            <router-link to="/register" class="text-body-2 text-medium-emphasis text-decoration-none link-hover">Reģistrēties</router-link>
          </div>
        </v-col>

        <v-col cols="12" md="4">
          <p class="text-subtitle-2 font-weight-bold mb-3">Jaunumi e-pastā</p>
          <p class="text-body-2 text-medium-emphasis mb-3">Saņem jaunākos rakstus tieši savā pastkastītē.</p>
          <v-text-field
            v-model="email"
            placeholder="tavs@epasts.lv"
            variant="outlined"
            density="compact"
            hide-details
            class="mb-2"
            prepend-inner-icon="mdi-email-outline"
          />
          <v-btn color="primary" block @click="subscribe" :disabled="!email">
            <v-icon start>mdi-send</v-icon>
            Abonēt
          </v-btn>
        </v-col>
      </v-row>

      <v-divider class="my-6" />

      <div class="d-flex flex-wrap justify-space-between align-center">
        <p class="text-caption text-medium-emphasis">
          © {{ currentYear }} BlogHub. Visas tiesības aizsargātas.
        </p>
        <p class="text-caption text-medium-emphasis">
          Izstrādāts ar Vue.js + Vuetify + Laravel
        </p>
      </div>
    </v-container>
  </v-footer>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

const email = ref('')
const currentYear = new Date().getFullYear()

function subscribe() {
  if (email.value) {
    email.value = ''
  }
}
</script>

<style scoped>
.link-hover:hover {
  color: rgb(var(--v-theme-primary)) !important;
}
.border-t {
  border-top: 1px solid rgba(var(--v-theme-on-surface), 0.12) !important;
}
</style>
