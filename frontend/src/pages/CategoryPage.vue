<template>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Category Header -->
            <div class="mb-8">
                <router-link to="/" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                    &larr; Back to categories
                </router-link>
                <h1 class="mt-2 text-3xl font-extrabold text-gray-900">{{ categoryName }}</h1>
            </div>

            <!-- Status Toggle -->
            <div class="mb-6 flex space-x-2">
                <button
                    class="px-4 py-2 rounded-md text-sm font-medium transition-colors"
                    :class="status === 'active'
                        ? 'bg-indigo-600 text-white'
                        : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'"
                    @click="switchStatus('active')"
                >
                    Active Auctions
                </button>
                <button
                    class="px-4 py-2 rounded-md text-sm font-medium transition-colors"
                    :class="status === 'closed'
                        ? 'bg-indigo-600 text-white'
                        : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'"
                    @click="switchStatus('closed')"
                >
                    Closed Auctions
                </button>
            </div>

            <!-- Loading Spinner -->
            <div v-if="loading" class="flex justify-center py-12">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
            </div>

            <!-- Items Grid -->
            <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <ItemCard
                    v-for="item in items"
                    :key="item.id"
                    :item="item"
                />
            </div>

            <p v-if="!loading && items.length === 0" class="text-center text-gray-500 py-12">
                No {{ status }} auctions in this category.
            </p>
        </div>
    </div>
</template>

<script>
import { useApi } from '../composables/useApi'
import ItemCard from '../components/ItemCard.vue'

export default {
    components: {
        ItemCard,
    },

    data() {
        return {
            items: [],
            categoryName: '',
            status: 'active',
            loading: true,
        }
    },

    computed: {
        slug() {
            return this.$route.params.slug
        },
    },

    watch: {
        slug() {
            this.fetchItems()
        },
    },

    mounted() {
        this.fetchItems()
    },

    methods: {
        async fetchItems() {
            this.loading = true
            const { api } = useApi()

            try {
                const response = await api.get(`/api/categories/${this.slug}/items`, {
                    params: { status: this.status },
                })
                this.items = response.data.data ?? response.data
                this.categoryName = response.data.category?.name ?? this.slug.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
            } catch (error) {
                console.error('Failed to load items:', error)
            } finally {
                this.loading = false
            }
        },

        switchStatus(newStatus) {
            if (this.status === newStatus) return
            this.status = newStatus
            this.fetchItems()
        },
    },
}
</script>
