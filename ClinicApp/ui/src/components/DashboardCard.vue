<template>
  <div class="bg-white shadow rounded-2xl p-6 flex items-center gap-4 hover:shadow-md transition">
    <component :is="icon" class="w-8 h-8 text-white p-1.5 rounded-full" :class="iconColor" />
    <div>
      <p class="text-sm text-gray-500">{{ title }}</p>
      <p class="text-2xl font-bold">{{ value }}</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  value: [String, Number],
  icon: {
    type: Function, 
    required: true
  },
  iconColor: {
    type: String,
    default: 'bg-green-500'
  }
})
</script>
