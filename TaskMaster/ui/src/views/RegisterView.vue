<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            <h2 class="text-center mb-0">Create Account</h2>
          </div>
          <div class="card-body">
            <form @submit.prevent="handleRegister">
              <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input 
                  type="text" 
                  class="form-control" 
                  id="name" 
                  v-model="userData.name" 
                  required 
                  placeholder="Enter your name"
                />
              </div>
              
              <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input 
                  type="email" 
                  class="form-control" 
                  id="email" 
                  v-model="userData.email" 
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
                  v-model="userData.password" 
                  required 
                  placeholder="Create a password"
                />
              </div>
              
              <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input 
                  type="password" 
                  class="form-control" 
                  id="password_confirmation" 
                  v-model="userData.password_confirmation" 
                  required 
                  placeholder="Confirm your password"
                />
              </div>
              
              <button type="submit" class="btn btn-primary w-100">Sign Up</button>
              
              <div class="mt-3 text-center">
                Already have an account? <router-link to="/login">Log in</router-link>
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

export default {
  data() {
    return {
      userData: {
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
      },
      message: '',
      isSuccess: false
    };
  },
  methods: {
    async handleRegister() {
      try {
        const response = await axios.post('http://127.0.0.1:8000/api/register', this.userData);
        this.message = response.data.message;
        this.isSuccess = true;
        
        // Reset form on success
        this.userData = {
          name: '',
          email: '',
          password: '',
          password_confirmation: ''
        };
        
        // Redirect to login after 1.5 seconds
        setTimeout(() => {
          this.$router.push('/login');
        }, 1500);
        
      } catch (error) {
        this.message = error.response?.data?.message || 'Registration failed. Please try again.';
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