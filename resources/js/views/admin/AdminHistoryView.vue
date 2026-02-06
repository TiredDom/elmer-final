<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-6">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Expense History</h1>
            <p class="text-gray-600 mt-1">Complete history of all processed expenses</p>
        </div>

        <div class="mb-6 flex flex-wrap gap-4">
            <div class="flex gap-2">
                <button
                    v-for="filter in statusFilters"
                    :key="filter.value"
                    @click="currentStatus = filter.value"
                    :class="[
                        'px-4 py-2 text-sm font-medium rounded-lg transition-colors',
                        currentStatus === filter.value
                            ? 'bg-indigo-600 text-white'
                            : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                    ]"
                >
                    {{ filter.label }}
                </button>
            </div>
        </div>

        <AppCard>
            <div v-if="adminStore.loading" class="text-center py-8">
                <p class="text-gray-500">Loading...</p>
            </div>
            <div v-else-if="adminStore.expenses.length === 0" class="text-center py-8">
                <p class="text-gray-500">No expenses found</p>
            </div>
            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reviewed By</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="expense in adminStore.expenses" :key="expense.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-500">#{{ expense.id }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ expense.user?.name }}</td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium text-gray-900">{{ expense.title }}</p>
                                <p class="text-xs text-gray-500 truncate max-w-xs">{{ expense.description || '-' }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">₱{{ formatAmount(expense.amount) }}</td>
                            <td class="px-4 py-3">
                                <StatusBadge :status="expense.status" />
                                <p v-if="expense.rejection_reason" class="text-xs text-red-500 mt-1 max-w-xs">
                                    {{ expense.rejection_reason }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ expense.reviewer?.name || (expense.status !== 'pending' ? 'Auto-processed' : '-') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                <p>{{ formatDate(expense.created_at) }}</p>
                                <p v-if="expense.reviewed_at" class="text-xs">
                                    Reviewed: {{ formatDate(expense.reviewed_at) }}
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <template v-if="adminStore.pagination" #footer>
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        Showing {{ adminStore.expenses.length }} of {{ adminStore.pagination.total }} expenses
                    </p>
                    <div class="flex gap-2">
                        <button
                            v-if="adminStore.pagination.currentPage > 1"
                            @click="loadPage(adminStore.pagination.currentPage - 1)"
                            class="px-3 py-1 text-sm border rounded hover:bg-gray-50"
                        >
                            Previous
                        </button>
                        <button
                            v-if="adminStore.pagination.currentPage < adminStore.pagination.lastPage"
                            @click="loadPage(adminStore.pagination.currentPage + 1)"
                            class="px-3 py-1 text-sm border rounded hover:bg-gray-50"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </template>
        </AppCard>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useAdminExpenseStore } from '../../stores/expense';
import AppCard from '../../components/AppCard.vue';
import StatusBadge from '../../components/StatusBadge.vue';

const adminStore = useAdminExpenseStore();

const statusFilters = [
    { label: 'All', value: '' },
    { label: 'Approved', value: 'approved' },
    { label: 'Rejected', value: 'rejected' },
    { label: 'Pending', value: 'pending' },
];

const currentStatus = ref('');

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

const loadPage = (page) => {
    adminStore.fetchExpenses({
        status: currentStatus.value || undefined,
        page
    });
};

onMounted(() => {
    adminStore.fetchExpenses();
});

watch(currentStatus, (newStatus) => {
    adminStore.fetchExpenses({ status: newStatus || undefined });
});
</script>
