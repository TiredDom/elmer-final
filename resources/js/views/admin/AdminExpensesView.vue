<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-6">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Expense Requests</h1>
            <p class="text-gray-600 mt-1">Review and manage expense submissions</p>
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="expense in adminStore.expenses" :key="expense.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ expense.user?.name }}</td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium text-gray-900">{{ expense.title }}</p>
                                <p class="text-xs text-gray-500">{{ expense.description || 'No description' }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">₱{{ formatAmount(expense.amount) }}</td>
                            <td class="px-4 py-3">
                                <StatusBadge :status="expense.status" />
                                <p v-if="expense.rejection_reason" class="text-xs text-red-500 mt-1 max-w-xs truncate">
                                    {{ expense.rejection_reason }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ formatDate(expense.created_at) }}</td>
                            <td class="px-4 py-3">
                                <div v-if="expense.status === 'pending'" class="flex gap-2">
                                    <button
                                        @click="handleApprove(expense.id)"
                                        class="text-xs px-3 py-1.5 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 font-medium"
                                    >
                                        Approve
                                    </button>
                                    <button
                                        @click="openRejectModal(expense)"
                                        class="text-xs px-3 py-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 font-medium"
                                    >
                                        Reject
                                    </button>
                                </div>
                                <span v-else class="text-xs text-gray-400">
                                    {{ expense.reviewer?.name ? `by ${expense.reviewer.name}` : 'Auto-processed' }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </AppCard>

        <RejectModal
            v-if="showRejectModal"
            :expense="selectedExpense"
            @close="showRejectModal = false"
            @reject="handleReject"
        />
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useAdminExpenseStore } from '../../stores/expense';
import AppCard from '../../components/AppCard.vue';
import StatusBadge from '../../components/StatusBadge.vue';
import RejectModal from '../../components/RejectModal.vue';

const adminStore = useAdminExpenseStore();

const filters = [
    { label: 'Pending', value: 'pending' },
    { label: 'Approved', value: 'approved' },
    { label: 'Rejected', value: 'rejected' },
    { label: 'All', value: '' },
];

const currentFilter = ref('pending');
const showRejectModal = ref(false);
const selectedExpense = ref(null);

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

const handleApprove = async (id) => {
    try {
        await adminStore.approveExpense(id);
        await adminStore.fetchExpenses({ status: currentFilter.value || undefined });
    } catch (error) {
        alert(error.response?.data?.message || 'Failed to approve expense');
    }
};

const openRejectModal = (expense) => {
    selectedExpense.value = expense;
    showRejectModal.value = true;
};

const handleReject = async (reason) => {
    try {
        await adminStore.rejectExpense(selectedExpense.value.id, reason);
        showRejectModal.value = false;
        await adminStore.fetchExpenses({ status: currentFilter.value || undefined });
    } catch (error) {
        alert(error.response?.data?.message || 'Failed to reject expense');
    }
};

onMounted(() => {
    adminStore.fetchExpenses({ status: 'pending' });
});

watch(currentFilter, (newFilter) => {
    adminStore.fetchExpenses({ status: newFilter || undefined });
});
</script>
