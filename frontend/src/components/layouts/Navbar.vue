<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <router-link to="/dashboard" class="navbar-brand">Kost Management</router-link>

      <!-- Toggler untuk Mobile -->
      <button
        class="navbar-toggler"
        type="button"
        @click="toggleNavbar"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" :class="{ show: isNavbarOpen }" id="navbarNav">
        <!-- Menu Navigasi -->
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <router-link 
              to="/dashboard" 
              class="nav-link" 
              :class="{ active: $route.path === '/dashboard' }"
              @click="handleNavLinkClick"
            >
              Dashboard
            </router-link>
          </li>
          <li class="nav-item">
            <router-link 
              to="/rooms" 
              class="nav-link"
              :class="{ active: $route.path === '/rooms' }"
              @click="handleNavLinkClick"
            >
              Kamar
            </router-link>
          </li>
        </ul>

        <!-- User Menu - Dropdown (Desktop & Mobile) -->
        <ul class="navbar-nav ms-auto">
          <li class="nav-item dropdown">
            <a
              class="nav-link dropdown-toggle"
              href="#"
              role="button"
              @click.prevent="toggleUserDropdown"
              :aria-expanded="isDropdownOpen"
            >
              {{ user?.name || 'User' }}
            </a>

            <ul class="dropdown-menu dropdown-menu-end" :class="{ show: isDropdownOpen }">
              <li>
                <a class="dropdown-item" href="#" @click.prevent="goToProfile">
                  <span class="me-2">👤</span> Profile
                </a>
              </li>
              <li><hr class="dropdown-divider" /></li>
              <li>
                <a class="dropdown-item text-danger" href="#" @click.prevent="handleLogout">
                  <span class="me-2">🚪</span> Logout
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
import api from '@/services/api';

export default {
  name: 'Navbar',
  data() {
    return {
      user: null,
      isNavbarOpen: false,
      isDropdownOpen: false
    };
  },
  mounted() {
    this.loadUser();
    // Close dropdown when clicking outside
    document.addEventListener('click', this.handleClickOutside);
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside);
  },
  methods: {
    loadUser() {
      const userData = localStorage.getItem('user');
      if (userData) {
        try {
          this.user = JSON.parse(userData);
        } catch (error) {
          console.error('Failed to parse user data:', error);
          this.user = null;
        }
      }
    },
    toggleNavbar() {
      this.isNavbarOpen = !this.isNavbarOpen;
    },
    toggleUserDropdown(event) {
      event.stopPropagation();
      this.isDropdownOpen = !this.isDropdownOpen;
    },
    handleNavLinkClick() {
      // Close navbar when clicking nav links (mobile)
      this.isNavbarOpen = false;
      this.isDropdownOpen = false;
    },
    handleClickOutside(event) {
      // Close dropdown when clicking outside
      const dropdown = event.target.closest('.dropdown');
      if (!dropdown) {
        this.isDropdownOpen = false;
      }
    },
    goToProfile() {
      this.isNavbarOpen = false;
      this.isDropdownOpen = false;
      // Implementasi nanti jika ada halaman profile
      alert('Halaman Profile belum tersedia');
    },
    async handleLogout() {
      if (!confirm('Apakah Anda yakin ingin logout?')) {
        return;
      }

      this.isNavbarOpen = false;
      this.isDropdownOpen = false;

      try {
        await api.logout();
      } catch (error) {
        console.error('Logout error:', error);
      } finally {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        this.$router.push('/login');
      }
    }
  }
};
</script>

<style scoped>
.navbar-brand {
  font-weight: 600;
  font-size: 1.25rem;
}

.nav-link {
  font-weight: 500;
  transition: all 0.3s ease;
  padding: 0.5rem 1rem;
}

.nav-link:hover {
  opacity: 0.8;
}

.nav-link.active {
  font-weight: 600;
  background-color: rgba(255, 255, 255, 0.15);
  border-radius: 5px;
}

/* Dropdown Styling */
.dropdown-toggle::after {
  margin-left: 0.5rem;
  transition: transform 0.3s ease;
}

/* Rotate arrow when dropdown open */
.dropdown[aria-expanded="true"] .dropdown-toggle::after {
  transform: rotate(180deg);
}

.dropdown-menu {
  margin-top: 0.5rem;
  border: none;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  display: block;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px);
  transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s;
}

.dropdown-menu.show {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.dropdown-item {
  padding: 0.75rem 1.25rem;
  transition: all 0.2s ease;
}

.dropdown-item:hover {
  background-color: rgba(13, 110, 253, 0.1);
  transform: translateX(5px);
}

.dropdown-divider {
  margin: 0.5rem 0;
}

/* Mobile Menu Styling */
@media (max-width: 991.98px) {
  /* Navbar Collapse Animation */
  .navbar-collapse {
    background-color: #0d6efd;
    padding: 0 1rem;
    margin-top: 0.5rem;
    border-radius: 8px;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease, padding 0.4s ease;
  }

  .navbar-collapse.show {
    max-height: 500px;
    padding: 1rem;
  }

  .navbar-nav {
    padding: 0.5rem 0;
  }
  
  .nav-item {
    margin: 0.25rem 0;
    opacity: 0;
    transform: translateX(-20px);
    animation: slideInLeft 0.3s ease forwards;
  }

  .nav-item:nth-child(1) {
    animation-delay: 0.1s;
  }

  .nav-item:nth-child(2) {
    animation-delay: 0.2s;
  }

  .nav-item:nth-child(3) {
    animation-delay: 0.3s;
  }

  @keyframes slideInLeft {
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  .nav-link {
    padding: 0.75rem 1rem;
    border-radius: 5px;
  }

  /* Dropdown di Mobile dengan Background Putih Transparan */
  .dropdown-menu {
    background-color: rgba(255, 255, 255, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    margin-top: 0.5rem;
    transform: translateY(0);
  }

  .dropdown-menu.show {
    animation: dropdownSlideDown 0.3s ease;
  }

  @keyframes dropdownSlideDown {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .dropdown-item {
    color: #0d6efd !important;
    font-weight: 500;
  }

  .dropdown-item:hover {
    background-color: rgba(13, 110, 253, 0.1);
  }

  .dropdown-item.text-danger {
    color: #dc3545 !important;
  }

  .dropdown-divider {
    border-color: rgba(0, 0, 0, 0.1);
  }

  /* User dropdown toggle styling di mobile */
  .dropdown .nav-link.dropdown-toggle {
    color: white;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 5px;
  }
}

/* Desktop Dropdown */
@media (min-width: 992px) {
  .dropdown-menu {
    background-color: white;
    min-width: 200px;
    right: -10px;
  }

  .dropdown-item {
    color: #333;
  }

  .dropdown-menu.show {
    animation: dropdownFadeIn 0.25s ease;
  }

  @keyframes dropdownFadeIn {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
}

/* Hamburger Icon Animation */
.navbar-toggler {
  border: none;
  transition: transform 0.3s ease;
}

.navbar-toggler:focus {
  box-shadow: none;
}

.navbar-toggler:hover {
  transform: scale(1.1);
}

/* Smooth transitions untuk semua interactive elements */
.navbar * {
  transition: all 0.2s ease;
}
</style>