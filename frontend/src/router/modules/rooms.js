export default [
  {
    path: '/rooms',
    name: 'Rooms',
    component: () => import('@/views/RoomsManagement.vue'),
    meta: { 
      requiresAuth: true,
      title: 'Room Management'
    }
  }
]