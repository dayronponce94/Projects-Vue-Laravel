<template>
  <div class="max-w-md mx-auto py-10 px-4">
    <div class="card">
      <h2 class="text-3xl font-bold text-center text-secondary mb-6">Create Your Account</h2>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <div class="form-group">
          <label for="name">Full Name</label>
          <input id="name" v-model="form.name" type="text" placeholder="John Doe" required>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input id="email" v-model="form.email" type="email" placeholder="you@example.com" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input id="password" v-model="form.password" type="password" placeholder="••••••••" required>
        </div>

        <div class="form-group">
          <label for="password_confirmation">Confirm Password</label>
          <input id="password_confirmation" v-model="form.password_confirmation" type="password" placeholder="••••••••" required>
        </div>

        <div class="form-group">
          <label for="role">Role</label>
          <select id="role" v-model="form.role" @change="toggleFields" class="w-full">
            <option v-for="role in roles" :key="role" :value="role">
              {{ role.charAt(0).toUpperCase() + role.slice(1) }}
            </option>
          </select>
        </div>

        <div v-if="form.role === 'admin'" class="form-group">
          <label for="secret_code">Admin Code</label>
          <input id="secret_code" v-model="form.secret_code" type="password" required>
        </div>

        <div v-if="showSpecialty" class="form-group">
          <label for="specialty">Specialty</label>
          <input id="specialty" v-model="form.specialty" type="text" :required="form.role === 'doctor'">
        </div>

        <button type="submit" class="btn btn-primary w-full" :disabled="loading">
          {{ loading ? 'Registering...' : 'Register' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from '@/stores/auth'

export default {
  data() {
    return {
      roles: ['patient', 'doctor', 'admin'],
      form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role: 'patient',
        secret_code: '',
        specialty: ''
      },
      showSpecialty: false,
      showSecretCode: false,
      loading: false
    }
  },

  mounted() {
    this.toggleFields()
  },

  methods: {
    toggleFields() {
      this.showSpecialty = this.form.role === 'doctor'
      this.showSecretCode = this.form.role === 'admin'
    },

    async handleRegister() {
      this.loading = true
      const authStore = useAuthStore()

      try {
        await authStore.register({ ...this.form })
        this.$router.push('/')
      } catch (error) {
        let errorMessage = 'Registration failed'

        if (error.response) {
          errorMessage = error.response.data.message ||
            error.response.data.error ||
            `HTTP error ${error.response.status}`
        } else if (error.request) {
          errorMessage = 'No response from server'
        } else if (error.message) {
          errorMessage = error.message
        }

        alert(`Error: ${errorMessage}`)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
