import api from './api';

export default {
  getCurrentBudget() {
    return api.get('/budget/current');
  },

  advanceMonth() {
    return api.post('/budget/advance');
  },

  getBudgetHistory(page = 1) {
    return api.get('/budget/history', { params: { page } });
  },
};

