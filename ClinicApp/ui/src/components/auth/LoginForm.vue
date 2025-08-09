<template>
  <div class="max-w-md mx-auto py-10 px-4">
    <div class="card">
      <h2 class="text-3xl font-bold text-center text-secondary mb-6">Welcome Back</h2>
      <div v-if="showError" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start">
        <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
          <h3 class="text-sm font-medium text-red-800">Invalid credentials</h3>
          <p class="mt-1 text-sm text-red-700">Please check your email and password and try again.</p>
        </div>
        <button @click="showError = false" class="ml-auto text-red-500 hover:text-red-700">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      <form @submit.prevent="handleLogin" class="space-y-4">
        <div class="form-group">
          <label for="email">Email</label>
          <input id="email" v-model="form.email" type="email" placeholder="you@example.com" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input id="password" v-model="form.password" type="password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-primary w-full">Login</button>
      </form>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from '@/stores/auth'

export default {
  data() {
    return {
      form: {
        email: '',
        password: ''
      },
      loading: false,
      showError: false
    }
  },
  methods: {
    async handleLogin() {
      this.loading = true;
      this.showError = false;
      
      const authStore = useAuthStore();
      
      try {
        const success = await authStore.login(this.form);
        
        if (success) {
          this.$router.push('/');
        } else {
          this.showError = true;
        }
      } catch (error) {
        this.showError = true;
        console.error('Login error:', error);
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

