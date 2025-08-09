<template>
  <div class="container mt-5">
    <div v-if="authState.isAuthenticated">
      <h1 class="display-4">Welcome, {{ authState.user.name }}</h1>
      <p class="lead">You are now logged in to TaskMaster.</p>
      
      <div class="card mt-4">
        <div class="card-body">
          <h5 class="card-title">Your Task Summary</h5>
          
          <div v-if="loadingSummary" class="text-center my-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
          
          <div v-else>
            <div class="d-flex justify-content-around text-center">
              <div>
                <h2 class="text-primary">{{ summary.total }}</h2>
                <p class="text-muted">Total Tasks</p>
              </div>
              <div>
                <h2 class="text-success">{{ summary.completed }}</h2>
                <p class="text-muted">Completed</p>
              </div>
              <div>
                <h2 class="text-warning">{{ summary.pending }}</h2>
                <p class="text-muted">Pending</p>
              </div>
            </div>
            
            <div class="mt-4">
              <h6>Priority Breakdown:</h6>
              <div class="progress mb-2" style="height: 25px">
                <div class="progress-bar bg-danger" :style="`width: ${priorityPercentage('high')}%`">
                  High: {{ summary.priority.high }}
                </div>
                <div class="progress-bar bg-warning" :style="`width: ${priorityPercentage('medium')}%`">
                  Medium: {{ summary.priority.medium }}
                </div>
                <div class="progress-bar bg-info" :style="`width: ${priorityPercentage('low')}%`">
                  Low: {{ summary.priority.low }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <hr class="my-4">
      <div class="d-flex justify-content-center gap-3">
        <router-link to="/tasks" class="btn btn-primary btn-lg">View All Tasks</router-link>
        <router-link to="/tasks/create" class="btn btn-outline-primary btn-lg">Create New Task</router-link>
      </div>
    </div>
    <div v-else class="jumbotron text-center">
      <h1 class="display-4">Welcome to TaskMaster</h1>
      <p class="lead">Simple task management for your team</p>
      <hr class="my-4">
      <p>Get started by creating an account or logging in</p>
      <div class="d-flex justify-content-center gap-3">
        <router-link to="/register" class="btn btn-primary btn-lg">Sign Up</router-link>
        <router-link to="/login" class="btn btn-outline-primary btn-lg">Login</router-link>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/services/api';
import { inject } from 'vue';

export default {
  name: 'HomeView',
  setup() {
    const authStore = inject('authStore');
    return { 
      authState: authStore.getState(),
      authStore
    };
  },
  data() {
    return {
      summary: {
        total: 0,
        completed: 0,
        pending: 0,
        priority: {
          high: 0,
          medium: 0,
          low: 0
        }
      },
      loadingSummary: true
    };
  },
  mounted() {
    if (this.authState.isAuthenticated) {
      this.fetchTaskSummary();
    }
  },
  methods: {
    async fetchTaskSummary() {
      try {
        
        const response = await api.get('/tasks', {
          params: {
            all: true 
          }
        });
        
        
        const tasks = response.data.data || response.data;
        
        this.summary.total = tasks.length;
        this.summary.completed = tasks.filter(t => t.completed).length;
        this.summary.pending = tasks.filter(t => !t.completed).length;
        
        this.summary.priority = {
          high: tasks.filter(t => t.priority === 'high').length,
          medium: tasks.filter(t => t.priority === 'medium').length,
          low: tasks.filter(t => t.priority === 'low').length
        };
        
      } catch (error) {
        console.error('Error fetching task summary:', error);
      } finally {
        this.loadingSummary = false;
      }
    },
    priorityPercentage(level) {
      if (this.summary.total === 0) return 0;
      return (this.summary.priority[level] / this.summary.total) * 100;
    }
  }
}
</script>

<style scoped>
.card {
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.progress-bar {
  font-size: 0.9rem;
  font-weight: 500;
}
</style>