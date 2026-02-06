import { defineStore } from 'pinia';
import { expenseService, adminExpenseService } from '../services/expenseService';

export const useExpenseStore = defineStore('expense', {
    state: () => ({
        expenses: [],
        currentExpense: null,
        pagination: null,
        loading: false,
        error: null,
    }),

    actions: {
        async fetchExpenses(params = {}) {
            this.loading = true;
            this.error = null;
            try {
                const response = await expenseService.getExpenses(params);
                this.expenses = response.data;
                this.pagination = {
                    currentPage: response.current_page,
                    lastPage: response.last_page,
                    total: response.total,
                };
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch expenses';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchExpense(id) {
            this.loading = true;
            this.error = null;
            try {
                this.currentExpense = await expenseService.getExpense(id);
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch expense';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async createExpense(data) {
            this.loading = true;
            this.error = null;
            try {
                const response = await expenseService.createExpense(data);
                return response;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to create expense';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        clearCurrentExpense() {
            this.currentExpense = null;
        },
    },
});

export const useAdminExpenseStore = defineStore('adminExpense', {
    state: () => ({
        expenses: [],
        currentExpense: null,
        dashboard: null,
        pagination: null,
        loading: false,
        error: null,
    }),

    actions: {
        async fetchDashboard(params = {}) {
            this.loading = true;
            this.error = null;
            try {
                this.dashboard = await adminExpenseService.getDashboard(params);
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch dashboard';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchExpenses(params = {}) {
            this.loading = true;
            this.error = null;
            try {
                const response = await adminExpenseService.getExpenses(params);
                this.expenses = response.data;
                this.pagination = {
                    currentPage: response.current_page,
                    lastPage: response.last_page,
                    total: response.total,
                };
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch expenses';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchExpense(id) {
            this.loading = true;
            this.error = null;
            try {
                this.currentExpense = await adminExpenseService.getExpense(id);
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch expense';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async approveExpense(id) {
            this.loading = true;
            this.error = null;
            try {
                const response = await adminExpenseService.approveExpense(id);
                await this.fetchExpenses();
                await this.fetchDashboard();
                return response;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to approve expense';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async rejectExpense(id, reason) {
            this.loading = true;
            this.error = null;
            try {
                const response = await adminExpenseService.rejectExpense(id, reason);
                await this.fetchExpenses();
                await this.fetchDashboard();
                return response;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to reject expense';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        clearCurrentExpense() {
            this.currentExpense = null;
        },
    },
});
