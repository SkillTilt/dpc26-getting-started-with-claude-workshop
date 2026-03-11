<script setup>
import { onMounted } from 'vue'
import { useCategories } from '../composables/useCategories'
import { useAuth } from '../composables/useAuth'
import NavBar from '../components/NavBar.vue'
import ToastNotification from '../components/ToastNotification.vue'

const { categories, fetchCategories } = useCategories()
const { fetchUser } = useAuth()

onMounted(() => {
  fetchCategories()
  fetchUser()
})
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <NavBar :categories="categories" />
    <ToastNotification />
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <router-view />
    </main>
  </div>
</template>
