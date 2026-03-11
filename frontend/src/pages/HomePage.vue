<script setup>
import { onMounted } from 'vue'
import { useCategories } from '../composables/useCategories'
import { useEndingSoon } from '../composables/useEndingSoon'
import CategoryCard from '../components/CategoryCard.vue'
import ItemCard from '../components/ItemCard.vue'

const { categories, fetchCategories, loading } = useCategories()
const { endingSoonItems, fetchEndingSoon, endingSoonLoading } = useEndingSoon()

onMounted(() => {
    fetchCategories()
    fetchEndingSoon()
})
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Hero Section -->
        <section class="bg-indigo-600 text-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl">
                    BidBoard
                </h1>
                <p class="mt-4 text-xl text-indigo-200 max-w-2xl mx-auto">
                    Find great deals on unique items
                </p>
            </div>
        </section>

        <!-- Ending Soon -->
        <section
            v-if="!endingSoonLoading && endingSoonItems.length > 0"
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12"
        >
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Ending Soon</h2>
            <div class="flex gap-6 overflow-x-auto pb-4">
                <ItemCard
                    v-for="item in endingSoonItems"
                    :key="item.id"
                    :item="item"
                    class="w-72 flex-shrink-0"
                />
            </div>
        </section>

        <!-- Categories Grid -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Browse Categories</h2>

            <div v-if="loading" class="flex justify-center py-12">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
            </div>

            <div
                v-else
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
            >
                <CategoryCard
                    v-for="category in categories"
                    :key="category.id"
                    :category="category"
                />
            </div>

            <p v-if="!loading && categories.length === 0" class="text-center text-gray-500 py-12">
                No categories found.
            </p>
        </section>
    </div>
</template>
