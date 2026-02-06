<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-6">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600 mt-1">Welcome back, {{ authStore.user?.name }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <AppCard>
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Pending</p>
                        <p class="text-2xl font-bold text-gray-900">{{ stats.pending }}</p>
                    </div>
                </div>
            </AppCard>
            <AppCard>
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Approved</p>
                        <p class="text-2xl font-bold text-gray-900">{{ stats.approved }}</p>
                    </div>
                </div>
            </AppCard>
            <AppCard>
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Rejected</p>
                        <p class="text-2xl font-bold text-gray-900">{{ stats.rejected }}</p>
                    </div>
                </div>
            </AppCard>
        </div>

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Recent Expenses</h2>
            <router-link
                to="/expenses/new"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Expense
            </router-link>
        </div>

        <AppCard>
            <div v-if="expenseStore.loading" class="text-center py-8">
                <p class="text-gray-500">Loading...</p>
            </div>
            <div v-else-if="expenseStore.expenses.length === 0" class="text-center py-8">
                <p class="text-gray-500">No expenses yet. Create your first expense!</p>
            </div>
            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="expense in expenseStore.expenses.slice(0, 5)" :key="expense.id">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ expense.title }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">₱{{ formatAmount(expense.amount) }}</td>
                            <td class="px-4 py-3">
                                <StatusBadge :status="expense.status" />
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ formatDate(expense.created_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <template #footer>
                <router-link to="/expenses" class="text-sm text-indigo-600 hover:text-indigo-500">
                    View all expenses →
                </router-link>
            </template>
        </AppCard>
        </div>
    </div>
</template>

<script setup>
import { onMounted, computed } from 'vue';
import { useAuthStore } from '../../stores/auth';
import { useExpenseStore } from '../../stores/expense';
import AppCard from '../../components/AppCard.vue';
import StatusBadge from '../../components/StatusBadge.vue';

const authStore = useAuthStore();
const expenseStore = useExpenseStore();

const stats = computed(() => {
    const expenses = expenseStore.expenses;
    return {
        pending: expenses.filter(e => e.status === 'pending').length,
        approved: expenses.filter(e => e.status === 'approved').length,
        rejected: expenses.filter(e => e.status === 'rejected').length,
    };
});

const formatAmount = (amount) => {
    return parseFloat(amount).toLocaleString('en-PH', { minimumFractionDigits: 2 });
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

onMounted(() => {
    expenseStore.fetchExpenses();
});
</script>
