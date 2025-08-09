<template>
  <div class="max-w-4xl mx-auto py-10 px-4">
    <div v-if="user" class="card">
      <div class="text-center mb-8">
        <div class="mx-auto w-28 h-28 rounded-full bg-primary/10 border-2 border-primary flex items-center justify-center text-primary text-3xl font-bold">
          {{ userInitials }}
        </div>
        <h2 class="text-3xl font-bold mt-4">{{ user.name }}</h2>
        <p class="text-gray-600 mt-1">{{ user.email }}</p>
        <div class="mt-3">
          <span class="bg-primary/10 text-primary px-4 py-1 rounded-full font-medium text-sm">
            {{ capitalizedRole }}
          </span>
          <p v-if="user.specialty" class="mt-2 text-gray-700">
            <span class="font-medium">Specialty:</span> {{ user.specialty }}
          </p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Personal Information-->
        <div class="border rounded-xl p-6 bg-gray-50">
          <h3 class="text-lg font-semibold text-gray-700 mb-4">Personal Information</h3>
          <p><span class="font-medium">Joined:</span> {{ formatDate(user.created_at) }}</p>
        </div>

        <!-- Settings Account -->
        <div class="border rounded-xl p-6 bg-gray-50">
          <h3 class="text-lg font-semibold text-gray-700 mb-4">Account Actions</h3>
          <button @click="showEditModal = true" class="btn btn-outline w-full mb-3">Edit Profile</button>
        </div>
      </div>

      <!-- Edit Profile Modal -->
      <div v-if="showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
          <h3 class="text-lg font-semibold mb-4">Edit Profile</h3>
          <form @submit.prevent="updateProfile">
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-name">Name</label>
              <input v-model="editForm.name" type="text" id="edit-name" class="input input-bordered w-full" required>
            </div>
            
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-email">Email</label>
              <input v-model="editForm.email" type="email" id="edit-email" class="input input-bordered w-full" required>
            </div>
            
            <div v-if="user.role === 'doctor'" class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-specialty">Specialty</label>
              <input v-model="editForm.specialty" type="text" id="edit-specialty" class="input input-bordered w-full">
            </div>
            
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-password">New Password (optional)</label>
              <input v-model="editForm.password" type="password" id="edit-password" class="input input-bordered w-full">
            </div>
            
            <div class="mb-4" v-if="editForm.password">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-password-confirm">Confirm Password</label>
              <input v-model="editForm.password_confirmation" type="password" id="edit-password-confirm" class="input input-bordered w-full">
            </div>
            
            <div class="flex justify-end">
              <button type="button" @click="showEditModal = false" class="btn btn-outline mr-2">Cancel</button>
              <button type="submit" class="btn btn-primary" :disabled="loadingUpdate">Save Changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div v-else class="text-center text-gray-500 py-20">
      <span class="loader mx-auto mb-4"></span>
      <p>Loading profile...</p>
    </div>
    <!-- Toast Notification -->
    <div v-if="toastMessage" :class="[
      'fixed bottom-4 right-4 px-4 py-3 rounded-lg shadow-lg text-white transition transform',
      toastType === 'success' ? 'bg-green-500' : 'bg-red-500'
    ]">
      {{ toastMessage }}
    </div>
  </div>
</template>

<script>
import api from '@/services/api';

export default {
  data() {
    return {
      user: null,
      showEditModal: false,
      loadingUpdate: false,
      toastMessage: '',
      toastType: 'success',
      editForm: {
        name: '',
        email: '',
        specialty: '',
        password: '',
        password_confirmation: ''
      }
    };
  },
  computed: {
    capitalizedRole() {
      if (!this.user?.role) return '';
      return this.user.role.charAt(0).toUpperCase() + this.user.role.slice(1);
    },
    userInitials() {
      if (!this.user?.name) return 'U';
      return this.user.name
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase();
    }
  },
  methods: {
    async fetchProfile() {
      try {
        const response = await api.get('/profile');
        this.user = response.data;
      } catch (error) {
        console.error('Error:', error);
        alert(`Error: ${this.extractErrorMessage(error)}`);
      }
    },
    extractErrorMessage(error) {
      if (error.response) {
        return error.response.data.message || error.response.data.error || `HTTP error ${error.response.status}`;
      } else if (error.request) {
        return 'No response from server';
      } else {
        return error.message;
      }
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
     showToast(message, type = 'success') {
      this.toastMessage = '';
      this.$nextTick(() => {
        this.toastType = type;
        this.toastMessage = message;
      });
    },
    async updateProfile() {
      this.loadingUpdate = true;
      
      try {
        const dataToUpdate = { ...this.editForm };
        if (!dataToUpdate.password) {
          delete dataToUpdate.password;
          delete dataToUpdate.password_confirmation;
        }
        
        const response = await api.put('/profile', dataToUpdate);
        this.user = response.data;
        
        // Update localStorage
        localStorage.setItem('user', JSON.stringify(this.user));
        
        this.showEditModal = false;
        this.showToast('Profile updated successfully!', 'success');
      } catch (error) {
        console.error('Error updating profile:', error);
        this.showToast(`Error: ${this.extractErrorMessage(error)}`, 'error');
      } finally {
        this.loadingUpdate = false;
      }
    }
  },
  watch: {
    user: {
      immediate: true,
      handler(newUser) {
        if (newUser) {
          this.editForm = {
            name: newUser.name,
            email: newUser.email,
            specialty: newUser.specialty || '',
            password: '',
            password_confirmation: ''
          };
        }
      }
    }
  },
  async created() {
    await this.fetchProfile();
  }
};
</script>