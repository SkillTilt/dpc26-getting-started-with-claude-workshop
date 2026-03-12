<script setup>
import { ref } from 'vue'
import { useApi } from '../composables/useApi'

const props = defineProps({
  itemId: [Number, String],
  currentPrice: Number,
})

const emit = defineEmits(['bid-placed'])
const { api } = useApi()

const bidAmount = ref('')
const error = ref('')
const loading = ref(false)

const validateBid = () => {
  const amount = parseFloat(bidAmount.value)
  if (isNaN(amount)) {
    error.value = 'Please enter a valid amount'
    return false
  }
  if (amount <= props.currentPrice) {
    error.value = 'Your bid must be higher than the current price'
    return false
  }
  return true
}

const submitBid = async () => {
  error.value = ''
  if (!validateBid()) return

  loading.value = true
  try {
    await api.post(`/api/items/${props.itemId}/bids`, {
      amount: parseFloat(bidAmount.value),
    })
    bidAmount.value = ''
    emit('bid-placed')
  } catch (e) {
    const data = e.response?.data
    error.value = data?.message || data?.error || 'Failed to place bid'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <form class="mt-4" @submit.prevent="submitBid">
    <label for="bid-amount" class="block text-sm font-medium text-gray-700">
      Your Bid
    </label>
    <div class="mt-1 flex rounded-md shadow-sm">
      <span class="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-50 px-3 text-gray-500 text-sm">
        $
      </span>
      <input
        id="bid-amount"
        v-model="bidAmount"
        type="number"
        step="0.01"
        min="0"
        :placeholder="`More than $${props.currentPrice.toFixed(2)}`"
        class="block w-full rounded-none rounded-r-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
        :disabled="loading"
      />
    </div>

    <p v-if="error" class="mt-2 text-sm text-red-600">
      {{ error }}
    </p>

    <button
      type="submit"
      :disabled="loading"
      class="mt-3 w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
    >
      <svg
        v-if="loading"
        class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
        fill="none"
        viewBox="0 0 24 24"
      >
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
      </svg>
      {{ loading ? 'Placing Bid...' : 'Place Bid' }}
    </button>
  </form>
</template>
