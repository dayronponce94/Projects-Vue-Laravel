<template>
  <div id="app">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <router-link to="/" class="navbar-brand taskmaster-logo">TaskMaster</router-link>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
       <li class="nav-item" v-if="authState && authState.isAuthenticated">
              <span class="nav-link fw-bold">{{ authState.user.name }}</span>
        </li> 
            <li class="nav-item" v-if="authState && authState.isAuthenticated">
                <button class="btn btn-outline-danger btn-sm ms-2" @click="logout">
                  <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container mt-4">
      <router-view />
    </div>
    <footer class="footer mt-auto py-3 bg-light">
      <div class="container text-center">
        <span class="text-muted">TaskMaster © 2025</span>
      </div>
    </footer>
  </div>
</template>

<script>
import { inject } from 'vue';
import { useRouter } from 'vue-router';

export default {
  name: 'App',
  setup() {
    const router = useRouter();
    const authStore = inject('authStore');
    
    const logout = async () => {
      try {
        await authStore.logout();
      } finally {
        router.push('/');
      }
    };

    return { 
      authState: authStore ? authStore.getState() : null,
      logout
    };
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
  min-height: 100vh;
  background-color: #f8f9fa;
}

.navbar {
  box-shadow: 0 2px 4px rgba(0,0,0,.1);
}

.taskmaster-logo {
  color: #0d6efd !important; 
  font-weight: 700;
  font-size: 1.5rem;
  letter-spacing: -0.5px;
  text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
}

.taskmaster-logo:hover {
  color: #0a58ca !important; 
}
</style>