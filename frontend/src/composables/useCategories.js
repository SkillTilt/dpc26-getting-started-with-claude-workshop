import { ref } from 'vue'
import { useApi } from './useApi'

const categories = ref([])
const loaded = ref(false)
const loading = ref(false)

export function useCategories() {
  const { api } = useApi()

  const fetchCategories = async (force = false) => {
    if (loaded.value && !force) return
    if (loading.value) return
    loading.value = true
    try {
      const response = await api.get('/api/categories')
      categories.value = response.data.data ?? response.data
      loaded.value = true
    } catch (e) {
      console.error('Failed to load categories:', e)
    } finally {
      loading.value = false
    }
  }

  return { categories, fetchCategories, loading }
}
