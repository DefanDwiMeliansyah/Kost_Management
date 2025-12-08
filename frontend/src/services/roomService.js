// services/roomService.js
import api from './api';

const roomService = {
  /**
   * Get all rooms with pagination and filters
   * @param {Object} params - Query parameters (page, per_page, search, status, etc)
   */
  getAll(params = {}) {
    return api.get('/rooms', { params });
  },

  /**
   * Get single room by ID
   * @param {Number} id - Room ID
   */
  getById(id) {
    return api.get(`/rooms/${id}`);
  },

  /**
   * Create new room
   * @param {Object} data - Room data
   */
  create(data) {
    return api.post('/rooms', data);
  },

  /**
   * Update room
   * @param {Number} id - Room ID
   * @param {Object} data - Updated room data
   */
  update(id, data) {
    return api.put(`/rooms/${id}`, data);
  },

  /**
   * Delete room
   * @param {Number} id - Room ID
   */
  delete(id) {
    return api.delete(`/rooms/${id}`);
  },

  /**
   * Get room statistics
   */
  getStatistics() {
    return api.get('/rooms/statistics');
  },

  /**
   * Get available rooms
   */
  getAvailable(params = {}) {
    return api.get('/rooms', { 
      params: { ...params, status: 'available' } 
    });
  },

  /**
   * Upload room image
   * @param {Number} id - Room ID
   * @param {File} file - Image file
   */
  uploadImage(id, file) {
    const formData = new FormData();
    formData.append('image', file);
    
    return api.post(`/rooms/${id}/image`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  }
};

export default roomService;