<template>
  <transition name="fade">
    <div
      v-if="visible"
      :class="[
        'fixed top-5 right-5 px-4 py-3 rounded-lg shadow-lg text-white z-50 max-w-sm',
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
      ]"
    >
      {{ message }}
    </div>
  </transition>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  message: String,
  type: { type: String, default: 'success' },
  duration: { type: Number, default: 3000 }
});

const visible = ref(false);

watch(
  () => props.message,
  (val) => {
    if (val) {
      visible.value = true;
      setTimeout(() => (visible.value = false), props.duration);
    }
  }
);
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
