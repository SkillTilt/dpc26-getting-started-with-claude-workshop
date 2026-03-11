import { ref, computed } from 'vue'
import { useApi } from './useApi'

const user = ref(null)
const token = ref(localStorage.getItem('auth_token'))

export function useAuth() {
  const { api } = useApi()
  const isAuthenticated = computed(() => !!token.value)

  const login = async (credentials) => {
    const { data } = await api.post('/api/login', credentials)
    token.value = data.token
    localStorage.setItem('auth_token', data.token)
    user.value = data.user
    return data
  }

  const register = async (userData) => {
    const { data } = await api.post('/api/register', userData)
    token.value = data.token
    localStorage.setItem('auth_token', data.token)
    user.value = data.user
    return data
  }

  const logout = async () => {
    try {
      await api.post('/api/logout')
    } catch (e) {
      // ignore
    }
    token.value = null
    user.value = null
    localStorage.removeItem('auth_token')
  }

  const fetchUser = async () => {
    if (!token.value) return null
    try {
      const { data } = await api.get('/api/user')
      user.value = data.data ?? data
      return user.value
    } catch (e) {
      token.value = null
      localStorage.removeItem('auth_token')
      return null
    }
  }

  return { user, token, isAuthenticated, login, register, logout, fetchUser }
}
