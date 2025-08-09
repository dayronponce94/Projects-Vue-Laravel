import { reactive } from 'vue';
import api from '@/services/api';

const state = reactive({
    user: JSON.parse(localStorage.getItem('user')) || null,
    isAuthenticated: !!localStorage.getItem('authToken')
});

export const authStore = {
    login(user, token) {
        localStorage.setItem('authToken', token);
        localStorage.setItem('user', JSON.stringify(user));
        state.user = user;
        state.isAuthenticated = true;
    },
    async logout() {
        const token = localStorage.getItem('authToken');

        localStorage.removeItem('authToken');
        localStorage.removeItem('user');
        state.user = null;
        state.isAuthenticated = false;

        if (token) {
            try {
                await api.post('/logout', {}, {
                    headers: {
                        Authorization: `Bearer ${token}`
                    }
                });
            } catch (error) {
                if (error.response?.status !== 401) {
                    console.error('Logout error:', error);
                }
            }
        }
    },
    getState() {
        return state;
    }
};