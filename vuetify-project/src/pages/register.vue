<template>
  <v-container class="py-16" style="max-width: 480px">
    <div class="text-center mb-8">
      <v-icon icon="mdi-account-plus" color="primary" size="52" class="mb-3" />
      <h1 class="text-h5 font-weight-bold mb-1">Izveido kontu</h1>
      <p class="text-body-2 text-medium-emphasis">Pievienojies BlogHub kopienai</p>
    </div>

    <v-card rounded="xl" elevation="2" class="pa-6">
      <v-alert v-if="apiError" type="error" variant="tonal" class="mb-4" rounded="lg">
        {{ apiError }}
      </v-alert>

      <v-form ref="formRef" @submit.prevent="handleRegister">
        <v-text-field
          v-model="form.name"
          label="Pilnais vārds"
          prepend-inner-icon="mdi-account-outline"
          variant="outlined"
          :rules="[rules.required, rules.minName]"
          class="mb-3"
        />

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
          :type="showPass ? 'text' : 'password'"
          prepend-inner-icon="mdi-lock-outline"
          :append-inner-icon="showPass ? 'mdi-eye-off' : 'mdi-eye'"
          @click:append-inner="showPass = !showPass"
          variant="outlined"
          :rules="[rules.required, rules.minPass]"
          hint="Vismaz 8 rakstzīmes"
          persistent-hint
          class="mb-3"
        />

        <v-text-field
          v-model="form.password_confirmation"
          label="Parole vēlreiz"
          :type="showPass2 ? 'text' : 'password'"
          prepend-inner-icon="mdi-lock-check-outline"
          :append-inner-icon="showPass2 ? 'mdi-eye-off' : 'mdi-eye'"
          @click:append-inner="showPass2 = !showPass2"
          variant="outlined"
          :rules="[rules.required, rules.passMatch]"
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
          <v-icon start>mdi-account-plus</v-icon>
          Reģistrēties
        </v-btn>
      </v-form>
    </v-card>

    <p class="text-center text-body-2 mt-5">
      Jau ir konts?
      <router-link to="/login" class="text-primary font-weight-medium text-decoration-none">
        Pieslēgties
      </router-link>
    </p>
  </v-container>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useApi } from '@/composables/useApi'
import { useAuth } from '@/composables/useAuth'

const router = useRouter()
const { post, loading } = useApi()
const { setUser } = useAuth()

const formRef = ref()
const showPass = ref(false)
const showPass2 = ref(false)
const apiError = ref('')

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const rules = {
  required: (v: string) => !!v || 'Šis lauks ir obligāts',
  email: (v: string) => /.+@.+\..+/.test(v) || 'Lūdzu ievadi derīgu e-pasta adresi',
  minName: (v: string) => v.length >= 2 || 'Vārdam jābūt vismaz 2 rakstzīmēm',
  minPass: (v: string) => v.length >= 8 || 'Parolei jābūt vismaz 8 rakstzīmēm',
  passMatch: (v: string) => v === form.value.password || 'Paroles nesakrīt',
}

async function handleRegister() {
  const { valid } = await formRef.value.validate()
  if (!valid) return

  apiError.value = ''
  const data = await post<any>('/auth/register', form.value)

  if (data) {
    setUser({ ...data.user, token: data.token })
    router.push('/dashboard')
  } else {
    apiError.value = 'Reģistrācija neizdevās. Iespējams e-pasts jau ir izmantots.'
  }
}
</script>
