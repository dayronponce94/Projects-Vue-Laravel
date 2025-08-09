<template>
  <div class="max-w-6xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Find a Doctor</h1>

    <!-- Select Specialty -->
    <div class="card p-6 mb-8 bg-white border border-gray-200 rounded-xl shadow-sm">
      <div class="form-group">
        <label class="block text-sm font-medium text-gray-700 mb-2">Select Specialty</label>
        <select 
          v-model="specialty" 
          @change="fetchDoctors" 
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500"
        >
          <option value="">-- Choose a specialty --</option>
          <option v-for="spec in specialties" :key="spec" :value="spec">
            {{ spec }}
          </option>
        </select>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loadingDoctors" class="text-center text-gray-500 py-12">
      <p>Loading doctors...</p>
    </div>

    <!-- No results -->
    <div v-else-if="doctors.length === 0 && specialty" class="card text-center py-12 text-gray-600">
      <p>No doctors found for "<strong>{{ specialty }}</strong>"</p>
    </div>

    <!-- Doctors grid -->
    <div v-else-if="doctors.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="doctor in doctors" 
        :key="doctor.id" 
        class="card bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition cursor-pointer"
        @click="selectDoctor(doctor)"
      >
        <div class="flex items-center gap-4">
          <!-- Avatar with initials -->
          <div class="w-14 h-14 rounded-full bg-green-100 text-green-700 font-bold flex items-center justify-center text-lg border border-green-300">
            {{ getInitials(doctor.name) }}
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-800">{{ doctor.name }}</h3>
            <p class="text-sm text-gray-500">{{ doctor.specialty }}</p>
          </div>
        </div>

        <button 
          @click.stop="selectDoctor(doctor)"
          class="btn btn-primary mt-6 w-full"
        >
          View Availability
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/services/api';

export default {
  data() {
    return {
      specialty: '',
      specialties: [
        'Cardiology', 'Dermatology', 'Endocrinology', 
        'Gastroenterology', 'Neurology', 'Oncology',
        'Pediatrics', 'Psychiatry', 'Rheumatology'
      ],
      doctors: [],
      loadingDoctors: false
    };
  },
  methods: {
    async fetchDoctors() {
      if (!this.specialty) return;

      try {
        this.loadingDoctors = true;
        const response = await api.get('/doctors', {
          params: { specialty: this.specialty }
        });
        this.doctors = response.data;
      } catch (error) {
        this.handleError(error, 'Failed to load doctors');
      } finally {
        this.loadingDoctors = false;
      }
    },
    selectDoctor(doctor) {
      this.$router.push({
        name: 'doctor-availability',
        params: { doctorId: doctor.id }
      });
    },
    getInitials(name) {
      return name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2);
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
