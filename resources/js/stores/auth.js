import { defineStore } from 'pinia';
import { authService } from '../services/authService';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: JSON.parse(localStorage.getItem('user')) || null,
        token: localStorage.getItem('token') || null,
        loading: false,
        error: null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        isAdmin: (state) => state.user?.role === 'admin',
    },

    actions: {
        async register(data) {
            this.loading = true;
            this.error = null;
            try {
                const response = await authService.register(data);
                this.setAuth(response.user, response.token);
                return response;
            } catch (error) {
                this.error = error.response?.data?.message || 'Registration failed';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async login(data) {
            this.loading = true;
            this.error = null;
            try {
                const response = await authService.login(data);
                this.setAuth(response.user, response.token);
                return response;
            } catch (error) {
                this.error = error.response?.data?.message || 'Login failed';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            try {
                await authService.logout();
            } catch (error) {
                console.error('Logout error:', error);
            } finally {
                this.clearAuth();
            }
        },

        async fetchUser() {
            try {
                const user = await authService.getUser();
                this.user = user;
                localStorage.setItem('user', JSON.stringify(user));
            } catch (error) {
                this.clearAuth();
            }
        },

        setAuth(user, token) {
            this.user = user;
            this.token = token;
            localStorage.setItem('user', JSON.stringify(user));
            localStorage.setItem('token', token);
        },

        clearAuth() {
            this.user = null;
            this.token = null;
            localStorage.removeItem('user');
            localStorage.removeItem('token');
        },
    },
});
