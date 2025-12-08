// services/authService.js
import api from './api';

const authService = {
  /**
   * Register new user
   */
  register(data) {
    return api.post('/auth/register', data);
  },
  
  /**
   * Login user
   */
  login(data) {
    return api.post('/auth/login', data);
  },
  
  /**
   * Logout user
   */
  logout() {
    return api.post('/auth/logout');
  },
  
  /**
   * Get current authenticated user
   */
  getCurrentUser() {
    return api.get('/auth/me');
  },

  /**
   * Refresh token
   */
  refreshToken() {
    return api.post('/auth/refresh');
  },

  /**
   * Update profile
   */
  updateProfile(data) {
    return api.put('/auth/profile', data);
  },

  /**
   * Change password
   */
  changePassword(data) {
    return api.post('/auth/change-password', data);
  }
};

export default authService;