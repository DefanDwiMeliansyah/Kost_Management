<template>
  <div>
    <!-- Navbar Component -->
    <Navbar />

    <!-- Main Content -->
    <div class="container mt-4">
      <div class="row">
        <div class="col-12">
          <h2 class="mb-4">Manajemen Kamar</h2>

          <!-- Statistics Cards -->
          <RoomStatistics />

          <!-- Room List Component -->
          <RoomList @room-updated="loadStatistics" @room-deleted="loadStatistics" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Navbar from '@/components/layouts/Navbar.vue'
import RoomList from '@/components/rooms/RoomList.vue'
import RoomStatistics from '@/components/rooms/RoomStatistics.vue'

export default {
  name: 'RoomsManagement',
  components: {
    Navbar,
    RoomStatistics,
    RoomList,
  },
}
</script>

<style scoped>
.card {
  border: none;
  border-radius: 10px;
}
</style>
