<template>
  <div class="max-w-5xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Your Appointments</h1>

    <!-- Loading state -->
    <div v-if="loading" class="text-center py-12 text-gray-500">
      <p>Loading appointments...</p>
    </div>

    <!-- No appointments -->
    <div v-else-if="appointments.length === 0" class="card text-center py-12">
      <p class="text-gray-600">You don’t have any appointments yet.</p>
      <router-link to="/find-doctors" class="btn btn-primary mt-6 inline-block">
        Book an Appointment
      </router-link>
    </div>

    <!-- Appointments list -->
    <div v-else class="space-y-6">
      <div 
        v-for="appointment in appointments" 
        :key="appointment.id" 
        class="card p-6 shadow-sm rounded-xl border border-gray-200 bg-white"
      >
        <div class="flex items-start gap-4">
          <!-- Doctor avatar placeholder -->
          <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-xl font-semibold border border-gray-300">
            {{ getInitials(appointment.doctor.name) }}
          </div>

          <!-- Appointment details -->
          <div class="flex-1">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="text-lg font-semibold text-gray-800">{{ appointment.doctor.name }}</h3>
                <p class="text-sm text-gray-500">{{ appointment.doctor.specialty }}</p>
              </div>
              <span 
                class="text-xs font-medium px-3 py-1 rounded-full border"
                :class="statusBadge(appointment.status)"
              >
                {{ appointment.status }}
              </span>
            </div>

            <div class="mt-4 flex items-center text-sm text-gray-700">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span class="font-medium">
                {{ formatDateTime(appointment.appointment_date) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Cancel button -->
        <div class="mt-6 flex justify-end">
          <button 
            v-if="appointment.status === 'pending'" 
            @click="cancelAppointment(appointment)" 
            class="btn btn-danger"
          >
            Cancel Appointment
          </button>
        </div>
      </div>
    </div>
    <!-- Toast -->
    <div v-if="toastMessage" :class="[
      'fixed bottom-4 right-4 px-4 py-3 rounded-lg shadow-lg text-white transition transform',
      toastType === 'success' ? 'bg-green-500' : 'bg-red-500'
    ]">
      {{ toastMessage }}
    </div>

    <!-- Confirmation Modal -->
    <div v-if="confirmDialog.show" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 max-w-sm w-full">
        <p class="text-gray-800 mb-4">{{ confirmDialog.message }}</p>
        <div class="flex justify-end gap-3">
          <button @click="confirmDialog.show = false" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Cancel</button>
          <button @click="confirmDialog.onConfirm(); confirmDialog.show = false" class="px-4 py-2 rounded bg-red-500 text-white hover:bg-red-600">Yes</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/services/api';

export default {
  data() {
    return {
      appointments: [],
      loading: true,
      toastMessage: '',
      toastType: 'success',
      confirmDialog: {
        show: false,
        message: '',
        onConfirm: null
      }
    };
  },
  async created() {
    await this.fetchAppointments();
  },
  methods: {
     showToast(message, type = 'success') {
      this.toastMessage = '';
      this.$nextTick(() => {
        this.toastType = type;
        this.toastMessage = message;
      });
    },
    showConfirm(message, onConfirm) {
      this.confirmDialog.message = message;
      this.confirmDialog.onConfirm = onConfirm;
      this.confirmDialog.show = true;
    },
    async fetchAppointments() {
      try {
        this.loading = true;
        const response = await api.get('/appointments');
        this.appointments = response.data;
      } catch (error) {
        this.handleError(error, 'Failed to load appointments');
      } finally {
        this.loading = false;
      }
    },
    cancelAppointment(appointment) {
      this.showConfirm('Are you sure you want to cancel this appointment?', async () => {
        try {
          await api.patch(`/appointments/${appointment.id}/cancel`);
          await this.fetchAppointments();
          this.showToast('Appointment canceled successfully!', 'success');
        } catch (error) {
          console.error('Error canceling appointment:', error.response?.data || error);
          this.showToast('Failed to cancel appointment', 'error');
        }
      });
    },
    formatDateTime(dateTimeString) {
      const date = new Date(dateTimeString);
      return date.toLocaleString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        timeZone: 'UTC'
      });
    },
    statusBadge(status) {
      return {
        pending: 'bg-yellow-100 text-yellow-800 border-yellow-300',
        confirmed: 'bg-green-100 text-green-800 border-green-300',
        canceled: 'bg-red-100 text-red-800 border-red-300'
      }[status];
    },
    getInitials(name) {
      return name.split(' ').map(part => part[0]).join('').substring(0, 2).toUpperCase();
    },
    handleError(error, fallbackMessage) {
      console.error('Error:', error);
      let message = fallbackMessage;

      if (error.response) {
        message = error.response.data.message || error.response.data.error || `HTTP ${error.response.status}`;
      } else if (error.request) {
        message = 'No response from server';
      } else {
        message = error.message;
      }

      alert(`Error: ${message}`);
    }
  }
};
</script>
<style scoped>
.btn-success {
  @apply bg-green-500 text-white hover:bg-green-600 px-4 py-2 rounded;
}
.btn-danger {
  @apply bg-red-500 text-white hover:bg-red-600 px-4 py-2 rounded;
}
</style>
