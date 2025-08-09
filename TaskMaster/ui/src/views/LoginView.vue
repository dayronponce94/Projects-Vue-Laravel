<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            <h2 class="text-center mb-0">Login</h2>
          </div>
          <div class="card-body">
            <form @submit.prevent="handleLogin">
              <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input 
                  type="email" 
                  class="form-control" 
                  id="email" 
                  v-model="loginData.email" 
                  required 
                  placeholder="Enter your email"
                />
              </div>
              
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input 
                  type="password" 
                  class="form-control" 
                  id="password" 
                  v-model="loginData.password" 
                  required 
                  placeholder="Enter your password"
                />
              </div>
              
              <button type="submit" class="btn btn-primary w-100">Login</button>
              
              <div class="mt-3 text-center">
                Don't have an account? <router-link to="/register">Register</router-link>
              </div>
            </form>
            
            <div v-if="message" class="alert mt-3" :class="isSuccess ? 'alert-success' : 'alert-danger'">
              {{ message }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { inject } from 'vue';

export default {
  setup() {
    const authStore = inject('authStore');
    return { authStore };
  },
  data() {
    return {
      loginData: {
        email: '',
        password: '',
      },
      message: '',
      isSuccess: false
    };
  },
  methods: {
    async handleLogin() {
      try {
        const response = await axios.post('http://127.0.0.1:8000/api/login', this.loginData);
        this.message = response.data.message;
        this.isSuccess = true;
        
        this.authStore.login(response.data.user, response.data.token);

        this.$router.push('/');
        
      } catch (error) {
        this.message = error.response?.data?.message || 'Login failed. Please try again.';
        this.isSuccess = false;
      }
    }
  }
};
</script>

<style scoped>
.card {
  border: none;
  border-radius: 10px;
}

.card-header {
  border-top-left-radius: 10px !important;
  border-top-right-radius: 10px !important;
}

.alert {
  border-radius: 8px;
}
</style>