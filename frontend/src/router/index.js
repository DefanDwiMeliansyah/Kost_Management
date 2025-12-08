import { createRouter, createWebHistory } from 'vue-router'

// Import route modules
import authRoutes from './modules/auth'
import dashboardRoutes from './modules/dashboard'
import roomRoutes from './modules/rooms'
import tenantRoutes from './modules/tenants'

const routes = [
  {
    path: '/',
    redirect: '/login'
  },
  // Spread all module routes
  ...authRoutes,
  ...dashboardRoutes,
  ...roomRoutes,
  ...tenantRoutes,
  // 404 Not Found
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/views/NotFound.vue'),
    meta: { title: '404 Not Found' }
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  // Smooth scroll behavior
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0, behavior: 'smooth' }
    }
  }
})