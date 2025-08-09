<template>
  <nav class="py-3 border-b border-gray-200 shadow-sm">
    <div class="container mx-auto flex items-center justify-between px-4">
      <div class="flex items-center space-x-6">
        <router-link to="/" class="text-xl font-bold text-primary">
          ClinicApp
        </router-link>
        <!--  Add doctors links -->
        <router-link
          v-if="user && user.role === 'doctor'"
          to="/schedule"
          class="text-gray-700 hover:text-primary transition-colors"
        >
          My Schedule
        </router-link>
        <router-link
          v-if="user && user.role === 'doctor'"
          to="/doctor/schedule/daily"
          class="text-gray-700 hover:text-primary transition-colors"
        >
          Daily Schedule
        </router-link>

      <!-- Add patient links -->
        <router-link
          v-if="user && user.role === 'patient'"
          to="/appointments"
          class="text-gray-700 hover:text-primary transition-colors"
        >
          Appointments
        </router-link>
        <router-link
          v-if="user && user.role === 'patient'"
          to="/find-doctors"
          class="text-gray-700 hover:text-primary transition-colors"
        >
          Find Doctors
        </router-link>

        <!-- Add admin link -->
        <router-link
          v-if="user && user.role === 'admin'"
          to="/admin/dashboard"
          class="text-gray-700 hover:text-primary transition-colors"
        >
          Admin Dashboard
        </router-link>
        <router-link 
          v-if="user"
          to="/profile" 
          class="text-gray-700 hover:text-primary transition-colors"
        >
          Profile
        </router-link>
      </div>
      
      <div class="flex items-center space-x-4">
        <template v-if="user">
          <NotificationBell />
          <span class="text-gray-700 hidden md:inline">Welcome, {{ user.name }}</span>
          <button 
            @click="handleLogout" 
            class="btn btn-danger px-3 py-1 text-sm"
          >
            Logout
          </button>
        </template>
        <template v-else>
          <router-link 
            to="/login" 
            class="text-primary hover:text-secondary transition-colors"
          >
            Login
          </router-link>
          <router-link 
            to="/register" 
            class="text-primary hover:text-secondary transition-colors"
          >
            Register
          </router-link>
        </template>
      </div>
    </div>
  </nav>
</template>

<script>
import { useAuthStore } from '@/stores/auth'
import NotificationBell from '@/components/NotificationBell.vue';

export default {
  components: {
    NotificationBell
  },
  setup() {
    const authStore = useAuthStore()
    return { authStore }
  },
  computed: {
    user() {
      return this.authStore.user
    }
  },
  methods: {
    async handleLogout() {
      try {
        await this.authStore.logout()
        this.$router.push('/login')
      } catch (error) {
        console.error('Logout failed:', error)
      }
    }
  }
}
</script>