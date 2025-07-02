<template>
  <div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Your Tasks</h1>
      <router-link to="/tasks/create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Task
      </router-link>
    </div>

    <div class="mb-4">
      <div class="btn-group" role="group">
       <button 
          type="button" 
          class="btn" 
          :class="filter === 'all' ? 'btn-primary' : 'btn-outline-primary'"
          @click="changeFilter('all')"
        >
          All
        </button>
        <button 
          type="button" 
          class="btn" 
          :class="filter === 'active' ? 'btn-primary' : 'btn-outline-primary'"
          @click="changeFilter('active')"
        >
          Active
        </button>
        <button 
          type="button" 
          class="btn" 
          :class="filter === 'completed' ? 'btn-primary' : 'btn-outline-primary'"
          @click="changeFilter('completed')"
        >
          Completed
        </button>
      </div>
    </div>

    <div v-if="loading" class="text-center">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div v-else>
      <div v-if="tasks.length === 0" class="alert alert-info">
        You don't have any tasks yet. Create your first task!
      </div>

      <div v-else>
        <div class="card mb-3" v-for="task in filteredTasks" :key="task.id">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <h5 class="card-title">
                  <input 
                    type="checkbox" 
                    class="form-check-input me-2" 
                    :checked="task.completed"
                    @change="toggleTaskCompletion(task)"
                  >
                  <span :class="{ 'text-decoration-line-through text-muted': task.completed }">
                    {{ task.title }}
                  </span>
                </h5>
                <p class="card-text" :class="{ 'text-muted': task.completed }">{{ task.description }}</p>
                <div class="d-flex gap-2 align-items-center">
                  <span class="badge" :class="priorityClass(task.priority)">
                    {{ formatPriority(task.priority) }}
                  </span>
                  <span :class="{'text-danger': isOverdue(task), 'text-muted': task.completed}">
                    <i class="bi bi-calendar-event me-1"></i> {{ formatDate(task.due_date) }}
                  </span>
                  <span v-if="task.completed" class="badge bg-success">
                    <i class="bi bi-check-circle"></i> Completed
                  </span>
                  <span v-else-if="isOverdue(task)" class="badge bg-danger">
                    <i class="bi bi-exclamation-circle"></i> Overdue
                  </span>
                </div>
              </div>
              <div class="btn-group">
                <router-link :to="`/tasks/edit/${task.id}`" class="btn btn-sm btn-outline-secondary">
                  <i class="bi bi-pencil"></i> Edit
                </router-link>
                <button class="btn btn-sm btn-outline-danger" @click="deleteTask(task.id)">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        <nav v-if="pagination.last_page > 1" class="mt-4"> 
          <ul class="pagination justify-content-center">
            <li 
              class="page-item" 
              :class="{ disabled: pagination.current_page === 1 }"
            >
              <button class="page-link" @click="changePage(pagination.current_page - 1)">
                Previous
              </button>
            </li>

            <li 
              v-for="page in pagination.last_page" 
              :key="page" 
              class="page-item" 
              :class="{ active: page === pagination.current_page }"
            >
              <button class="page-link" @click="changePage(page)">
                {{ page }}
              </button>
            </li>

            <li 
              class="page-item" 
              :class="{ disabled: pagination.current_page === pagination.last_page }"
            >
              <button class="page-link" @click="changePage(pagination.current_page + 1)">
                Next
              </button>
            </li>
          </ul>
        </nav>
      </div>
    </div>
    <ConfirmationModal 
      :show="showDeleteModal"
      title="Delete Task" 
      message="Are you sure you want to delete this task? This action cannot be undone."
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
  </div>
</template>

<script>
import api from '@/services/api';
import ConfirmationModal from '@/components/ConfirmationModal.vue';
import { inject } from 'vue';

export default {
  components: { ConfirmationModal },
  data() {
    return {
      tasks: [],
      loading: true,
      filter: 'all',
      showDeleteModal: false,
      taskToDelete: null,
      pagination: {
      current_page: 1,
      last_page: 1
      }
    }; 
  },
  watch: {
    'authState.isAuthenticated'(newVal) {
      if (!newVal) {
        this.$router.push('/');
      }
    }
  },
   computed: {
    filteredTasks() {
      if (this.filter === 'active') {
        return this.tasks.filter(task => !task.completed);
      } else if (this.filter === 'completed') {
        return this.tasks.filter(task => task.completed);
      }
      return this.tasks;
    }
  },
  setup() {
    const authStore = inject('authStore');
    return { 
      authState: authStore.getState()
    };
  },
  methods: {
    async fetchTasks(page = 1) {
      this.loading = true;
      try {
        const params = {
          page: page,
          filter: this.filter !== 'all' ? this.filter : undefined
        };
        
        const response = await api.get('/tasks', { params });
        this.tasks = response.data.data;
        this.pagination = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          total: response.data.total
        };
      } catch (error) {
        console.error('Error fetching tasks:', error);
      } finally {
        this.loading = false;
      }
    },
    
    changeFilter(newFilter) {
      this.filter = newFilter;
      this.fetchTasks(1); 
    },

    formatDate(dateString) {
      if (!dateString) return 'No due date';
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
      });
    },

    formatPriority(priority) {
      const priorityMap = {
        high: 'High',
        medium: 'Medium',
        low: 'Low'
      };
      return priorityMap[priority] || priority;
    },

    priorityClass(priority) {
      return {
        'bg-danger': priority === 'high',
        'bg-warning': priority === 'medium',
        'bg-info': priority === 'low'
      };
    },

    editTask(id) {
    this.$router.push(`/tasks/edit/${id}`);
    },

    deleteTask(id) {
      this.taskToDelete = id;
      this.showDeleteModal = true;
    },

    async confirmDelete() {
      if (this.taskToDelete) {
        try {
          await api.delete(`/tasks/${this.taskToDelete}`);
          this.tasks = this.tasks.filter(task => task.id !== this.taskToDelete);
        } catch (error) {
          console.error('Error deleting task:', error);
          alert('Failed to delete task. Please try again.');
        } finally {
          this.taskToDelete = null;
          this.showDeleteModal = false;
        }
      }
    },

    cancelDelete() {
      this.taskToDelete = null;
      this.showDeleteModal = false;
    },

    async toggleTaskCompletion(task) {
      try {
        const originalState = task.completed;
        task.completed = !task.completed;
        
        await api.patch(`/tasks/${task.id}`, {
          completed: task.completed
        });
        
      } catch (error) {
        console.error('Error updating task:', error);
        task.completed = !task.completed; 
      }
    },
    
    isOverdue(task) {
      if (!task.due_date) return false;
      
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      const dueDate = new Date(task.due_date);
      dueDate.setHours(0, 0, 0, 0);
      
      return !task.completed && dueDate < today;
    },

    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.fetchTasks(page);
      }
    }
  },

  async mounted() {
    await this.fetchTasks();
  },
}
</script>

<style scoped>
.card {
  border-radius: 10px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 15px;
}
.card:hover {
  box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}
.badge {
  font-size: 0.85em;
  padding: 0.5em 0.75em;
}
</style>