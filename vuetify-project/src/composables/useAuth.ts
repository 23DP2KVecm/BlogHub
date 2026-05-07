import { reactive, readonly } from 'vue'

export interface AuthUser {
  id: number
  name: string
  email: string
  role: string
  token: string
}

const state = reactive<{ user: AuthUser | null }>({ user: null })

try {
  const stored = localStorage.getItem('bloghub_user')
  if (stored) state.user = JSON.parse(stored)
} catch {}

export function useAuth() {
  function setUser(user: AuthUser) {
    state.user = user
    localStorage.setItem('bloghub_user', JSON.stringify(user))
  }

  function logout() {
    state.user = null
    localStorage.removeItem('bloghub_user')
  }

  function isLoggedIn(): boolean {
    return !!state.user?.token
  }

  function isAdmin(): boolean {
    return state.user?.role === 'administrators'
  }

  function canWrite(): boolean {
    return ['autors', 'moderators', 'administrators', 'lietotajs'].includes(state.user?.role ?? '')
  }

  return {
    user: readonly(state),
    setUser,
    logout,
    isLoggedIn,
    isAdmin,
    canWrite,
  }
}
