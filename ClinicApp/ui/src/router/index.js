import { createRouter, createWebHistory } from 'vue-router';
import LoginForm from '@/components/auth/LoginForm.vue';
import RegisterForm from '@/components/auth/RegisterForm.vue';
import ProfileView from '@/views/ProfileView.vue';
import HomeView from '@/views/HomeView.vue'; 
import ScheduleView from '@/views/doctor/ScheduleView.vue';
import FindDoctors from '@/views/patient/FindDoctors.vue';
import DoctorAvailability from '@/views/patient/DoctorAvailability.vue';
import BookAppointment from '@/views/patient/BookAppointment.vue';
import AppointmentsView from '@/views/patient/AppointmentsView.vue';
import DailySchedule from '@/views/doctor/DailySchedule.vue';
import AdminDashboard from '@/views/admin/AdminDashboard.vue';
import ManageDoctors from '@/views/admin/ManageDoctors.vue';

const routes = [
  { 
    path: '/', 
    name: 'home',
    component: HomeView,
    meta: { requiresAuth: true } 
  },
  { path: '/login', component: LoginForm, name: 'login' },
  { path: '/register', component: RegisterForm, name: 'register' },
  { 
    path: '/profile', 
    component: ProfileView, 
    name: 'profile',
    meta: { requiresAuth: true } 
  },
 
  { path: '/:pathMatch(.*)*', redirect: '/' },
  { 
    path: '/schedule',
    component: ScheduleView,
    name: 'schedule',
    meta: { requiresAuth: true, role: 'doctor' }
  },
  { 
    path: '/find-doctors',
    component: FindDoctors,
    name: 'find-doctors',
    meta: { requiresAuth: true, role: 'patient' }
  },
  { 
    path: '/doctors/:doctorId/availability',
    component: DoctorAvailability,
    name: 'doctor-availability',
    meta: { requiresAuth: true, role: 'patient' }
  },
  { 
    path: '/doctors/:doctorId/book/:scheduleId',
    component: BookAppointment,
    name: 'book-appointment',
    meta: { requiresAuth: true, role: 'patient' }
  },
  { 
    path: '/appointments',
    component: AppointmentsView,
    name: 'appointments',
    meta: { requiresAuth: true, role: 'patient' }
  },
  { 
    path: '/doctor/schedule/daily',
    component: DailySchedule,
    name: 'daily-schedule',
    meta: { requiresAuth: true, role: 'doctor' }
  },
  { 
    path: '/admin/dashboard',
    component: AdminDashboard,
    name: 'admin-dashboard',
    meta: { requiresAuth: true, role: 'admin' }
  },
  { 
    path: '/admin/doctors',
    component: ManageDoctors,
    name: 'manage-doctors',
    meta: { requiresAuth: true, role: 'admin' }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach((to, from, next) => {
  const publicPages = ['/login', '/register'];
  const authRequired = to.matched.some(record => record.meta.requiresAuth);
  const token = localStorage.getItem('token');
  const storedUser = localStorage.getItem('user');
  const user = storedUser ? JSON.parse(storedUser) : null;

  if (authRequired && !token) {
    return next('/login');
  }

  if (publicPages.includes(to.path) && token) {
    return next('/');
  }

  if (to.meta.role) {
    if (!user || user.role !== to.meta.role) {
      alert('You do not have permission to access this page');
      return next('/');
    }
  }

  next();
});

export default router;