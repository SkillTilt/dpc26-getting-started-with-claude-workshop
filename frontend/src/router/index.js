import { createRouter, createWebHistory } from 'vue-router'
import { useAuth } from '../composables/useAuth'
import HomePage from '../pages/HomePage.vue'
import LoginPage from '../pages/LoginPage.vue'
import RegisterPage from '../pages/RegisterPage.vue'
import CategoryPage from '../pages/CategoryPage.vue'
import ItemDetailPage from '../pages/ItemDetailPage.vue'
import SellPage from '../pages/SellPage.vue'
import MyListingsPage from '../pages/MyListingsPage.vue'
import MyBidsPage from '../pages/MyBidsPage.vue'
import NotFoundPage from '../pages/NotFoundPage.vue'

const routes = [
  { path: '/', name: 'home', component: HomePage },
  { path: '/login', name: 'login', component: LoginPage },
  { path: '/register', name: 'register', component: RegisterPage },
  { path: '/category/:slug', name: 'category', component: CategoryPage },
  { path: '/item/:id', name: 'item', component: ItemDetailPage },
  { path: '/sell', name: 'sell', component: SellPage, meta: { requiresAuth: true } },
  { path: '/my-listings', name: 'my-listings', component: MyListingsPage, meta: { requiresAuth: true } },
  { path: '/my-bids', name: 'my-bids', component: MyBidsPage, meta: { requiresAuth: true } },
  { path: '/:pathMatch(.*)*', name: 'not-found', component: NotFoundPage },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to) => {
  if (!to.meta.requiresAuth) return true

  const token = localStorage.getItem('auth_token')
  if (!token) return { name: 'login' }

  const { user, fetchUser } = useAuth()
  if (!user.value) {
    const fetched = await fetchUser()
    if (!fetched) return { name: 'login' }
  }

  return true
})

export default router
