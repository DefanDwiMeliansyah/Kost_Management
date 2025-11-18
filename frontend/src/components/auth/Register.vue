<template>
  <div class="container">
    <div class="row justify-content-center align-items-center min-vh-100 py-5">
      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-body p-5">
            <div class="text-center mb-4">
              <h3 class="fw-bold">Daftar Akun</h3>
              <p class="text-muted">Buat akun baru Anda</p>
            </div>

            <div v-if="error" class="alert alert-danger alert-dismissible fade show" role="alert">
              <div v-if="typeof error === 'string'">{{ error }}</div>
              <ul v-else class="mb-0">
                <li v-for="(err, key) in error" :key="key">{{ err[0] }}</li>
              </ul>
              <button type="button" class="btn-close" @click="error = ''" aria-label="Close"></button>
            </div>

            <div v-if="success" class="alert alert-success alert-dismissible fade show" role="alert">
              {{ success }}
              <button type="button" class="btn-close" @click="success = ''" aria-label="Close"></button>
            </div>

            <form @submit.prevent="handleRegister">
              <div class="mb-3">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input
                  type="text"
                  class="form-control"
                  id="name"
                  v-model="form.name"
                  placeholder="Masukkan nama lengkap"
                  required
                />
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  v-model="form.email"
                  placeholder="nama@example.com"
                  required
                />
              </div>

              <div class="mb-3">
                <label for="phone" class="form-label">No. Telepon</label>
                <input
                  type="tel"
                  class="form-control"
                  id="phone"
                  v-model="form.phone"
                  placeholder="08xxxxxxxxxx"
                />
              </div>

              <div class="mb-3">
                <label for="role" class="form-label">Daftar Sebagai</label>
                <select class="form-select" id="role" v-model="form.role">
                  <option value="penyewa">Penyewa</option>
                  <option value="pemilik">Pemilik Kost</option>
                </select>
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  v-model="form.password"
                  placeholder="Min. 6 karakter"
                  required
                />
              </div>

              <div class="mb-4">
                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                <input
                  type="password"
                  class="form-control"
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  placeholder="Ulangi password"
                  required
                />
              </div>

              <button type="submit" class="btn btn-primary w-100 mb-3" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                {{ loading ? 'Memproses...' : 'Daftar' }}
              </button>

              <div class="text-center">
                <p class="mb-0">
                  Sudah punya akun?
                  <router-link to="/login" class="text-decoration-none">Login di sini</router-link>
                </p>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/services/api';

export default {
  name: 'Register',
  data() {
    return {
      form: {
        name: '',
        email: '',
        phone: '',
        role: 'penyewa',
        password: '',
        password_confirmation: '',
      },
      loading: false,
      error: '',
      success: '',
    };
  },
  methods: {
    async handleRegister() {
      this.loading = true;
      this.error = '';
      this.success = '';

      try {
        const response = await api.register(this.form);

        if (response.data.success) {
          this.success = 'Pendaftaran berhasil! Mengalihkan ke dashboard...';
          
          localStorage.setItem('token', response.data.data.token);
          localStorage.setItem('user', JSON.stringify(response.data.data.user));

          setTimeout(() => {
            this.$router.push('/dashboard');
          }, 2000);
        }
      } catch (error) {
        if (error.response) {
          this.error = error.response.data.errors || error.response.data.message || 'Pendaftaran gagal';
        } else {
          this.error = 'Terjadi kesalahan. Silakan coba lagi.';
        }
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.min-vh-100 {
  min-height: 100vh;
}

.card {
  border: none;
  border-radius: 10px;
}

.btn-primary {
  padding: 12px;
  font-weight: 500;
}
</style>