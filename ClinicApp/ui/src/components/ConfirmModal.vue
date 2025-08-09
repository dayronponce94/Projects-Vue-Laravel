<template>
  <div v-if="show" class="fixed inset-0 flex items-center justify-center z-50">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div class="bg-white rounded-lg shadow-lg p-6 z-10 max-w-md w-full">
      <h2 class="text-lg font-semibold mb-4">Confirm Action</h2>
      <p class="mb-6">{{ message }}</p>

      <div class="flex justify-end gap-3">
        <button
          @click="$emit('cancel')"
          class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition"
        >
          Cancel
        </button>
        <button
          @click="$emit('confirm')"
          class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition"
        >
          Confirm
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  show: Boolean,
  message: String
});
</script>
