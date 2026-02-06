import api from './api';

const budgetService = {
    getCurrentBudget() {
        return api.get('/budget/current');
    },

    advanceBudget() {
        return api.post('/budget/advance');
    },

    getBudgetHistory() {
        return api.get('/budget/history');
    },
};

export default budgetService;
