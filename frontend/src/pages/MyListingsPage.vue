<script setup>
import { ref, computed, onMounted } from 'vue'
import { useApi } from '../composables/useApi'
import { formatCurrency } from '../utils/format'

const { api } = useApi()

const activeListings = ref([])
const soldItems = ref([])
const loading = ref(true)
const error = ref(null)

onMounted(() => {
  fetchListings()
})

async function fetchListings() {
  loading.value = true
  error.value = null
  try {
    const response = await api.get('/api/user/listings')
    const data = response.data.data ?? response.data
    activeListings.value = data.active ?? []
    soldItems.value = data.sold ?? []
  } catch (err) {
    error.value = 'Failed to load your listings.'
  } finally {
    loading.value = false
  }
}

function timeRemaining(endsAt) {
  const now = new Date()
  const end = new Date(endsAt)
  const diff = end - now

  if (diff <= 0) return 'Ended'

  const days = Math.floor(diff / (1000 * 60 * 60 * 24))
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))

  if (days > 0) return `${days}d ${hours}h remaining`
  if (hours > 0) return `${hours}h ${minutes}m remaining`
  return `${minutes}m remaining`
}
</script>

<template>
  <div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">My Listings</h1>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-16">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
      <p class="text-red-700">{{ error }}</p>
      <button @click="fetchListings" class="mt-4 text-indigo-600 hover:text-indigo-800 font-medium">
        Try Again
      </button>
    </div>

    <template v-else>
      <!-- Active Listings -->
      <section class="mb-12">
        <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
          Active Listings
          <span class="text-sm font-normal text-gray-500">({{ activeListings.length }})</span>
        </h2>

        <div v-if="activeListings.length === 0" class="bg-gray-50 rounded-lg p-8 text-center">
          <p class="text-gray-500">You have no active listings.</p>
          <router-link to="/sell" class="mt-3 inline-block text-indigo-600 hover:text-indigo-800 font-medium">
            List an item
          </router-link>
        </div>

        <div v-else class="space-y-4">
          <router-link
            v-for="item in activeListings"
            :key="item.id"
            :to="`/item/${item.id}`"
            class="block bg-white border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow"
          >
            <div class="flex justify-between items-start">
              <div>
                <h3 class="text-lg font-medium text-gray-900">{{ item.title }}</h3>
                <div class="mt-2 flex items-center gap-4 text-sm text-gray-500">
                  <span>{{ item.bids_count ?? 0 }} {{ (item.bids_count ?? 0) === 1 ? 'bid' : 'bids' }}</span>
                  <span>{{ timeRemaining(item.ends_at) }}</span>
                </div>
              </div>
              <div class="text-right">
                <p class="text-lg font-bold text-indigo-600">{{ formatCurrency(item.current_price ?? item.starting_price) }}</p>
                <p class="text-xs text-gray-400 mt-1">current price</p>
              </div>
            </div>
          </router-link>
        </div>
      </section>

      <!-- Sold Items -->
      <section>
        <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
          Sold Items
          <span class="text-sm font-normal text-gray-500">({{ soldItems.length }})</span>
        </h2>

        <div v-if="soldItems.length === 0" class="bg-gray-50 rounded-lg p-8 text-center">
          <p class="text-gray-500">No sold items yet.</p>
        </div>

        <div v-else class="space-y-4">
          <router-link
            v-for="item in soldItems"
            :key="item.id"
            :to="`/item/${item.id}`"
            class="block bg-white border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow"
          >
            <div class="flex justify-between items-start">
              <div>
                <h3 class="text-lg font-medium text-gray-900">{{ item.title }}</h3>
                <p class="mt-1 text-sm text-gray-500">
                  Sold to <span class="font-medium text-gray-700">{{ item.buyer?.name ?? 'Unknown' }}</span>
                </p>
              </div>
              <div class="text-right">
                <p class="text-lg font-bold text-green-600">{{ formatCurrency(item.final_price ?? item.current_price) }}</p>
                <p class="text-xs text-gray-400 mt-1">final price</p>
              </div>
            </div>
          </router-link>
        </div>
      </section>
    </template>
  </div>
</template>
