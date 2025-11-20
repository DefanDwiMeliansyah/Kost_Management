import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
  },
});

// Request interceptor
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor
api.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    if (error.response && error.response.status === 401) {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default {
  // Auth endpoints
  register(data) {
    return api.post('/auth/register', data);
  },
  
  login(data) {
    return api.post('/auth/login', data);
  },
  
  logout() {
    return api.post('/auth/logout');
  },
  
  getCurrentUser() {
    return api.get('/auth/me');
  },

  // Rooms endpoints
  getRooms(params) {
    return api.get('/rooms', { params });
  },

  getRoom(id) {
    return api.get(`/rooms/${id}`);
  },

  createRoom(data) {
    return api.post('/rooms', data);
  },

  updateRoom(id, data) {
    return api.put(`/rooms/${id}`, data);
  },

  deleteRoom(id) {
    return api.delete(`/rooms/${id}`);
  },

  getRoomStatistics() {
    return api.get('/rooms/statistics');
  },
};