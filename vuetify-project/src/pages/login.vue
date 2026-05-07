<template>
  <v-container class="py-16" style="max-width: 440px">
    <div class="text-center mb-8">
      <v-icon icon="mdi-book-open-variant" color="primary" size="52" class="mb-3" />
      <h1 class="text-h5 font-weight-bold mb-1">Laipni lūgts atpakaļ!</h1>
      <p class="text-body-2 text-medium-emphasis">Pieslēdzies savam BlogHub kontam</p>
    </div>

    <v-card rounded="xl" elevation="2" class="pa-6">
      <v-alert v-if="apiError" type="error" variant="tonal" class="mb-4" rounded="lg">
        {{ apiError }}
      </v-alert>

      <v-form ref="formRef" @submit.prevent="handleLogin">
        <v-text-field
          v-model="form.email"
          label="E-pasta adrese"
          type="email"
          prepend-inner-icon="mdi-email-outline"
          variant="outlined"
          :rules="[rules.required, rules.email]"
          class="mb-3"
        />

        <v-text-field
          v-model="form.password"
          label="Parole"
          :type="showPassword ? 'text' : 'password'"
          prepend-inner-icon="mdi-lock-outline"
          :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
          @click:append-inner="showPassword = !showPassword"
          variant="outlined"
          :rules="[rules.required]"
          class="mb-4"
        />

        <v-btn
          type="submit"
          color="primary"
          size="large"
          block
          rounded="lg"
          :loading="loading"
        >
          <v-icon start>mdi-login</v-icon>
          Pieslēgties
        </v-btn>
      </v-form>
    </v-card>

    <p class="text-center text-body-2 mt-5">
      Nav konta?
      <router-link to="/register" class="text-primary font-weight-medium text-decoration-none">
        Reģistrēties
      </router-link>
    </p>

    <!-- Demo credentials -->
    <v-card variant="tonal" color="info" rounded="xl" class="mt-6 pa-4">
      <p class="text-caption font-weight-bold mb-2">
        <v-icon size="14" class="mr-1">mdi-information</v-icon>
        Demo konti (parole: <code>password</code>):
      </p>
      <div class="d-flex flex-wrap ga-2">
        <v-chip size="small" @click="fillDemo('admin@bloghub.lv')">Admin</v-chip>
        <v-chip size="small" @click="fillDemo('anna@bloghub.lv')">Autore</v-chip>
        <v-chip size="small" @click="fillDemo('maris@bloghub.lv')">Autors</v-chip>
      </div>
    </v-card>
  </v-container>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useApi } from '@/composables/useApi'
import { useAuth } from '@/composables/useAuth'

const router = useRouter()
const route = useRoute()
const { post, loading } = useApi()
const { setUser } = useAuth()

const formRef = ref()
const showPassword = ref(false)
const apiError = ref('')

const form = ref({ email: '', password: '' })

const rules = {
  required: (v: string) => !!v || 'Šis lauks ir obligāts',
  email: (v: string) => /.+@.+\..+/.test(v) || 'Lūdzu ievadi derīgu e-pasta adresi',
}

function fillDemo(email: string) {
  form.value.email = email
  form.value.password = 'password'
}

async function handleLogin() {
  const { valid } = await formRef.value.validate()
  if (!valid) return

  apiError.value = ''
  const data = await post<any>('/auth/login', form.value)

  if (data) {
    setUser({ ...data.user, token: data.token })
    const redirect = (route.query.redirect as string) || '/'
    router.push(redirect)
  } else {
    apiError.value = 'Nepareizs e-pasts vai parole.'
  }
}
</script>
