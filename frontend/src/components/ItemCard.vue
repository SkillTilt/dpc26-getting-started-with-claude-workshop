<script setup>
import CountdownTimer from './CountdownTimer.vue'

defineProps({
  item: {
    type: Object,
    required: true,
  },
})
</script>

<template>
  <router-link
    :to="`/item/${item.id}`"
    class="block bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden"
  >
    <!-- Image -->
    <div class="aspect-w-16 aspect-h-9 bg-gray-100">
      <img
        v-if="item.image_url"
        :src="item.image_url"
        :alt="item.title"
        class="w-full h-48 object-cover"
      />
      <div
        v-else
        class="w-full h-48 flex items-center justify-center bg-gray-200 text-gray-400"
      >
        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="1.5"
            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
          />
        </svg>
      </div>
    </div>

    <!-- Content -->
    <div class="p-4">
      <h3 class="text-lg font-semibold text-gray-900 truncate">
        {{ item.title }}
      </h3>

      <div class="mt-2 flex items-center justify-between">
        <span class="text-xl font-bold text-indigo-600">
          ${{ Number(item.current_price).toFixed(2) }}
        </span>
        <span class="text-sm text-gray-500">
          {{ item.bids_count ?? item.bid_count ?? 0 }} {{ (item.bids_count ?? item.bid_count ?? 0) === 1 ? 'bid' : 'bids' }}
        </span>
      </div>

      <div class="mt-2 flex items-center text-sm text-gray-500">
        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <CountdownTimer :ends-at="item.ends_at" />
      </div>
    </div>
  </router-link>
</template>
