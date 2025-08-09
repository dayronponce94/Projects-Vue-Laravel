import { defineStore } from 'pinia'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null
  }),
  actions: {
    async register(formData) {
      try {
        // Validate secret code for administrators
        if (formData.role === 'admin' && formData.secret_code !== 'ADMIN123') {
          throw new Error('Invalid admin secret code')
        }

        // Prepare payload according to the role
        const payload = {
          name: formData.name,
          email: formData.email,
          password: formData.password,
          password_confirmation: formData.password_confirmation,
          role: formData.role,
          specialty: formData.role === 'doctor' ? formData.specialty : null,
          ...(formData.role === 'admin' ? { secret_code: formData.secret_code } : {})
        }

        // Submit registration
        const response = await api.post('/register', payload)
        
        // Save user data
        this.user = response.data.user
        this.token = response.data.access_token
        
        localStorage.setItem('user', JSON.stringify(this.user))
        localStorage.setItem('token', this.token)
        
        return true
      } catch (error) {
        console.error('Registration failed:', error)
        throw error
      }
    },
    
    async login(credentials) {
      this.loading = true;
      this.error = null;
      
      try {
        const response = await api.post('/login', credentials);
        
        const { access_token, user } = response.data;
        
        // Save token and user
        localStorage.setItem('token', access_token);
        localStorage.setItem('user', JSON.stringify(user));
        
        this.user = user;
        return true;
        
      } catch (error) {
        console.error('Login failed:', error);
        
        // Handling different types of errors
        if (error.response?.status === 422) {
          this.error = 'Invalid email or password';
        } else if (error.response?.status === 401) {
          this.error = 'Unauthorized access';
        } else {
          this.error = 'Connection error. Please try again later.';
        }
        
        return false;
      } finally {
        this.loading = false;
      }
    },

    logout() {
      this.user = null
      this.token = null
      localStorage.removeItem('user')
      localStorage.removeItem('token')
    },
    
    async fetchUser() {
      if (!this.token) return
      
      try {
        const response = await api.get('/profile')
        this.user = response.data
        localStorage.setItem('user', JSON.stringify(this.user))
      } catch (error) {
        console.error('Failed to fetch user:', error)
        this.logout()
      }
    }
  }
})