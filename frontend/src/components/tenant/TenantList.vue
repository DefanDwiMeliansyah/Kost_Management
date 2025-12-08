<template>
  <div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="mb-1">Tenant Management</h2>
        <p class="text-muted mb-0">Manage your property tenants</p>
      </div>
      <button class="btn btn-primary" @click="showCreateModal">
        <i class="bi bi-plus-circle me-2"></i>Add New Tenant
      </button>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                  <i class="bi bi-people-fill text-primary fs-4"></i>
                </div>
              </div>
              <div class="flex-grow-1 ms-3">
                <h6 class="text-muted mb-1">Total Tenants</h6>
                <h3 class="mb-0">{{ statistics.total_tenants }}</h3>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <div class="bg-success bg-opacity-10 rounded-3 p-3">
                  <i class="bi bi-check-circle-fill text-success fs-4"></i>
                </div>
              </div>
              <div class="flex-grow-1 ms-3">
                <h6 class="text-muted mb-1">Active</h6>
                <h3 class="mb-0">{{ statistics.active_tenants }}</h3>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <div class="bg-info bg-opacity-10 rounded-3 p-3">
                  <i class="bi bi-door-open-fill text-info fs-4"></i>
                </div>
              </div>
              <div class="flex-grow-1 ms-3">
                <h6 class="text-muted mb-1">Available Rooms</h6>
                <h3 class="mb-0">{{ statistics.available_rooms }}</h3>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                  <i class="bi bi-cash-stack text-warning fs-4"></i>
                </div>
              </div>
              <div class="flex-grow-1 ms-3">
                <h6 class="text-muted mb-1">Monthly Revenue</h6>
                <h3 class="mb-0">Rp {{ formatCurrency(statistics.monthly_revenue) }}</h3>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters & Search -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search"></i>
              </span>
              <input
                type="text"
                class="form-control border-start-0 ps-0"
                placeholder="Search by name, ID card, phone..."
                v-model="filters.search"
                @input="debouncedSearch"
              />
            </div>
          </div>
          <div class="col-md-3">
            <select class="form-select" v-model="filters.status" @change="fetchTenants">
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="pending">Pending</option>
              <option value="moved_out">Moved Out</option>
            </select>
          </div>
          <div class="col-md-3">
            <select class="form-select" v-model="filters.room_id" @change="fetchTenants">
              <option value="">All Rooms</option>
              <option v-for="room in availableRooms" :key="room.id" :value="room.id">
                Room {{ room.room_number }} - Floor {{ room.floor }}
              </option>
            </select>
          </div>
          <div class="col-md-2">
            <button class="btn btn-outline-secondary w-100" @click="resetFilters">
              <i class="bi bi-arrow-clockwise me-2"></i>Reset
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Tenants Table -->
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>

        <div v-else-if="tenants.length === 0" class="text-center py-5">
          <i class="bi bi-inbox fs-1 text-muted"></i>
          <p class="text-muted mt-3">No tenants found</p>
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Tenant</th>
                <th>Room</th>
                <th>Contact</th>
                <th>Check In</th>
                <th>Monthly Rent</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="tenant in tenants" :key="tenant.id">
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar-circle me-3">
                      <img
                        v-if="tenant.tenant_photo"
                        :src="getImageUrl(tenant.tenant_photo)"
                        alt="Tenant"
                        class="rounded-circle"
                        width="40"
                        height="40"
                      />
                      <div v-else class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-person-fill text-primary"></i>
                      </div>
                    </div>
                    <div>
                      <div class="fw-semibold">{{ tenant.full_name }}</div>
                      <small class="text-muted">{{ tenant.id_card_number }}</small>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge bg-light text-dark">
                    <i class="bi bi-door-closed me-1"></i>
                    Room {{ tenant.room ? tenant.room.room_number : '-' }}
                  </span>
                  <br>
                  <small class="text-muted">Floor {{ tenant.room ? tenant.room.floor : '-' }}</small>
                </td>
                <td>
                  <div>
                    <i class="bi bi-telephone me-1"></i>{{ tenant.phone_number }}
                  </div>
                  <small class="text-muted" v-if="tenant.email">
                    <i class="bi bi-envelope me-1"></i>{{ tenant.email }}
                  </small>
                </td>
                <td>{{ formatDate(tenant.check_in_date) }}</td>
                <td class="fw-semibold">Rp {{ formatCurrency(tenant.monthly_rent) }}</td>
                <td>
                  <span :class="getStatusClass(tenant.status)">
                    {{ getStatusText(tenant.status) }}
                  </span>
                </td>
                <td>
                  <div class="btn-group">
                    <button
                      class="btn btn-sm btn-outline-primary"
                      @click="viewTenant(tenant)"
                      title="View Details"
                    >
                      <i class="bi bi-eye"></i>
                    </button>
                    <button
                      class="btn btn-sm btn-outline-warning"
                      @click="editTenant(tenant)"
                      title="Edit"
                    >
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button
                      class="btn btn-sm btn-outline-danger"
                      @click="confirmDelete(tenant)"
                      title="Delete"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4" v-if="pagination.last_page > 1">
          <div class="text-muted">
            Showing {{ ((pagination.current_page - 1) * pagination.per_page) + 1 }} to 
            {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} of 
            {{ pagination.total }} entries
          </div>
          <nav>
            <ul class="pagination mb-0">
              <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">
                  Previous
                </a>
              </li>
              <li
                v-for="page in visiblePages"
                :key="page"
                class="page-item"
                :class="{ active: page === pagination.current_page }"
              >
                <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
              </li>
              <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">
                  Next
                </a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>

    <!-- Modals akan di handle terpisah -->
  </div>
