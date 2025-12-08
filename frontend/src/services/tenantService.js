// services/tenantService.js
import api from './api';

const tenantService = {
  /**
   * Get all tenants with pagination and filters
   * @param {Object} params - Query parameters (page, per_page, search, status, room_id, etc)
   */
  getAll(params = {}) {
    return api.get('/tenants', { params });
  },

  /**
   * Get single tenant by ID
   * @param {Number} id - Tenant ID
   */
  getById(id) {
    return api.get(`/tenants/${id}`);
  },

  /**
   * Create new tenant
   * @param {Object} tenantData - Tenant data (with files)
   */
  create(tenantData) {
    const formData = new FormData();
    
    // Append all fields to FormData
    Object.keys(tenantData).forEach(key => {
      if (tenantData[key] !== null && tenantData[key] !== undefined) {
        if (key === 'id_card_photo' || key === 'tenant_photo') {
          // File upload
          if (tenantData[key] instanceof File) {
            formData.append(key, tenantData[key]);
          }
        } else {
          formData.append(key, tenantData[key]);
        }
      }
    });

    return api.post('/tenants', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },

  /**
   * Update tenant
   * @param {Number} id - Tenant ID
   * @param {Object} tenantData - Updated tenant data (with files)
   */
  update(id, tenantData) {
    const formData = new FormData();
    
    // Append all fields to FormData
    Object.keys(tenantData).forEach(key => {
      if (tenantData[key] !== null && tenantData[key] !== undefined) {
        if (key === 'id_card_photo' || key === 'tenant_photo') {
          // File upload
          if (tenantData[key] instanceof File) {
            formData.append(key, tenantData[key]);
          }
        } else {
          formData.append(key, tenantData[key]);
        }
      }
    });

    // Use POST with FormData for file uploads (Laravel/Lumen limitation)
    return api.post(`/tenants/${id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },

  /**
   * Delete tenant
   * @param {Number} id - Tenant ID
   */
  delete(id) {
    return api.delete(`/tenants/${id}`);
  },

  /**
   * Get tenant statistics
   */
  getStatistics() {
    return api.get('/tenants/statistics');
  },

  /**
   * Get tenants expiring soon (within 30 days)
   */
  getExpiringSoon() {
    return api.get('/tenants/expiring-soon');
  }
};

export default tenantService;