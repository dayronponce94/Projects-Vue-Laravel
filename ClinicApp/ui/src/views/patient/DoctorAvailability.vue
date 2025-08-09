<template>
  <div class="max-w-4xl mx-auto py-10 px-4">
    <!-- Back & Title -->
    <div class="flex items-center mb-8">
      <button @click="$router.go(-1)" class="btn btn-outline mr-4">
        &larr; Back
      </button>
      <h1 class="text-3xl font-bold text-gray-800">Check Availability</h1>
    </div>

    <!-- Doctor Info Card -->
    <div class="card bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-8">
      <div class="flex items-center gap-4">
        <!-- Avatar with initials -->
        <div class="w-16 h-16 rounded-full bg-green-100 text-green-700 font-bold flex items-center justify-center text-xl border border-green-300">
          {{ getInitials(doctor.name) }}
        </div>
        <div>
          <h2 class="text-xl font-semibold text-gray-800">{{ doctor.name }}</h2>
          <p class="text-gray-500">{{ doctor.specialty }}</p>
        </div>
      </div>

      <!-- Date Picker -->
      <div class="form-group mt-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
        <input 
          v-model="selectedDate" 
          type="date" 
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500"
        />
      </div>

      <button 
        @click="fetchAvailability"
        :disabled="!selectedDate"
        class="btn btn-primary mt-4"
      >
        Check Availability
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center text-gray-500 py-12">
      <p>Loading availability...</p>
    </div>

    <!-- Available Time Slots -->
    <div 
      v-else-if="timeSlots.length > 0" 
      class="card bg-white border border-gray-200 rounded-xl shadow-sm p-6"
    >
      <h2 class="text-xl font-semibold mb-4 text-gray-800">
        Available Time Slots for {{ formatDate(selectedDate) }}
      </h2>
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        <button
          v-for="slot in timeSlots"
          :key="slot.id"
          @click="bookAppointment(slot)"
          class="btn-outline py-2 rounded-full text-sm font-medium transition"
        >
          {{ formatTime(slot.start_time) }}
        </button>
      </div>
    </div>

    <!-- No Availability -->
    <div 
      v-else-if="availabilityChecked" 
      class="card bg-white border border-gray-200 rounded-xl shadow-sm text-center py-12 text-gray-600"
    >
      <p>No available slots for <strong>{{ formatDate(selectedDate) }}</strong></p>
    </div>
  </div>
</template>

<script>
import api from '@/services/api';

export default {
  data() {
    return {
      doctor: {},
      selectedDate: '',
      timeSlots: [],
      loading: false,
      availabilityChecked: false
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
        this.handleError(error, 'Failed to load doctor');
      }
    },
    async fetchAvailability() {
      try {
        this.loading = true;
        this.timeSlots = [];
        const response = await api.get(`/doctors/${this.doctor.id}/availability`, {
          params: { date: this.selectedDate }
        });
        this.timeSlots = response.data;
        this.availabilityChecked = true;
      } catch (error) {
        this.handleError(error, 'Failed to load availability');
      } finally {
        this.loading = false;
      }
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
    formatTime(timeString) {
      return new Date(`1970-01-01T${timeString}`).toLocaleTimeString([], {
        hour: '2-digit', 
        minute: '2-digit'
      });
    },
    bookAppointment(slot) {
      this.$router.push({
        name: 'book-appointment',
        params: { 
          doctorId: this.doctor.id,
          scheduleId: slot.id
        },
        query: {
          date: this.selectedDate,
          time: slot.start_time
        }
      });
    },
    getInitials(name) {
      return name
        ? name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2)
        : '';
    },
    handleError(error, fallbackMessage) {
      console.error('Error:', error);
      let errorMessage = fallbackMessage;

      if (error.response) {
        errorMessage = error.response.data.message || error.response.data.error || `HTTP ${error.response.status}`;
      } else if (error.request) {
        errorMessage = 'No response from server';
      } else {
        errorMessage = error.message;
      }

      alert(`Error: ${errorMessage}`);
    }
  }
};
</script>

<style scoped>
.btn-outline {
  border: 1px solid #42b983;
  color: #42b983;
  background-color: white;
}
.btn-outline:hover {
  background-color: #42b983;
  color: white;
}
</style>