</template>

<script>
import { mapState, mapActions } from 'pinia';
import { useTenantStore } from '@/stores/tenantStore';
import { useRoomStore } from '@/stores/roomStore';

export default {
  name: 'TenantList',
  
  data() {
    return {
      filters: {
        search: '',
        status: '',
        room_id: '',
        per_page: 10
      },
      searchTimeout: null
    };
  },

  computed: {
    ...mapState(useTenantStore, ['tenants', 'statistics', 'pagination', 'loading']),
    ...mapState(useRoomStore, { availableRooms: 'rooms' }),

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

  methods: {
    ...mapActions(useTenantStore, ['fetchTenants', 'fetchStatistics', 'deleteTenant']),
    ...mapActions(useRoomStore, { fetchRooms: 'fetchRooms' }),

    debouncedSearch() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.fetchTenants(this.filters);
      }, 500);
    },

    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.filters.page = page;
        this.fetchTenants(this.filters);
      }
    },

    resetFilters() {
      this.filters = {
        search: '',
        status: '',
        room_id: '',
        per_page: 10
      };
      this.fetchTenants(this.filters);
    },

    showCreateModal() {
      // Navigate to create page or show modal
      this.$router.push('/tenants/create');
    },

    editTenant(tenant) {
      // Navigate to edit page
      this.$router.push(`/tenants/${tenant.id}/edit`);
    },

    viewTenant(tenant) {
      // Navigate to detail page
      this.$router.push(`/tenants/${tenant.id}`);
    },

    confirmDelete(tenant) {
      if (confirm(`Are you sure you want to delete tenant ${tenant.full_name}?`)) {
        this.handleDelete(tenant.id);
      }
    },

    async handleDelete(id) {
      try {
        await this.deleteTenant(id);
        this.fetchTenants(this.filters);
      } catch (error) {
        console.error('Error deleting tenant:', error);
        alert('Failed to delete tenant');
      }
    },

    formatCurrency(value) {
      return new Intl.NumberFormat('id-ID').format(value || 0);
    },

    formatDate(date) {
      if (!date) return '-';
      return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    },

    getImageUrl(path) {
      return `http://localhost:8000/storage/${path}`;
    },

    getStatusClass(status) {
      const classes = {
        active: 'badge bg-success',
        pending: 'badge bg-warning',
        moved_out: 'badge bg-secondary'
      };
      return classes[status] || 'badge bg-secondary';
    },

    getStatusText(status) {
      const texts = {
        active: 'Active',
        pending: 'Pending',
        moved_out: 'Moved Out'
      };
      return texts[status] || status;
    }
  },

  async mounted() {
    await Promise.all([
      this.fetchStatistics(),
      this.fetchTenants(this.filters),
      this.fetchRooms()
    ]);
  }
};
</script>

<style scoped>
.card {
  transition: all 0.3s ease;
}

.card:hover {
  transform: translateY(-2px);
}

.table-hover tbody tr:hover {
  background-color: rgba(0, 0, 0, 0.02);
}

.btn-group .btn {
  padding: 0.25rem 0.5rem;
}

.avatar-circle img {
  object-fit: cover;
}
</style>