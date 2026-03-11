<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../composables/useAuth'

defineProps({
  categories: {
    type: Array,
    default: () => [],
  },
})

const router = useRouter()
const { user, isAuthenticated: isLoggedIn, logout } = useAuth()
const mobileMenuOpen = ref(false)

const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value
}

const handleLogout = async () => {
  await logout()
  router.push('/')
}
</script>

<template>
  <nav class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Logo / Brand -->
        <div class="flex items-center">
          <router-link to="/" class="text-xl font-bold text-indigo-600">
            BidBoard
          </router-link>
        </div>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex md:items-center md:space-x-6">
          <!-- Category Links -->
          <router-link
            v-for="category in categories"
            :key="category.slug"
            :to="`/category/${category.slug}`"
            class="text-gray-600 hover:text-indigo-600 transition-colors text-sm font-medium"
          >
            {{ category.name }}
          </router-link>

          <div class="h-6 w-px bg-gray-300" />

          <!-- Logged-in links -->
          <template v-if="isLoggedIn">
            <span class="text-sm text-gray-700 font-medium">{{ user?.name }}</span>
            <router-link to="/my-listings" class="text-sm text-gray-600 hover:text-indigo-600 transition-colors">
              My Listings
            </router-link>
            <router-link to="/my-bids" class="text-sm text-gray-600 hover:text-indigo-600 transition-colors">
              My Bids
            </router-link>
            <router-link
              to="/sell"
              class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition-colors"
            >
              Sell
            </router-link>
            <button
              class="text-sm text-gray-600 hover:text-red-600 transition-colors"
              @click="handleLogout"
            >
              Logout
            </button>
          </template>

          <!-- Guest links -->
          <template v-else>
            <router-link to="/login" class="text-sm text-gray-600 hover:text-indigo-600 transition-colors">
              Login
            </router-link>
            <router-link
              to="/register"
              class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition-colors"
            >
              Register
            </router-link>
          </template>
        </div>

        <!-- Mobile menu button -->
        <div class="flex items-center md:hidden">
          <button
            class="text-gray-600 hover:text-gray-900 focus:outline-none"
            @click="toggleMobileMenu"
          >
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                v-if="!mobileMenuOpen"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
              <path
                v-else
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <div v-if="mobileMenuOpen" class="md:hidden border-t border-gray-200 bg-white">
      <div class="px-4 py-3 space-y-2">
        <!-- Category Links -->
        <router-link
          v-for="category in categories"
          :key="category.slug"
          :to="`/category/${category.slug}`"
          class="block text-gray-600 hover:text-indigo-600 text-sm font-medium py-1"
          @click="mobileMenuOpen = false"
        >
          {{ category.name }}
        </router-link>

        <hr class="border-gray-200" />

        <!-- Logged-in links -->
        <template v-if="isLoggedIn">
          <span class="block text-sm text-gray-700 font-medium py-1">{{ user?.name }}</span>
          <router-link to="/my-listings" class="block text-sm text-gray-600 hover:text-indigo-600 py-1" @click="mobileMenuOpen = false">
            My Listings
          </router-link>
          <router-link to="/my-bids" class="block text-sm text-gray-600 hover:text-indigo-600 py-1" @click="mobileMenuOpen = false">
            My Bids
          </router-link>
          <router-link to="/sell" class="block text-sm text-indigo-600 font-medium py-1" @click="mobileMenuOpen = false">
            Sell
          </router-link>
          <button
            class="block w-full text-left text-sm text-red-600 py-1"
            @click="handleLogout(); mobileMenuOpen = false"
          >
            Logout
          </button>
        </template>

        <!-- Guest links -->
        <template v-else>
          <router-link to="/login" class="block text-sm text-gray-600 hover:text-indigo-600 py-1" @click="mobileMenuOpen = false">
            Login
          </router-link>
          <router-link to="/register" class="block text-sm text-indigo-600 font-medium py-1" @click="mobileMenuOpen = false">
            Register
          </router-link>
        </template>
      </div>
    </div>
  </nav>
</template>
