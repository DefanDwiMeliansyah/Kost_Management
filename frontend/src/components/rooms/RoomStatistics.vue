<template>
    <!-- Statistics Cards -->
          <div class="row mb-4">
            <div class="col-md-3 mb-3">
              <div class="card bg-primary text-white">
                <div class="card-body">
                  <h6 class="card-title">Total Kamar</h6>
                  <h2 class="mb-0">{{ statistics.total }}</h2>
                </div>
              </div>
            </div>
            <div class="col-md-3 mb-3">
              <div class="card bg-success text-white">
                <div class="card-body">
                  <h6 class="card-title">Tersedia</h6>
                  <h2 class="mb-0">{{ statistics.available }}</h2>
                </div>
              </div>
            </div>
            <div class="col-md-3 mb-3">
              <div class="card bg-danger text-white">
                <div class="card-body">
                  <h6 class="card-title">Terisi</h6>
                  <h2 class="mb-0">{{ statistics.occupied }}</h2>
                </div>
              </div>
            </div>
            <div class="col-md-3 mb-3">
              <div class="card bg-warning text-white">
                <div class="card-body">
                  <h6 class="card-title">Maintenance</h6>
                  <h2 class="mb-0">{{ statistics.maintenance }}</h2>
                </div>
              </div>
            </div>
          </div>
</template>
<script>
import api from '@/services/api';

export default {
  name: 'RoomsStatistics',
  data() {
    return {
      statistics: {
        total: 0,
        available: 0,
        occupied: 0,
        maintenance: 0
      }
    };
  },
  mounted() {
    this.loadStatistics();
  },
  methods: {
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
<style scoped >

</style>