<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useApi } from '../composables/useApi'
import { useAuth } from '../composables/useAuth'
import { formatCurrency } from '../utils/format'
import CountdownTimer from '../components/CountdownTimer.vue'
import BidForm from '../components/BidForm.vue'

const route = useRoute()
const { api } = useApi()
const { user } = useAuth()

const item = ref(null)
const loading = ref(true)
const error = ref(null)

const itemId = computed(() => route.params.id)

const isAuctionActive = computed(() => {
  if (!item.value) return false
  return new Date(item.value.ends_at) > new Date()
})

const sortedBids = computed(() => {
  if (!item.value?.bids) return []
  return [...item.value.bids].sort(
    (a, b) => new Date(b.created_at) - new Date(a.created_at)
  )
})

const currentPrice = computed(() => {
  if (!item.value) return 0
  if (sortedBids.value.length > 0) {
    return Number(sortedBids.value[0].amount)
  }
  return Number(item.value.starting_price)
})

async function fetchItem() {
  loading.value = true
  error.value = null
  try {
    const response = await api.get(`/api/items/${itemId.value}`)
    item.value = response.data.data ?? response.data
  } catch (err) {
    error.value = 'Failed to load item details.'
  } finally {
    loading.value = false
  }
}

function onBidPlaced() {
  fetchItem()
}

function formatTime(dateString) {
  return new Date(dateString).toLocaleString()
}

watch(() => route.params.id, () => {
  fetchItem()
})

onMounted(fetchItem)
</script>

<template>
  <div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-16">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
      <p class="text-red-700">{{ error }}</p>
      <button @click="fetchItem" class="mt-4 text-indigo-600 hover:text-indigo-800 font-medium">
        Try Again
      </button>
    </div>

    <!-- Item Detail -->
    <div v-else-if="item">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Image -->
        <div>
          <img
            v-if="item.image_url"
            :src="item.image_url"
            :alt="item.title"
            class="w-full rounded-lg shadow-md object-cover aspect-square"
          />
          <div
            v-else
            class="w-full rounded-lg shadow-md bg-gray-200 flex items-center justify-center aspect-square"
          >
            <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
        </div>

        <!-- Info -->
        <div class="space-y-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ item.title }}</h1>
            <p class="mt-1 text-sm text-gray-500">
              Listed by <span class="font-medium text-gray-700">{{ item.seller?.name ?? 'Unknown' }}</span>
            </p>
          </div>

          <p class="text-gray-700 leading-relaxed">{{ item.description }}</p>

          <div class="bg-gray-50 rounded-lg p-4 space-y-3">
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-500">Current Price</span>
              <span class="text-2xl font-bold text-indigo-600">{{ formatCurrency(currentPrice) }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-500">Starting Price</span>
              <span class="text-gray-700">{{ formatCurrency(item.starting_price) }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-500">Total Bids</span>
              <span class="text-gray-700">{{ item.bids?.length ?? 0 }}</span>
            </div>
          </div>

          <!-- Countdown Timer - Bug 3: passes ends_at directly without timezone conversion -->
          <div class="bg-white border border-gray-200 rounded-lg p-4">
            <p class="text-sm text-gray-500 mb-2">Time Remaining</p>
            <CountdownTimer :ends-at="item.ends_at" />
          </div>

          <!-- Bid Form -->
          <BidForm
            v-if="user && isAuctionActive && user.id !== item?.seller?.id"
            :item-id="item.id"
            :current-price="currentPrice"
            @bid-placed="onBidPlaced"
          />

          <div v-else-if="user && isAuctionActive && user.id === item?.seller?.id" class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
            <p class="text-blue-800 font-medium">This is your listing.</p>
          </div>

          <div v-else-if="!user && isAuctionActive" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
            <p class="text-yellow-800">
              <router-link to="/login" class="font-medium underline hover:text-yellow-900">Log in</router-link>
              to place a bid.
            </p>
          </div>

          <div v-else-if="!isAuctionActive" class="bg-gray-100 rounded-lg p-4 text-center">
            <p class="text-gray-600 font-medium">This auction has ended.</p>
          </div>
        </div>
      </div>

      <!-- Bid History -->
      <div class="mt-12">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Bid History</h2>

        <div v-if="sortedBids.length === 0" class="text-gray-500 text-center py-8">
          No bids yet. Be the first to bid!
        </div>

        <div v-else class="bg-white border border-gray-200 rounded-lg overflow-hidden">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bidder</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="bid in sortedBids" :key="bid.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ bid.user?.name ?? 'Anonymous' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">{{ formatCurrency(bid.amount) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatTime(bid.created_at) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
