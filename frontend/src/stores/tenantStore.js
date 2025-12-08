// stores/tenantStore.js
import { defineStore } from 'pinia';
import tenantService from '@/services/tenantService';

export const useTenantStore = defineStore('tenant', {
  state: () => ({
    tenants: [],
    currentTenant: null,
    statistics: {
      total_tenants: 0,
      active_tenants: 0,
      moved_out_tenants: 0,
      pending_tenants: 0,
      monthly_revenue: 0,
      occupancy_rate: 0,
      available_rooms: 0
    },
    expiringSoon: [],
    pagination: {
      current_page: 1,
      per_page: 10,
      total: 0,
      last_page: 1
    },
    loading: false,
    error: null
  }),

  getters: {
    activeTenants: (state) => state.tenants.filter(t => t.status === 'active'),
    movedOutTenants: (state) => state.tenants.filter(t => t.status === 'moved_out'),
    pendingTenants: (state) => state.tenants.filter(t => t.status === 'pending'),
  },

  actions: {
    async fetchTenants(params = {}) {
      this.loading = true;
      this.error = null;
      try {
        const response = await tenantService.getAll(params);
        this.tenants = response.data.data;
        this.pagination = {
          current_page: response.data.current_page,
          per_page: response.data.per_page,
          total: response.data.total,
          last_page: response.data.last_page
        };
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch tenants';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchTenant(id) {
      this.loading = true;
      this.error = null;
      try {
        const response = await tenantService.getById(id);
        this.currentTenant = response.data.data;
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch tenant';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async createTenant(tenantData) {
      this.loading = true;
      this.error = null;
      try {
        const response = await tenantService.create(tenantData);
        this.tenants.unshift(response.data.data);
        await this.fetchStatistics(); // Update statistics
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to create tenant';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async updateTenant(id, tenantData) {
      this.loading = true;
      this.error = null;
      try {
        const response = await tenantService.update(id, tenantData);
        const index = this.tenants.findIndex(t => t.id === id);
        if (index !== -1) {
          this.tenants[index] = response.data.data;
        }
        if (this.currentTenant?.id === id) {
          this.currentTenant = response.data.data;
        }
        await this.fetchStatistics();
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update tenant';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async deleteTenant(id) {
      this.loading = true;
      this.error = null;
      try {
        await tenantService.delete(id);
        this.tenants = this.tenants.filter(t => t.id !== id);
        if (this.currentTenant?.id === id) {
          this.currentTenant = null;
        }
        await this.fetchStatistics();
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to delete tenant';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchStatistics() {
      try {
        const response = await tenantService.getStatistics();
        this.statistics = response.data.data;
        return response.data;
      } catch (error) {
        console.error('Failed to fetch statistics:', error);
      }
    },

    async fetchExpiringSoon() {
      try {
        const response = await tenantService.getExpiringSoon();
        this.expiringSoon = response.data.data;
        return response.data;
      } catch (error) {
        console.error('Failed to fetch expiring tenants:', error);
      }
    },

    clearError() {
      this.error = null;
    },

    clearCurrentTenant() {
      this.currentTenant = null;
    }
  }
});