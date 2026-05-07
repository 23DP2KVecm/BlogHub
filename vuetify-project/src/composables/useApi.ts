import { ref } from 'vue'

const BASE_URL = '/api'

function authHeaders(extra: Record<string, string> = {}): Record<string, string> {
  const headers: Record<string, string> = { Accept: 'application/json', ...extra }
  try {
    const stored = localStorage.getItem('bloghub_user')
    if (stored) {
      const user = JSON.parse(stored)
      if (user?.token) headers['Authorization'] = `Bearer ${user.token}`
    }
  } catch {}
  return headers
}

export function useApi() {
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function get<T>(
    endpoint: string,
    params?: Record<string, string | number>,
  ): Promise<T | null> {
    loading.value = true
    error.value = null
    try {
      const url = new URL(BASE_URL + endpoint, window.location.origin)
      if (params) {
        Object.entries(params).forEach(([k, v]) => url.searchParams.set(k, String(v)))
      }
      const res = await fetch(url.toString(), { headers: authHeaders() })
      if (!res.ok) throw new Error(`HTTP ${res.status}`)
      return (await res.json()) as T
    } catch (e) {
      error.value = (e as Error).message
      return null
    } finally {
      loading.value = false
    }
  }

  async function post<T>(endpoint: string, body: unknown): Promise<T | null> {
    loading.value = true
    error.value = null
    try {
      const res = await fetch(BASE_URL + endpoint, {
        method: 'POST',
        headers: authHeaders({ 'Content-Type': 'application/json' }),
        body: JSON.stringify(body),
      })
      const data = await res.json()
      if (!res.ok) throw new Error(data?.message ?? `HTTP ${res.status}`)
      return data as T
    } catch (e) {
      error.value = (e as Error).message
      return null
    } finally {
      loading.value = false
    }
  }

  async function put<T>(endpoint: string, body: unknown): Promise<T | null> {
    loading.value = true
    error.value = null
    try {
      const res = await fetch(BASE_URL + endpoint, {
        method: 'PUT',
        headers: authHeaders({ 'Content-Type': 'application/json' }),
        body: JSON.stringify(body),
      })
      const data = await res.json()
      if (!res.ok) throw new Error(data?.message ?? `HTTP ${res.status}`)
      return data as T
    } catch (e) {
      error.value = (e as Error).message
      return null
    } finally {
      loading.value = false
    }
  }

  async function patch<T>(endpoint: string, body: unknown): Promise<T | null> {
    loading.value = true
    error.value = null
    try {
      const res = await fetch(BASE_URL + endpoint, {
        method: 'PATCH',
        headers: authHeaders({ 'Content-Type': 'application/json' }),
        body: JSON.stringify(body),
      })
      const data = await res.json()
      if (!res.ok) throw new Error(data?.message ?? `HTTP ${res.status}`)
      return data as T
    } catch (e) {
      error.value = (e as Error).message
      return null
    } finally {
      loading.value = false
    }
  }

  async function del(endpoint: string): Promise<boolean> {
    loading.value = true
    error.value = null
    try {
      const res = await fetch(BASE_URL + endpoint, {
        method: 'DELETE',
        headers: authHeaders(),
      })
      if (!res.ok) throw new Error(`HTTP ${res.status}`)
      return true
    } catch (e) {
      error.value = (e as Error).message
      return false
    } finally {
      loading.value = false
    }
  }

  return { loading, error, get, post, put, patch, del }
}
