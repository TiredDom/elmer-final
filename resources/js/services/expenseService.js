import api from './api';

export const expenseService = {
    async getExpenses(params = {}) {
        const response = await api.get('/expenses', { params });
        return response.data;
    },

    async getExpense(id) {
        const response = await api.get(`/expenses/${id}`);
        return response.data;
    },

    async createExpense(data) {
        const response = await api.post('/expenses', data);
        return response.data;
    },
};

export const adminExpenseService = {
    async getDashboard(params = {}) {
        const response = await api.get('/admin/dashboard', { params });
        return response.data;
    },

    async getExpenses(params = {}) {
        const response = await api.get('/admin/expenses', { params });
        return response.data;
    },

    async getExpense(id) {
        const response = await api.get(`/admin/expenses/${id}`);
        return response.data;
    },

    async approveExpense(id) {
        const response = await api.post(`/admin/expenses/${id}/approve`);
        return response.data;
    },

    async rejectExpense(id, reason) {
        const response = await api.post(`/admin/expenses/${id}/reject`, { reason });
        return response.data;
    },
};
