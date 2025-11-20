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
              <div class="row mt-4">
                <div class="col-md-4 mb-3">
                  <div class="card bg-primary text-white">
                    <div class="card-body">
                      <h5 class="card-title">Total Kamar</h5>
                      <h2 class="mb-0">{{ statistics.total }}</h2>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="card bg-success text-white">
                    <div class="card-body">
                      <h5 class="card-title">Kamar Terisi</h5>
                      <h2 class="mb-0">{{ statistics.occupied }}</h2>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="card bg-warning text-white">
                    <div class="card-body">
                      <h5 class="card-title">Kamar Kosong</h5>
                      <h2 class="mb-0">{{ statistics.available }}</h2>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Quick Actions -->
              <div class="row mt-4">
                <div class="col-12">
                  <h5 class="mb-3">Quick Actions</h5>
                  <div class="d-flex gap-2">
                    <router-link to="/rooms" class="btn btn-primary">
                      <i class="bi bi-door-open"></i> Kelola Kamar
                    </router-link>
                    <button class="btn btn-outline-primary" disabled>
                      <i class="bi bi-people"></i> Kelola Penghuni
                    </button>
                    <button class="btn btn-outline-primary" disabled>
                      <i class="bi bi-cash-stack"></i> Pembayaran
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Navbar from '@/components/layouts/Navbar.vue';
import api from '@/services/api';

export default {
  name: 'Dashboard',
  components: {
    Navbar
  },
  data() {
    return {
      user: null,
      statistics: {
        total: 0,
        available: 0,
        occupied: 0,
        maintenance: 0
      }
    };
  },
  mounted() {
    this.loadUser();
    this.loadStatistics();
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
    async loadStatistics() {
      try {
        const response = await api.getRoomStatistics();
        if (response.data.success) {
          this.statistics = response.data.data;
        }
      } catch (error) {
        console.error('Failed to load statistics:', error);
      }
    }
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