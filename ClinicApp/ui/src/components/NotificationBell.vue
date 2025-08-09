<template>
  <div class="notification-bell">
    <button @click="toggleNotifications" class="bell-button">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
      </svg>
      <span v-if="unreadCount" class="badge">{{ unreadCount }}</span>
    </button>
    
    <div v-if="showNotifications" class="notification-dropdown">
      <div class="notification-header">
        <h3>Notifications</h3>
        <button @click="markAllAsRead">Mark all as read</button>
      </div>
      
      <div v-if="notifications.length === 0" class="empty-notifications">
        No notifications
      </div>
      
      <div v-else>
        <div 
          v-for="notification in notifications" 
          :key="notification.id"
          class="notification-item"
          :class="{'unread': !notification.is_read}"
          @click="markAsRead(notification)"
        >
          <div class="notification-message">{{ notification.message }}</div>
          <div class="notification-time">{{ formatTime(notification.created_at) }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';

const notifications = ref([]);
const unreadCount = ref(0);
const showNotifications = ref(false);

const fetchNotifications = async () => {
  try {
    const response = await api.get('/notifications');
    notifications.value = response.data.notifications;
    unreadCount.value = response.data.unread_count;
  } catch (error) {
    console.error('Error fetching notifications:', error);
    
    if (error.response?.status === 401) {
      console.warn('Session expired, notifications could not be loaded');
    }
  }
};

const markAsRead = async (notification) => {
  if (!notification.is_read) {
    try {
      await api.post(`/notifications/${notification.id}/read`);
      notification.is_read = true;
      unreadCount.value--;
    } catch (error) {
      console.error('Error marking notification as read:', error);
    }
  }
};

const markAllAsRead = async () => {
  try {
    await api.post('/notifications/read-all');
    notifications.value.forEach(n => n.is_read = true);
    unreadCount.value = 0;
  } catch (error) {
    console.error('Error marking all notifications as read:', error);
  }
};

const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value;
  if (showNotifications.value) {
    fetchNotifications();
  }
};

const formatTime = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

// Polling each 60 seconds
onMounted(() => {
  fetchNotifications();
  setInterval(fetchNotifications, 60000);
});
</script>

<style scoped>
/* Styles  */
.notification-bell {
  position: relative;
  display: inline-block;
}

.bell-button {
  position: relative;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.5rem;
}

.badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: red;
  color: white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  font-size: 0.75rem;
  display: flex;
  justify-content: center;
  align-items: center;
}

.notification-dropdown {
  position: absolute;
  right: 0;
  top: 100%;
  width: 350px;
  max-height: 400px;
  overflow-y: auto;
  background: white;
  border: 1px solid #ddd;
  border-radius: 4px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  z-index: 1000;
}

.notification-header {
  display: flex;
  justify-content: space-between;
  padding: 10px;
  border-bottom: 1px solid #eee;
}

.notification-item {
  padding: 10px;
  border-bottom: 1px solid #eee;
  cursor: pointer;
}

.notification-item.unread {
  background-color: #f0f8ff;
  font-weight: bold;
}

.notification-message {
  margin-bottom: 5px;
}

.notification-time {
  font-size: 0.8rem;
  color: #666;
}

.empty-notifications {
  padding: 20px;
  text-align: center;
  color: #999;
}
</style>