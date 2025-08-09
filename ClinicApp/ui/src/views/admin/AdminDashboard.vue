<template>
  <div class="container mx-auto py-10">
    <h1 class="text-3xl font-bold mb-8 text-gray-800">Admin Dashboard</h1>

    <!-- Main statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
      <DashboardCard 
        title="Total Doctors" 
        :value="stats.doctors || 0" 
        :icon="LucideStethoscope" 
        icon-color="bg-blue-500"
      />
      <DashboardCard 
        title="Total Patients" 
        :value="stats.patients || 0" 
        :icon="LucideUsers" 
        icon-color="bg-emerald-500"
      />
      <DashboardCard 
        title="Total Appointments" 
        :value="stats.appointments || 0" 
        :icon="LucideCalendar" 
        icon-color="bg-purple-500"
      />
    </div>

    <!-- Statistics by specialty and doctor -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <div class="bg-white p-6 rounded-2xl shadow">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">Appointments by Specialty</h2>
        <div v-if="loadingSpecialties" class="text-center py-4">
          <p class="text-gray-500">Loading data...</p>
        </div>
        <ul v-else>
          <li 
            v-for="item in specialtyStats" 
            :key="item.specialty"
            class="flex justify-between py-2 border-b border-gray-100"
          >
            <span class="text-gray-600">{{ item.specialty }}</span>
            <span class="font-medium text-gray-800">{{ item.count }} appointments</span>
          </li>
        </ul>
      </div>

      <div class="bg-white p-6 rounded-2xl shadow">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">Appointments by Doctor</h2>
        <div v-if="loadingDoctors" class="text-center py-4">
          <p class="text-gray-500">Loading data...</p>
        </div>
        <ul v-else>
          <li 
            v-for="item in doctorStats" 
            :key="item.doctor_id"
            class="flex justify-between py-2 border-b border-gray-100"
          >
            <span class="text-gray-600">{{ item.doctor_name }}</span>
            <span class="font-medium text-gray-800">{{ item.count }} appointments</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import DashboardCard from '@/components/DashboardCard.vue'
import { ref, onMounted } from 'vue'
import api from '@/services/api'

// Icons
import { LucideStethoscope, LucideUsers, LucideCalendar } from 'lucide-vue-next'

const stats = ref({})
const specialtyStats = ref([])
const doctorStats = ref([])
const loadingSpecialties = ref(true)
const loadingDoctors = ref(true)

const fetchDashboardStats = async () => {
  try {
    const { data } = await api.get('/admin/stats')
    stats.value = data
  } catch (err) {
    console.error('Error fetching stats:', err)
  }
}

const fetchSpecialtyStats = async () => {
  loadingSpecialties.value = true
  try {
    const { data } = await api.get('/admin/stats/specialties')
    specialtyStats.value = data
  } catch (err) {
    console.error('Error fetching specialty stats:', err)
  } finally {
    loadingSpecialties.value = false
  }
}

const fetchDoctorStats = async () => {
  loadingDoctors.value = true
  try {
    const { data } = await api.get('/admin/stats/doctors')
    doctorStats.value = data
  } catch (err) {
    console.error('Error fetching doctor stats:', err)
  } finally {
    loadingDoctors.value = false
  }
}

onMounted(async () => {
  await fetchDashboardStats()
  await fetchSpecialtyStats()
  await fetchDoctorStats()
})
</script>
