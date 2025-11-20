<template>
  <div class="card shadow">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Daftar Kamar</h4>
        <button class="btn btn-primary" @click="openAddModal">
          <i class="bi bi-plus-circle"></i> Tambah Kamar
        </button>
      </div>

      <!-- Filters -->
      <div class="row mb-4">
        <div class="col-md-4">
          <select class="form-select" v-model="filters.status" @change="loadRooms">
            <option value="">Semua Status</option>
            <option value="available">Tersedia</option>
            <option value="occupied">Terisi</option>
            <option value="maintenance">Maintenance</option>
          </select>
        </div>
        <div class="col-md-4">
          <select class="form-select" v-model="filters.room_type" @change="loadRooms">
            <option value="">Semua Tipe</option>
            <option value="single">Single</option>
            <option value="double">Double</option>
            <option value="shared">Shared</option>
          </select>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>

      <!-- Table -->
      <div v-else-if="rooms.length > 0" class="table-responsive">
        <table class="table table-hover">
          <thead class="table-light">
            <tr>
              <th>No. Kamar</th>
              <th>Lantai</th>
              <th>Tipe</th>
              <th>Harga/Bulan</th>
              <th>Status</th>
              <th>Fasilitas</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="room in rooms" :key="room.id">
              <td class="fw-bold">{{ room.room_number }}</td>
              <td>Lantai {{ room.floor }}</td>
              <td>
                <span class="badge bg-secondary">
                  {{ getRoomTypeLabel(room.room_type) }}
                </span>
              </td>
              <td>{{ formatPrice(room.price) }}</td>
              <td>
                <span :class="getStatusBadgeClass(room.status)">
                  {{ getStatusLabel(room.status) }}
                </span>
              </td>
              <td>
                <span v-if="room.facilities && room.facilities.length > 0" class="small">
                  {{ room.facilities.slice(0, 2).join(', ') }}
                  <span v-if="room.facilities.length > 2">
                    +{{ room.facilities.length - 2 }} lainnya
                  </span>
                </span>
                <span v-else class="text-muted small">-</span>
              </td>
              <td class="text-center">
                <button class="btn btn-sm btn-warning me-1" @click="openEditModal(room)" title="Edit">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-danger" @click="confirmDelete(room)" title="Hapus">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div v-if="pagination.last_page > 1" class="d-flex justify-content-between align-items-center mt-3">
          <div class="text-muted">
            Menampilkan {{ pagination.from }} - {{ pagination.to }} dari {{ pagination.total }} data
          </div>
          <nav>
            <ul class="pagination mb-0">
              <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                <button class="page-link" @click="changePage(pagination.current_page - 1)">
                  Previous
                </button>
              </li>
              <li
                v-for="page in visiblePages"
                :key="page"
                class="page-item"
                :class="{ active: page === pagination.current_page }"
              >
                <button class="page-link" @click="changePage(page)">{{ page }}</button>
              </li>
              <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                <button class="page-link" @click="changePage(pagination.current_page + 1)">
                  Next
                </button>
              </li>
            </ul>
          </nav>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-5">
        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
        <p class="text-muted mt-3">Belum ada data kamar</p>
        <button class="btn btn-primary" @click="openAddModal">Tambah Kamar Pertama</button>
      </div>
    </div>

    <!-- Room Form Modal -->
    <RoomForm
      :modal-id="'roomModal'"
      :room-data="selectedRoom"
      @success="handleFormSuccess"
    />
  </div>
</template>

<script>
import api from '@/services/api';
import RoomForm from './RoomForm.vue';
import { Modal } from 'bootstrap';

export default {
  name: 'RoomList',
  components: {
    RoomForm
  },
  data() {
    return {
      rooms: [],
      loading: false,
      filters: {
        status: '',
        room_type: '',
        page: 1,
        per_page: 10
      },
      pagination: {
        current_page: 1,
        last_page: 1,
        from: 0,
        to: 0,
        total: 0
      },
      selectedRoom: null,
      modalInstance: null
    };
  },
  computed: {
    visiblePages() {
      const pages = [];
      const current = this.pagination.current_page;
      const last = this.pagination.last_page;
      
      for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
        pages.push(i);
      }
      
      return pages;
    }
  },
  mounted() {
    this.loadRooms();
  },
  methods: {
    async loadRooms() {
      this.loading = true;
      try {
        const response = await api.getRooms(this.filters);
        
        if (response.data.success) {
          this.rooms = response.data.data.data;
          this.pagination = {
            current_page: response.data.data.current_page,
            last_page: response.data.data.last_page,
            from: response.data.data.from,
            to: response.data.data.to,
            total: response.data.data.total
          };
        }
      } catch (error) {
        console.error('Failed to load rooms:', error);
        alert('Gagal memuat data kamar');
      } finally {
        this.loading = false;
      }
    },
    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.filters.page = page;
        this.loadRooms();
      }
    },
    openAddModal() {
      this.selectedRoom = null;
      const modalElement = document.getElementById('roomModal');
      if (modalElement) {
        this.modalInstance = new Modal(modalElement);
        this.modalInstance.show();
      }
    },
    openEditModal(room) {
      this.selectedRoom = { ...room };
      const modalElement = document.getElementById('roomModal');
      if (modalElement) {
        this.modalInstance = new Modal(modalElement);
        this.modalInstance.show();
      }
    },
    async confirmDelete(room) {
      if (confirm(`Apakah Anda yakin ingin menghapus kamar ${room.room_number}?`)) {
        try {
          const response = await api.deleteRoom(room.id);
          
          if (response.data.success) {
            alert('Kamar berhasil dihapus');
            this.loadRooms();
            this.$emit('room-deleted');
          }
        } catch (error) {
          console.error('Failed to delete room:', error);
          alert('Gagal menghapus kamar');
        }
      }
    },
    handleFormSuccess() {
      this.loadRooms();
      this.$emit('room-updated');
    },
    getRoomTypeLabel(type) {
      const labels = {
        single: 'Single',
        double: 'Double',
        shared: 'Shared'
      };
      return labels[type] || type;
    },
    getStatusLabel(status) {
      const labels = {
        available: 'Tersedia',
        occupied: 'Terisi',
        maintenance: 'Maintenance'
      };
      return labels[status] || status;
    },
    getStatusBadgeClass(status) {
      const classes = {
        available: 'badge bg-success',
        occupied: 'badge bg-danger',
        maintenance: 'badge bg-warning text-dark'
      };
      return classes[status] || 'badge bg-secondary';
    },
    formatPrice(price) {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
      }).format(price);
    }
  }
};
</script>

<style scoped>
.table th {
  font-weight: 600;
  white-space: nowrap;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
}
</style>