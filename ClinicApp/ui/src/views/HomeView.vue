<template>
  <div class="max-w-6xl mx-auto px-4 py-10">
    <div class="mb-10 text-center">
      <h1 class="text-4xl font-bold text-gray-800">Welcome to <span class="text-green-600">ClinicApp</span></h1>
      <p class="text-gray-600 mt-2 text-lg">Your personalized healthcare solution</p>
    </div>

    <div v-if="user" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="bg-white shadow-xl rounded-2xl p-6 lg:col-span-2">
        <div class="flex items-center">
          <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center mr-4">
            <span class="text-2xl font-bold text-green-600">{{ userInitials }}</span>
          </div>
          <div>
            <h2 class="text-2xl font-bold text-gray-800">Welcome, {{ user.name }}</h2>
            <p class="text-gray-600 text-sm">You are logged in as <span class="capitalize">{{ capitalizedRole }}</span></p>
          </div>
        </div>
        
        <div class="mt-8">
          <h3 class="text-lg font-semibold text-gray-700 mb-4">Quick Actions</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <router-link 
              v-if="user.role === 'patient'"
              to="/appointments"
              class="bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg p-4 flex items-center transition cursor-pointer"
            >
              <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
              </div>
              <span class="text-gray-800 font-medium">Book Appointment</span>
            </router-link>

            <router-link 
              v-if="user.role === 'doctor'"
              to="/schedule"
              class="bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg p-4 flex items-center transition cursor-pointer"
            >
              <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
              </div>
              <span class="text-gray-800 font-medium">View Schedule</span>
            </router-link>

            <router-link 
              v-if="user.role === 'admin'"
              to="/admin/doctors"
              class="bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg p-4 flex items-center transition cursor-pointer"
            >
              <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
              </div>
              <span class="text-gray-800 font-medium">Manage Doctors</span>
            </router-link>
          </div>
        </div>
      </div>
      
      <div class="bg-white shadow-xl rounded-2xl p-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Your Activity</h3>
        <div class="space-y-4">
          <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg shadow-sm">
            <span class="text-gray-700">Upcoming Appointments</span>
            <span class="font-bold text-green-600">2</span>
          </div>
          <!-- Más estadísticas si gustas -->
        </div>
      </div>
    </div>

    <div v-else class="max-w-md mx-auto bg-white shadow-xl rounded-2xl p-6 text-center">
      <h2 class="text-2xl font-semibold mb-2 text-gray-800">Get Started</h2>
      <p class="mb-6 text-gray-600">Login or create an account to access our services</p>
      <div class="flex space-x-4">
        <router-link to="/login" class="btn btn-primary flex-1">Login</router-link>
        <router-link to="/register" class="btn btn-outline flex-1">Register</router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      user: null,
    };
  },
  computed: {
    capitalizedRole() {
      if (!this.user?.role) return '';
      return this.user.role.charAt(0).toUpperCase() + this.user.role.slice(1);
    },
    userInitials() {
      if (!this.user?.name) return '';
      return this.user.name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase();
    },
  },
  created() {
    const storedUser = localStorage.getItem('user');
    if (storedUser) {
      this.user = JSON.parse(storedUser);
    }
  },
};
</script>
