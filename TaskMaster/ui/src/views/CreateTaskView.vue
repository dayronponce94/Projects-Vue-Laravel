<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            <h2 class="text-center mb-0">Create New Task</h2>
          </div>
          <div class="card-body">
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
              
              <button type="submit" class="btn btn-primary w-100">Create Task</button>
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
import api from '@/services/api';

export default {
  data() {
    return {
      task: {
        title: '',
        description: '',
        due_date: '',
        priority: 'medium'
      },
      message: '',
      isSuccess: false
    };
  },
  methods: {
    async handleSubmit() {
      try {
        const response = await api.post('/tasks', this.task);
        this.message = 'Task created successfully!';
        this.isSuccess = true;
        
        // Reset form after successful submission
        this.task = {
          title: '',
          description: '',
          due_date: '',
          priority: 'medium'
        };
        
        // Redirect to tasks list after 1.5 seconds
        setTimeout(() => {
          this.$router.push('/tasks');
        }, 1500);
        
      } catch (error) {
        this.message = error.response?.data?.message || 'Failed to create task. Please try again.';
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