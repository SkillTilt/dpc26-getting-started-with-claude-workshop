<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useApi } from '../composables/useApi'
import { useCategories } from '../composables/useCategories'

const router = useRouter()
const { api } = useApi()
const { categories, fetchCategories } = useCategories()

onMounted(() => {
  fetchCategories()
})

const title = ref('')
const description = ref('')
const startingPrice = ref('')
const categoryId = ref('')
const duration = ref('')
const image = ref(null)
const imagePreview = ref(null)
const submitting = ref(false)
const errors = ref({})

// BAD CODE 4: Hardcoded duration options
const durationOptions = [
  { label: '1 day', value: 1 },
  { label: '3 days', value: 3 },
  { label: '7 days', value: 7 },
]

function onImageChange(e) {
  const file = e.target.files[0]
  if (file) {
    image.value = file
    imagePreview.value = URL.createObjectURL(file)
  }
}

function removeImage() {
  image.value = null
  imagePreview.value = null
}

async function submitListing() {
  errors.value = {}
  submitting.value = true

  try {
    const formData = new FormData()
    formData.append('title', title.value)
    formData.append('description', description.value)
    formData.append('starting_price', parseFloat(startingPrice.value))
    formData.append('category_id', categoryId.value)
    formData.append('duration', duration.value)
    if (image.value) {
      formData.append('image', image.value)
    }

    const response = await api.post('/api/items', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    const itemId = response.data.data?.id ?? response.data.id
    router.push(`/item/${itemId}`)
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors ?? {}
    } else {
      errors.value = { general: ['Something went wrong. Please try again.'] }
    }
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Sell an Item</h1>
    <p class="text-gray-600 mb-8">List your item for auction. Fill out the details below to get started.</p>

    <!-- General Error -->
    <div v-if="errors.general" class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
      <p v-for="msg in errors.general" :key="msg" class="text-red-700 text-sm">{{ msg }}</p>
    </div>

    <form @submit.prevent="submitListing" class="space-y-6">
      <!-- Title -->
      <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
        <input
          id="title"
          v-model="title"
          type="text"
          required
          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          placeholder="What are you selling?"
        />
        <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title[0] }}</p>
      </div>

      <!-- Description -->
      <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
        <textarea
          id="description"
          v-model="description"
          rows="4"
          required
          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          placeholder="Describe your item in detail..."
        ></textarea>
        <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description[0] }}</p>
      </div>

      <!-- Image -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Image (optional)</label>
        <div v-if="imagePreview" class="mb-3 relative inline-block">
          <img :src="imagePreview" alt="Preview" class="h-40 w-40 object-cover rounded-lg border border-gray-300" />
          <button
            type="button"
            @click="removeImage"
            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600"
          >
            &times;
          </button>
        </div>
        <input
          type="file"
          accept="image/jpeg,image/png,image/webp,image/gif"
          @change="onImageChange"
          class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
        />
        <p v-if="errors.image" class="mt-1 text-sm text-red-600">{{ errors.image[0] }}</p>
      </div>

      <!-- Starting Price -->
      <div>
        <label for="starting_price" class="block text-sm font-medium text-gray-700 mb-1">Starting Price ($)</label>
        <input
          id="starting_price"
          v-model="startingPrice"
          type="number"
          min="0.01"
          step="0.01"
          required
          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          placeholder="0.00"
        />
        <p v-if="errors.starting_price" class="mt-1 text-sm text-red-600">{{ errors.starting_price[0] }}</p>
      </div>

      <!-- Category -->
      <div>
        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
        <select
          id="category"
          v-model="categoryId"
          required
          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >
          <option value="" disabled>Select a category</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
        <p v-if="errors.category_id" class="mt-1 text-sm text-red-600">{{ errors.category_id[0] }}</p>
      </div>

      <!-- Duration -->
      <div>
        <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Auction Duration</label>
        <select
          id="duration"
          v-model="duration"
          required
          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >
          <option value="" disabled>Select duration</option>
          <option v-for="opt in durationOptions" :key="opt.value" :value="opt.value">
            {{ opt.label }}
          </option>
        </select>
        <p v-if="errors.duration" class="mt-1 text-sm text-red-600">{{ errors.duration[0] }}</p>
      </div>

      <!-- Submit -->
      <div class="pt-4">
        <button
          type="submit"
          :disabled="submitting"
          class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="submitting">Listing...</span>
          <span v-else>List Item for Auction</span>
        </button>
      </div>
    </form>
  </div>
</template>
