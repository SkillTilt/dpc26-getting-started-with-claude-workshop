import { ref } from 'vue'
import { useApi } from './useApi'

const endingSoonItems = ref([])
const loaded = ref(false)
const endingSoonLoading = ref(false)

export function useEndingSoon() {
  const { api } = useApi()

  const fetchEndingSoon = async (force = false) => {
    if (loaded.value && !force) return
    if (endingSoonLoading.value) return
    endingSoonLoading.value = true
    try {
      const response = await api.get('/api/items?filter=ending_soon')
      endingSoonItems.value = response.data.data ?? response.data
      loaded.value = true
    } catch (e) {
      console.error('Failed to load ending soon items:', e)
    } finally {
      endingSoonLoading.value = false
    }
  }

  return { endingSoonItems, fetchEndingSoon, endingSoonLoading }
}
