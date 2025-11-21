<template>
  <div>
    <!-- Navbar Component -->
    <Navbar />

    <!-- Main Content -->
    <div class="container mt-5">
      <div class="row">
        <div class="col-12">
          <div class="card shadow">
            <div class="card-body p-5">
              <h2 class="mb-4">Dashboard</h2>
              
              <div class="alert alert-success" role="alert">
                <h5 class="alert-heading">Selamat datang, {{ user?.name }}!</h5>
                <p class="mb-0">
                  Anda berhasil login sebagai <strong>{{ user?.role }}</strong>
                </p>
              </div>

              <!-- Statistics Cards -->
              <DashboardStatistics />

              <!-- Quick Actions -->
              <DashboardQuickActions />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Navbar from '@/components/layouts/Navbar.vue';
import DashboardStatistics from '@/components/dashboard/DashboardStatistics.vue';
import DashboardQuickActions from '@/components/dashboard/DashboardQuickActions.vue';

export default {
  name: 'Dashboard',
  components: {
    Navbar,
    DashboardStatistics,
    DashboardQuickActions
  },
  data() {
    return {
      user: null,
    };
  },
  mounted() {
    this.loadUser();
  },
  methods: {
    loadUser() {
      const userData = localStorage.getItem('user');
      if (userData) {
        try {
          this.user = JSON.parse(userData);
        } catch (error) {
          console.error('Failed to parse user data:', error);
        }
      }
    },
  }
};
</script>

<style scoped>
.card {
  border: none;
  border-radius: 10px;
}

.gap-2 {
  gap: 0.5rem;
}
</style>