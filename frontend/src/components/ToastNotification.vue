<script setup>
import { ref, onMounted } from 'vue'

const toasts = ref([])

onMounted(() => {
  if (window.Echo) {
    const userId = window.Laravel?.userId
    if (userId) {
      window.Echo.private(`App.Models.User.${userId}`)
        .notification((notification) => {
          addToast(notification)
        })
    }
  }
})

function addToast(notification) {
  const id = Date.now()
  toasts.value.push({ id, ...notification })

  setTimeout(() => {
    removeToast(id)
  }, 5000)
}

function removeToast(id) {
  toasts.value = toasts.value.filter(t => t.id !== id)
}
</script>

<template>
  <div class="fixed top-4 right-4 z-50 flex flex-col gap-3">
    <transition-group name="toast">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        class="bg-white border border-gray-200 rounded-lg shadow-lg p-4 max-w-sm"
      >
        <div class="flex items-start justify-between">
          <p class="text-sm font-medium text-gray-900">{{ toast.message }}</p>
          <button
            class="ml-3 text-gray-400 hover:text-gray-600"
            @click="removeToast(toast.id)"
          >
            &times;
          </button>
        </div>
        <a
          v-if="toast.item_id"
          :href="`/items/${toast.item_id}`"
          class="text-sm text-blue-600 hover:underline mt-1 inline-block"
        >
          View item
        </a>
      </div>
    </transition-group>
  </div>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
</style>
