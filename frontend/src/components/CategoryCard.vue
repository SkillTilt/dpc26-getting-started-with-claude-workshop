<script setup>
defineProps({
  category: {
    type: Object,
    required: true,
  },
})

const iconMap = {
  laptop: '💻',
  camera: '📷',
  bicycle: '🚲',
  leaf: '🌿',
  book: '📚',
  star: '⭐',
  heart: '❤️',
}
</script>

<template>
  <router-link
    :to="`/category/${category.slug}`"
    class="block bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 text-center"
  >
    <!-- Icon / Emoji placeholder -->
    <div class="text-4xl mb-3">
      {{ iconMap[category.icon] || '📦' }}
    </div>

    <!-- Category name -->
    <h3 class="text-lg font-semibold text-gray-900">
      {{ category.name }}
    </h3>

    <!-- Items count -->
    <p class="mt-1 text-sm text-gray-500">
      {{ category.items_count ?? 0 }} {{ (category.items_count ?? 0) === 1 ? 'item' : 'items' }}
    </p>
  </router-link>
</template>
