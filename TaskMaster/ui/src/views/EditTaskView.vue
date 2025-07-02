<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            <h2 class="text-center mb-0">Edit Task</h2>
          </div>
          
          <div class="card-body">
            <div v-if="loading" class="text-center">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
            <template v-else>
            <form @submit.prevent="handleSubmit">
              <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input 
                  type="text" 
                  class="form-control" 
                  id="title" 
                  v-model="task.title" 
                  required 
                  placeholder="Task title"
                />
              </div>
              
              <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea 
                  class="form-control" 
                  id="description" 
                  v-model="task.description" 
                  rows="3" 
                  placeholder="Task description"
                ></textarea>
              </div>
              
              <div class="mb-3">
                <label for="due_date" class="form-label">Due Date</label>
                <input 
                  type="date" 
                  class="form-control" 
                  id="due_date" 
                  v-model="task.due_date" 
                  required 
                />
              </div>
              
              <div class="mb-3">
                <label class="form-label">Priority</label>
                <div>
                  <div class="form-check form-check-inline">
                    <input 
                      class="form-check-input" 
                      type="radio" 
                      id="priority_high" 
                      value="high" 
                      v-model="task.priority"
                    >
                    <label class="form-check-label" for="priority_high">High</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input 
                      class="form-check-input" 
                      type="radio" 
                      id="priority_medium" 
                      value="medium" 
                      v-model="task.priority"
                    >
                    <label class="form-check-label" for="priority_medium">Medium</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input 
                      class="form-check-input" 
                      type="radio" 
                      id="priority_low" 
                      value="low" 
                      v-model="task.priority"
                    >
                    <label class="form-check-label" for="priority_low">Low</label>
                  </div>
                </div>
              </div>
            
              <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Update Task</button>
                <button type="button" class="btn btn-secondary" @click="goBack">Cancel</button>
              </div>
            </form>
            </template>
            <div v-if="message" class="alert mt-3" :class="isSuccess ? 'alert-success' : 'alert-danger'">
              {{ message }}
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="error" class="alert alert-danger">
        {{ error }}
        <button class="btn btn-link" @click="tryAgain">Try again</button>
    </div>
  </div>
</template>

<script>
import api from '@/services/api';

export default {
  data() {
    return {
      task: {
        id: null,
        title: '',
        description: '',
        due_date: '',
        priority: 'medium',
        completed: false
      },
      message: '',
      isSuccess: false,
      loading: true,
      error: null
    };
  },
  async created() {
    await this.fetchTask();
  },
  methods: {
    async fetchTask() {
      try {
        const taskId = this.$route.params.id;
        const response = await api.get(`/tasks/${taskId}`);

        // Convert the date to the YYYY-MM-DD format for the date input
        const taskData = response.data;
        taskData.due_date = taskData.due_date ? taskData.due_date.split('T')[0] : '';
        
        this.task = taskData;
        this.loading = false;

      } catch (error) {
        console.error('Error fetching task:', error);
        this.error = 'Failed to load task. Please try again.';
        this.loading = false;
      }
    },
    async handleSubmit() {
      try {
        await api.put(`/tasks/${this.task.id}`, this.task);
        this.message = 'Task updated successfully!';
        this.isSuccess = true;
        
        setTimeout(() => {
          this.$router.push('/tasks');
        }, 1500);
        
      } catch (error) {
        this.message = error.response?.data?.message || 'Failed to update task. Please try again.';
        this.isSuccess = false;
      }
    },
     goBack() {
      this.$router.push('/tasks');
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