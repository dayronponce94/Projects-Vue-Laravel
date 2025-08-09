<template>
  <div id="app" class="min-h-screen flex flex-col">
    <Navbar />
    <main class="px-4">
      <router-view />
    </main>
    <Footer />
  </div>
</template>

<script>
import Navbar from '@/components/layout/Navbar.vue';
import Footer from '@/components/layout/Footer.vue';

export default {
  components: {
    Navbar,
    Footer
  }
};
</script>