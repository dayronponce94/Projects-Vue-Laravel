<template>
  <div class="max-w-5xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Today's Appointments</h1>

    <!-- Date filter -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center mb-8 gap-4">
      <input v-model="selectedDate" type="date" class="form-input border border-gray-300 rounded-lg px-4 py-2 shadow-sm w-full sm:max-w-xs" />
      <button @click="fetchAppointments" class="btn btn-primary px-6 py-2 shadow-md">
        Load Appointments
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-12 text-gray-500">
      <p>Loading appointments...</p>
    </div>

    <!-- No appointments -->
    <div v-else-if="appointments.length === 0" class="card text-center py-10">
      <p class="text-gray-600">No appointments for <strong>{{ formatDate(selectedDate) }}</strong></p>
    </div>

    <!-- Appointment list -->
    <div v-else class="space-y-6">
      <div v-for="appointment in appointments" :key="appointment.id" class="card p-6 shadow-sm rounded-xl border border-gray-200 bg-white">
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-lg font-semibold text-gray-800">{{ appointment.patient.name }}</h3>
            <div class="mt-1">
              <p class="text-gray-600">{{ appointment.patient.email }}</p>
              <p class="text-sm text-gray-500">Phone: {{ appointment.patient.phone || 'N/A' }}</p>
            </div>
          </div>
          <span :class="['badge rounded-full px-3 py-1 text-sm font-medium', statusClass(appointment.status)]">
            {{ appointment.status }}
          </span>
        </div>

        <div class="mt-4 flex items-center text-gray-700">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <span class="font-medium">
            {{ formatDateTime(appointment.appointment_date) }}
          </span>
        </div>

        <div class="mt-6 flex flex-wrap justify-end gap-3">
          <button 
            v-if="appointment.status === 'pending'" 
            @click="confirmAppointment(appointment)" 
            class="btn btn-success"
          >
            Confirm
          </button>
          <button 
            v-if="appointment.status !== 'canceled'" 
            @click="cancelAppointment(appointment)" 
            class="btn btn-danger"
          >
            Cancel
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
      selectedDate: new Date().toISOString().split('T')[0],
      appointments: [],
      loading: false,
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
        const response = await api.get('/doctor/appointments', {
          params: { date: this.selectedDate }
        });
        this.appointments = response.data;
      } catch (error) {
        console.error('Failed to fetch appointments:', error);
        this.showToast('Failed to load appointments', 'error');
      } finally {
        this.loading = false;
      }
    },
    async confirmAppointment(appointment) {
      try {
        await api.patch(`/appointments/${appointment.id}/confirm`);
        await this.fetchAppointments();
        this.showToast('Appointment confirmed successfully!', 'success');
      } catch (error) {
        console.error('Failed to confirm appointment:', error);
        this.showToast('Failed to confirm appointment', 'error');
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
    statusClass(status) {
      return {
        pending: 'bg-yellow-100 text-yellow-800 border border-yellow-300',
        confirmed: 'bg-green-100 text-green-800 border border-green-300',
        canceled: 'bg-red-100 text-red-800 border border-red-300'
      }[status] || 'bg-gray-200 text-gray-600';
    },
    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        timeZone: 'UTC'
      });
    },
    formatDateTime(dateTimeString) {
      const date = new Date(dateTimeString);
      return date.toLocaleString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
        timeZone: 'UTC'
      });
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
