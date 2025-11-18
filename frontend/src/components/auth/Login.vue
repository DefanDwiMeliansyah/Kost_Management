<template>
  <div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
      <div class="col-md-5">
        <div class="card shadow">
          <div class="card-body p-5">
            <div class="text-center mb-4">
              <h3 class="fw-bold">Login</h3>
              <p class="text-muted">Masuk ke akun Anda</p>
            </div>

            <!-- Alert -->
            <div v-if="error" class="alert alert-danger alert-dismissible fade show" role="alert">
              {{ error }}
              <button type="button" class="btn-close" @click="error = ''" aria-label="Close"></button>
            </div>

            <form @submit.prevent="handleLogin">
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
                <label for="password" class="form-label">Password</label>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  v-model="form.password"
                  placeholder="Masukkan password"
                  required
                />
              </div>

              <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" v-model="form.remember" />
                <label class="form-check-label" for="remember">
                  Ingat saya
                </label>
              </div>

              <button type="submit" class="btn btn-primary w-100 mb-3" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                {{ loading ? 'Memproses...' : 'Login' }}
              </button>

              <div class="text-center">
                <p class="mb-0">
                  Belum punya akun?
                  <router-link to="/register" class="text-decoration-none">Daftar di sini</router-link>
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
  name: 'Login',
  data() {
    return {
      form: {
        email: '',
        password: '',
        remember: false,
      },
      loading: false,
      error: '',
    };
  },
  methods: {
    async handleLogin() {
      this.loading = true;
      this.error = '';

      try {
        const response = await api.login({
          email: this.form.email,
          password: this.form.password,
        });

        if (response.data.success) {
          localStorage.setItem('token', response.data.data.token);
          localStorage.setItem('user', JSON.stringify(response.data.data.user));
          this.$router.push('/dashboard');
        }
      } catch (error) {
        if (error.response) {
          this.error = error.response.data.message || 'Login gagal';
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