<template>
  <div class="max-w-6xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Manage Doctors</h1>

    <div class="flex justify-between items-center mb-6">
      <button @click="openCreateModal" class="btn btn-success">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
        </svg>
        Add New Doctor
      </button>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="text-center py-12 text-gray-500">
      <p>Loading doctors...</p>
    </div>

    <!-- Doctors table -->
    <div v-else class="bg-white shadow-md rounded-xl overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specialty</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="doctor in doctors" :key="doctor.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-full flex items-center justify-center text-green-800 font-bold">
                  {{ doctor.name.charAt(0) }}
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">{{ doctor.name }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ doctor.email }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ doctor.specialty }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button @click="editDoctor(doctor)" class="text-green-600 hover:text-green-900 mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
              </button>
              <button @click="openConfirm(doctor)" class="text-red-600 hover:text-red-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-10">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-4">{{ currentDoctor ? 'Edit Doctor' : 'Add New Doctor' }}</h3>
        
        <form @submit.prevent="saveDoctor">
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name</label>
            <input v-model="form.name" type="text" id="name" class="input input-bordered w-full" required>
          </div>
          
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
            <input v-model="form.email" type="email" id="email" class="input input-bordered w-full" required>
          </div>
          
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="specialty">Specialty</label>
            <input v-model="form.specialty" type="text" id="specialty" class="input input-bordered w-full" required>
          </div>
          
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="password">{{ currentDoctor ? 'New Password (optional)' : 'Password' }}</label>
            <input v-model="form.password" type="password" id="password" class="input input-bordered w-full" :required="!currentDoctor">
          </div>
          
          <div class="mb-4" v-if="!currentDoctor">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="password_confirmation">Confirm Password</label>
            <input v-model="form.password_confirmation" type="password" id="password_confirmation" class="input input-bordered w-full" required>
          </div>
          
          <div class="flex justify-end">
            <button type="button" @click="closeModal" class="btn btn-outline mr-2">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Toast -->
    <Toast :message="toastMessage" :type="toastType" />

    <!-- Confirm Modal -->
    <ConfirmModal
      :show="confirmVisible"
      :message="confirmMessage"
      @cancel="confirmVisible = false"
      @confirm="deleteDoctor"
    />
  </div>
</template>

<script>
import api from '@/services/api';
import Toast from '@/components/Toast.vue';
import ConfirmModal from '@/components/ConfirmModal.vue';

export default {
  components: { Toast, ConfirmModal },
  data() {
    return {
      doctors: [],
      loading: true,
      showModal: false,
      currentDoctor: null,
      form: {
        name: '',
        email: '',
        specialty: '',
        password: '',
        password_confirmation: ''
      },
      toastMessage: '',
      toastType: 'success',
      confirmVisible: false,
      confirmMessage: '',
      doctorToDelete: null
    };
  },
  async created() {
    await this.fetchDoctors();
  },
  methods: {
    showToast(message, type = 'success') {
      this.toastMessage = '';
      this.$nextTick(() => {
        this.toastType = type;
        this.toastMessage = message;
      });
    },
    async fetchDoctors() {
      try {
        this.loading = true;
        const response = await api.get('/admin/doctors');
        this.doctors = response.data;
      } catch (error) {
        console.error('Error fetching doctors:', error);
        this.showToast('Failed to load doctors', 'error');
      } finally {
        this.loading = false;
      }
    },
    editDoctor(doctor) {
      this.currentDoctor = doctor;
      this.form = {
        name: doctor.name,
        email: doctor.email,
        specialty: doctor.specialty,
        password: '',
        password_confirmation: ''
      };
      this.showModal = true;
    },
    openConfirm(doctor) {
      this.doctorToDelete = doctor;
      this.confirmMessage = `Are you sure you want to delete ${doctor.name}?`;
      this.confirmVisible = true;
    },
    async deleteDoctor() {
      try {
        await api.delete(`/admin/doctors/${this.doctorToDelete.id}`);
        this.doctors = this.doctors.filter(d => d.id !== this.doctorToDelete.id);
        this.showToast('Doctor deleted successfully', 'success');
      } catch (error) {
        console.error('Error deleting doctor:', error);
        this.showToast('Failed to delete doctor', 'error');
      } finally {
        this.confirmVisible = false;
        this.doctorToDelete = null;
      }
    },
    openCreateModal() {
      this.currentDoctor = null;
      this.form = {
        name: '',
        email: '',
        specialty: '',
        password: '',
        password_confirmation: ''
      };
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
      this.currentDoctor = null;
      this.form = {
        name: '',
        email: '',
        specialty: '',
        password: '',
        password_confirmation: ''
      };
    },
    async saveDoctor() {
      try {
        if (this.currentDoctor) {
          // Update existing doctor
          const response = await api.put(`/admin/doctors/${this.currentDoctor.id}`, this.form);
          this.doctors = this.doctors.map(d =>
            d.id === this.currentDoctor.id ? response.data : d
          );
        } else {
          // Create new doctor
          const response = await api.post('/admin/doctors', this.form);
          this.doctors.push(response.data);
        }
        this.closeModal();
        this.showToast('Doctor saved successfully', 'success');
      } catch (error) {
        console.error('Error saving doctor:', error);
        this.showToast('Failed to save doctor', 'error');
      }
    }
  }
};
</script>