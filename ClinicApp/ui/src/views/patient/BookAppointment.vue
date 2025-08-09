<template>
  <div class="container mx-auto py-8">
     <div class="flex items-center mb-6">
      <button @click="$router.go(-1)" class="btn btn-outline mr-4">
        &larr; Back
      </button>
      <h1 class="text-2xl font-bold text-center">Book Appointment</h1>
    </div>
    <div class="max-w-md mx-auto card">
      <div class="text-center mb-6">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xl font-semibold uppercase">
          {{ getInitials(doctor.name) }}
        </div>
        <h2 class="text-xl font-semibold">{{ doctor.name }}</h2>
        <p class="text-gray-600">{{ doctor.specialty }}</p>
      </div>

      <div class="text-center mb-6">
        <p class="text-lg font-medium">
          {{ formatDate($route.query.date) }} at {{ formatTime($route.query.time) }}
        </p>
      </div>

      <button 
        @click="confirmBooking"
        :disabled="loading"
        class="btn btn-primary w-full mt-4"
      >
        {{ loading ? 'Booking...' : 'Confirm Appointment' }}
      </button>
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
      doctor: {},
      loading: false,
      toastMessage: '',
      toastType: 'success'
    };
  },
  async created() {
    await this.fetchDoctorDetails();
  },
  methods: {
    async fetchDoctorDetails() {
      try {
        const response = await api.get(`/users/${this.$route.params.doctorId}`);
        this.doctor = response.data;
      } catch (error) {
        this.handleError(error, 'fetching doctor details');
      }
    },
    getInitials(name) {
      if (!name) return '';
      return name
        .split(' ')
        .map(word => word[0])
        .join('')
        .slice(0, 2);
    },
    formatDate(dateString) {
      const [year, month, day] = dateString.split('-');
      const date = new Date(Date.UTC(year, month - 1, day));
      return date.toLocaleDateString('en-US', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        timeZone: 'UTC'
      });
    },
    formatTime(timeString) {
      const [hours, minutes] = timeString.split(':');
      const hour = parseInt(hours, 10);
      const period = hour >= 12 ? 'PM' : 'AM';
      const hour12 = hour % 12 || 12; 
      return `${hour12}:${minutes} ${period}`;
    },
    showToast(message, type = 'success') {
      this.toastMessage = '';
      this.$nextTick(() => {
        this.toastType = type;
        this.toastMessage = message;
      });
    },
    async confirmBooking() {
      try {
        this.loading = true;
        await api.post('/appointments/book', {
          doctor_id: this.doctor.id,
          schedule_id: this.$route.params.scheduleId
        });
        this.showToast('Appointment booked successfully!', 'success');
        
        setTimeout(() => {
          this.$router.push('/appointments');
        }, 1500);
      } catch (error) {
        this.handleError(error, 'booking appointment');
      } finally {
        this.loading = false;
      }
    },
    handleError(error, context) {
      console.error(`Error while ${context}:`, error);
      let errorMessage = 'Operation failed';
      if (error.response) {
        errorMessage = error.response.data.message || 
                      error.response.data.error ||
                      `HTTP error ${error.response.status}`;
      } else if (error.request) {
        errorMessage = 'No response from server';
      } else {
        errorMessage = error.message;
      }
      this.showToast(`Error: ${errorMessage}`, 'error');
    }
  }
};
</script>
