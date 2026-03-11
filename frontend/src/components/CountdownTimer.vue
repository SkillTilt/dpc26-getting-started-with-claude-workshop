<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'

const props = defineProps({ endsAt: String })

const now = ref(new Date())
let interval

onMounted(() => {
  interval = setInterval(() => { now.value = new Date() }, 1000)
})
onUnmounted(() => clearInterval(interval))

const timeLeft = computed(() => {
  const end = new Date(props.endsAt)
  const diff = end - now.value
  if (diff <= 0) return 'Ended'
  const hours = Math.floor(diff / 3600000)
  const minutes = Math.floor((diff % 3600000) / 60000)
  const seconds = Math.floor((diff % 60000) / 1000)
  if (hours > 24) {
    const days = Math.floor(hours / 24)
    return `${days}d ${hours % 24}h`
  }
  return `${hours}h ${minutes}m ${seconds}s`
})
</script>

<template>
  <span :class="timeLeft === 'Ended' ? 'text-red-500' : 'text-green-600'" class="font-mono font-semibold">
    {{ timeLeft }}
  </span>
</template>
