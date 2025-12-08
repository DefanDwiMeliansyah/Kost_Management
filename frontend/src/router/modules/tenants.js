export default [
  {
    path: '/tenants',
    name: 'Tenants',
    component: () => import('@/views/TenantsManagement.vue'),
    meta: { 
      requiresAuth: true,
      title: 'Tenant Management'
    }
  }
]