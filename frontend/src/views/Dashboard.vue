<template>
  <div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Kost Management</a>

        <!-- Toggler -->
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <!-- ===========================
             DESKTOP VERSION (DROPDOWN)
            ============================ -->
            <li class="nav-item dropdown d-none d-lg-block">
              <a
                class="nav-link dropdown-toggle"
                href="#"
                id="userDropdown"
                role="button"
                data-bs-toggle="dropdown"
              >
                {{ user?.name }}
              </a>

              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li>
                  <a class="dropdown-item" href="#" @click.prevent="handleLogout"> Logout </a>
                </li>
              </ul>
            </li>

            <!-- ===========================
              MOBILE VERSION (NO DROPDOWN)
            ============================ -->
            <li class="nav-item d-lg-none">
              <a class="nav-link fw-bold disabled text-white">
                {{ user?.name }}
              </a>
            </li>

            <li class="nav-item d-lg-none">
              <a class="nav-link" href="#">Profile</a>
            </li>

            <li class="nav-item d-lg-none">
              <a class="nav-link" href="#" @click.prevent="handleLogout"> Logout </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

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

              <div class="row mt-4">
                <div class="col-md-4 mb-3">
                  <div class="card bg-primary text-white">
                    <div class="card-body">
                      <h5 class="card-title">Total Kamar</h5>
                      <h2>0</h2>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="card bg-success text-white">
                    <div class="card-body">
                      <h5 class="card-title">Kamar Terisi</h5>
                      <h2>0</h2>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="card bg-warning text-white">
                    <div class="card-body">
                      <h5 class="card-title">Kamar Kosong</h5>
                      <h2>0</h2>
                    </div>
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
import api from '@/services/api'

export default {
  name: 'Dashboard',
  data() {
    return {
      user: null,
    }
  },
  mounted() {
    this.loadUser()
  },
  methods: {
    loadUser() {
      const userData = localStorage.getItem('user')
      if (userData) {
        this.user = JSON.parse(userData)
      }
    },
    async handleLogout() {
      try {
        await api.logout()
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        this.$router.push('/login')
      }
    },
  },
}
</script>

<style scoped>
.card {
  border: none;
  border-radius: 10px;
}
</style>
