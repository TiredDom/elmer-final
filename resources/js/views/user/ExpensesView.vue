<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-6">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Expenses</h1>
                <p class="text-gray-600 mt-1">View and track all your expense requests</p>
            </div>
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

        <div class="mb-6 flex gap-2">
            <button
                v-for="filter in filters"
                :key="filter.value"
                @click="currentFilter = filter.value"
                :class="[
                    'px-4 py-2 text-sm font-medium rounded-lg transition-colors',
                    currentFilter === filter.value
                        ? 'bg-indigo-600 text-white'
                        : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                ]"
            >
                {{ filter.label }}
            </button>
        </div>

        <AppCard>
            <div v-if="expenseStore.loading" class="text-center py-8">
                <p class="text-gray-500">Loading...</p>
            </div>
            <div v-else-if="filteredExpenses.length === 0" class="text-center py-8">
                <p class="text-gray-500">No expenses found</p>
            </div>
            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="expense in filteredExpenses" :key="expense.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ expense.title }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ expense.description || '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">₱{{ formatAmount(expense.amount) }}</td>
                            <td class="px-4 py-3">
                                <StatusBadge :status="expense.status" />
                                <p v-if="expense.rejection_reason" class="text-xs text-red-500 mt-1">
                                    {{ expense.rejection_reason }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ formatDate(expense.created_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </AppCard>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useExpenseStore } from '../../stores/expense';
import AppCard from '../../components/AppCard.vue';
import StatusBadge from '../../components/StatusBadge.vue';

const expenseStore = useExpenseStore();

const filters = [
    { label: 'All', value: '' },
    { label: 'Pending', value: 'pending' },
    { label: 'Approved', value: 'approved' },
    { label: 'Rejected', value: 'rejected' },
];

const currentFilter = ref('');

const filteredExpenses = computed(() => {
    if (!currentFilter.value) return expenseStore.expenses;
    return expenseStore.expenses.filter(e => e.status === currentFilter.value);
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

watch(currentFilter, (newFilter) => {
    expenseStore.fetchExpenses({ status: newFilter || undefined });
});
</script>
