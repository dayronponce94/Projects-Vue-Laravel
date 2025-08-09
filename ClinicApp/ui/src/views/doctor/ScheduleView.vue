<template>
  <div class="max-w-4xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Manage Your Schedule</h1>

    <!-- Add New Availability -->
    <div class="bg-white shadow-md rounded-2xl p-6 mb-8">
      <h2 class="text-xl font-semibold text-gray-700 mb-4 flex items-center gap-2">
        <span class="i-lucide-calendar-plus w-5 h-5 text-green-600"></span>
        Add New Availability
      </h2>

      <form @submit.prevent="addSchedule" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block mb-1 text-sm font-medium text-gray-600">Date</label>
          <input v-model="newSchedule.date" type="date" required class="input input-bordered w-full" />
        </div>
        <div>
          <label class="block mb-1 text-sm font-medium text-gray-600">Start Time</label>
          <input v-model="newSchedule.start_time" type="time" required class="input input-bordered w-full" />
        </div>
        <div>
          <label class="block mb-1 text-sm font-medium text-gray-600">End Time</label>
          <input v-model="newSchedule.end_time" type="time" required class="input input-bordered w-full" />
        </div>
        <div class="md:col-span-3 mt-4 text-right">
          <button type="submit" class="btn btn-success">Add Availability</button>
        </div>
      </form>
    </div>

    <!-- Existing Availability -->
    <div class="bg-white shadow-md rounded-2xl p-6">
      <h2 class="text-xl font-semibold text-gray-700 mb-4 flex items-center gap-2">
        <span class="i-lucide-calendar-range w-5 h-5 text-green-600"></span>
        Your Availability
      </h2>

      <div v-if="loading" class="text-center py-6 text-gray-500">
        Loading schedules...
      </div>

      <div v-else-if="schedules.length === 0" class="text-center py-6 text-gray-500">
        No availability slots added yet.
      </div>

      <div v-else class="divide-y divide-gray-200">
        <div
          v-for="schedule in schedules"
          :key="schedule.id"
          class="flex justify-between items-center py-4"
        >
          <div>
            <p class="font-medium text-gray-800">{{ formatDate(schedule.date) }}</p>
            <p class="text-gray-600 text-sm">{{ schedule.start_time }} - {{ schedule.end_time }}</p>
          </div>
          <button
            @click="openConfirm(schedule.id)"
            class="btn btn-danger btn-sm"
          >
            Delete
          </button>
        </div>
      </div>
    </div>
    <!-- Toast -->
    <Toast :message="toastMessage" :type="toastType" />

    <!-- Confirm Modal -->
    <ConfirmModal
      :show="confirmVisible"
      :message="confirmMessage"
      @cancel="confirmVisible = false"
      @confirm="confirmDelete"
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
      newSchedule: { date: '', start_time: '', end_time: '' },
      schedules: [],
      loading: true,
      toastMessage: '',
      toastType: 'success',
      confirmVisible: false,
      confirmMessage: '',
      scheduleToDelete: null
    };
  },
  async created() {
    await this.fetchSchedules();
  },
  methods: {
    formatDate(date) {
      if (!date) return '';
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(date).toLocaleDateString(undefined, options);
    },
    showToast(message, type = 'success') {
      this.toastMessage = '';
      this.$nextTick(() => {
        this.toastType = type;
        this.toastMessage = message;
      });
    },
    openConfirm(id) {
      this.scheduleToDelete = id;
      this.confirmMessage = 'Are you sure you want to delete this availability?';
      this.confirmVisible = true;
    },
    async fetchSchedules() {
      try {
        this.loading = true;
        const response = await api.get('/schedules');
        this.schedules = response.data;
      } catch (error) {
        this.showToast('Failed to load schedules', 'error');
      } finally {
        this.loading = false;
      }
    },
    async addSchedule() {
      try {
        await api.post('/schedules', this.newSchedule);
        this.newSchedule = { date: '', start_time: '', end_time: '' };
        await this.fetchSchedules();
        this.showToast('Availability added successfully!', 'success');
      } catch {
        this.showToast('Failed to add availability', 'error');
      }
    },
    async confirmDelete() {
      try {
        await api.delete(`/schedules/${this.scheduleToDelete}`);
        await this.fetchSchedules();
        this.showToast('Availability deleted successfully!', 'success');
      } catch {
        this.showToast('Failed to delete availability', 'error');
      } finally {
        this.confirmVisible = false;
      }
    }
  }
};
</script>
