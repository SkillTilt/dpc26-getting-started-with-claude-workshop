<script setup>
import { ref, computed, onMounted } from 'vue'
import { useApi } from '../composables/useApi'
import { formatCurrency } from '../utils/format'

const { api } = useApi()

const winningBids = ref([])
const wonBids = ref([])
const lostBids = ref([])
const loading = ref(true)
const error = ref(null)
const hasBids = computed(() => winningBids.value.length + wonBids.value.length + lostBids.value.length > 0)

onMounted(() => {
  fetchBids()
})

async function fetchBids() {
  loading.value = true
  error.value = null
  try {
    const response = await api.get('/api/user/bids')
    const data = response.data.data ?? response.data
    winningBids.value = data.winning ?? []
    wonBids.value = data.won ?? []
    lostBids.value = data.lost ?? []
  } catch (err) {
    error.value = 'Failed to load your bids.'
  } finally {
    loading.value = false
  }
}

function statusBadgeClasses(section) {
  switch (section) {
    case 'winning':
      return 'bg-green-100 text-green-800'
    case 'won':
      return 'bg-indigo-100 text-indigo-800'
    case 'lost':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

function statusLabel(section) {
  switch (section) {
    case 'winning':
      return 'Winning'
    case 'won':
      return 'Won'
    case 'lost':
      return 'Outbid / Lost'
    default:
      return ''
  }
}
</script>

<template>
  <div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">My Bids</h1>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-16">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
      <p class="text-red-700">{{ error }}</p>
      <button @click="fetchBids" class="mt-4 text-indigo-600 hover:text-indigo-800 font-medium">
        Try Again
      </button>
    </div>

    <template v-else>
      <!-- No bids at all -->
      <div
        v-if="!hasBids"
        class="bg-gray-50 rounded-lg p-12 text-center"
      >
        <p class="text-gray-500 text-lg">You haven't placed any bids yet.</p>
        <router-link to="/" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800 font-medium">
          Browse items
        </router-link>
      </div>

      <template v-else>
        <!-- Winning -->
        <section class="mb-10">
          <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
            Winning
            <span class="text-sm font-normal text-gray-500">({{ winningBids.length }})</span>
          </h2>

          <div v-if="winningBids.length === 0" class="bg-gray-50 rounded-lg p-6 text-center">
            <p class="text-gray-500">No bids currently winning.</p>
          </div>

          <div v-else class="space-y-3">
            <router-link
              v-for="bid in winningBids"
              :key="bid.id"
              :to="`/item/${bid.item.id}`"
              class="block bg-white border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow"
            >
              <div class="flex justify-between items-start">
                <div>
                  <h3 class="text-lg font-medium text-gray-900">{{ bid.item.title }}</h3>
                  <div class="mt-2 flex items-center gap-3">
                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', statusBadgeClasses('winning')]">
                      {{ statusLabel('winning') }}
                    </span>
                  </div>
                </div>
                <div class="text-right space-y-1">
                  <p class="text-sm text-gray-500">
                    Your bid: <span class="font-medium text-gray-900">{{ formatCurrency(bid.amount) }}</span>
                  </p>
                  <p class="text-sm text-gray-500">
                    Current: <span class="font-medium text-indigo-600">{{ formatCurrency(bid.item.current_price) }}</span>
                  </p>
                </div>
              </div>
            </router-link>
          </div>
        </section>

        <!-- Won -->
        <section class="mb-10">
          <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
            Won
            <span class="text-sm font-normal text-gray-500">({{ wonBids.length }})</span>
          </h2>

          <div v-if="wonBids.length === 0" class="bg-gray-50 rounded-lg p-6 text-center">
            <p class="text-gray-500">No auctions won yet.</p>
          </div>

          <div v-else class="space-y-3">
            <router-link
              v-for="bid in wonBids"
              :key="bid.id"
              :to="`/item/${bid.item.id}`"
              class="block bg-white border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow"
            >
              <div class="flex justify-between items-start">
                <div>
                  <h3 class="text-lg font-medium text-gray-900">{{ bid.item.title }}</h3>
                  <div class="mt-2 flex items-center gap-3">
                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', statusBadgeClasses('won')]">
                      {{ statusLabel('won') }}
                    </span>
                  </div>
                </div>
                <div class="text-right space-y-1">
                  <p class="text-sm text-gray-500">
                    Your bid: <span class="font-medium text-gray-900">{{ formatCurrency(bid.amount) }}</span>
                  </p>
                  <p class="text-sm text-gray-500">
                    Final: <span class="font-medium text-indigo-600">{{ formatCurrency(bid.item.current_price) }}</span>
                  </p>
                </div>
              </div>
            </router-link>
          </div>
        </section>

        <!-- Lost -->
        <section>
          <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
            Lost
            <span class="text-sm font-normal text-gray-500">({{ lostBids.length }})</span>
          </h2>

          <div v-if="lostBids.length === 0" class="bg-gray-50 rounded-lg p-6 text-center">
            <p class="text-gray-500">No lost bids.</p>
          </div>

          <div v-else class="space-y-3">
            <router-link
              v-for="bid in lostBids"
              :key="bid.id"
              :to="`/item/${bid.item.id}`"
              class="block bg-white border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow"
            >
              <div class="flex justify-between items-start">
                <div>
                  <h3 class="text-lg font-medium text-gray-900">{{ bid.item.title }}</h3>
                  <div class="mt-2 flex items-center gap-3">
                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', statusBadgeClasses('lost')]">
                      {{ statusLabel('lost') }}
                    </span>
                  </div>
                </div>
                <div class="text-right space-y-1">
                  <p class="text-sm text-gray-500">
                    Your bid: <span class="font-medium text-gray-900">{{ formatCurrency(bid.amount) }}</span>
                  </p>
                  <p class="text-sm text-gray-500">
                    Current: <span class="font-medium text-red-600">{{ formatCurrency(bid.item.current_price) }}</span>
                  </p>
                </div>
              </div>
            </router-link>
          </div>
        </section>
      </template>
    </template>
  </div>
</template>
