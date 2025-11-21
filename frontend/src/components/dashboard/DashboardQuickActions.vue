<template>
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
</template>
<script>
export default {
  name: 'DashboardQuickActions',
}
</script>
<style scoped></style>
