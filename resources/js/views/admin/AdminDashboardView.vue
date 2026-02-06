<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-6">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="text-gray-600 mt-1">Showing budget for {{ currentBudgetPeriod }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <AppCard>
                <div class="text-center">
                    <p class="text-sm text-gray-500">Monthly Budget</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">
                        ₱{{ formatAmount(dashboard?.budget_limit || 0) }}
                    </p>
                </div>
            </AppCard>
            <AppCard>
                <div class="text-center">
                    <p class="text-sm text-gray-500">Total Approved</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">
                        ₱{{ formatAmount(dashboard?.total_approved || 0) }}
                    </p>
                </div>
            </AppCard>
            <AppCard>
                <div class="text-center">
                    <p class="text-sm text-gray-500">Remaining Budget</p>
                    <p class="text-3xl font-bold text-indigo-600 mt-2">
                        ₱{{ formatAmount(dashboard?.remaining_budget || 0) }}
                    </p>
                </div>
            </AppCard>
            <AppCard>
                <div class="text-center">
                    <p class="text-sm text-gray-500">Pending Requests</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-2">
                        {{ dashboard?.pending_count || 0 }}
                    </p>
                </div>
            </AppCard>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <AppCard title="Budget Usage">
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Used</span>
                            <span class="font-medium">{{ budgetPercentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div
                                class="h-3 rounded-full transition-all duration-500"
                                :class="budgetPercentage > 80 ? 'bg-red-500' : 'bg-indigo-600'"
                                :style="{ width: `${Math.min(budgetPercentage, 100)}%` }"
                            ></div>
                        </div>
                    </div>
                </div>
            </AppCard>
            <AppCard title="Request Summary">
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Approved</span>
                        <span class="font-medium text-green-600">{{ dashboard?.approved_count || 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Pending</span>
                        <span class="font-medium text-yellow-600">{{ dashboard?.pending_count || 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Rejected</span>
                        <span class="font-medium text-red-600">{{ dashboard?.rejected_count || 0 }}</span>
                    </div>
                </div>
            </AppCard>
            <AppCard title="Quick Actions">
                <div class="space-y-3">
                    <router-link
                        to="/admin/expenses"
                        class="block w-full px-4 py-2 text-center text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100"
                    >
                        Review Pending Expenses
                    </router-link>
                    <router-link
                        to="/admin/history"
                        class="block w-full px-4 py-2 text-center text-sm font-medium text-gray-600 bg-gray-50 rounded-lg hover:bg-gray-100"
                    >
                        View Expense History
                    </router-link>
                </div>
            </AppCard>
        </div>

        <AppCard title="Recent Pending Requests">
            <div v-if="adminStore.loading" class="text-center py-8">
                <p class="text-gray-500">Loading...</p>
            </div>
            <div v-else-if="pendingExpenses.length === 0" class="text-center py-8">
                <p class="text-gray-500">No pending expense requests</p>
            </div>
            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="expense in pendingExpenses.slice(0, 5)" :key="expense.id">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ expense.user?.name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ expense.title }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">₱{{ formatAmount(expense.amount) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ formatDate(expense.created_at) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <button
                                        @click="handleApprove(expense.id)"
                                        class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded hover:bg-green-200"
                                    >
                                        Approve
                                    </button>
                                    <button
                                        @click="openRejectModal(expense)"
                                        class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200"
                                    >
                                        Reject
                                    </button>
                                </div>
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
import { ref, computed, onMounted } from 'vue';
import { useAdminExpenseStore } from '../../stores/expense';
import AppCard from '../../components/AppCard.vue';
import RejectModal from '../../components/RejectModal.vue';

const adminStore = useAdminExpenseStore();

const showRejectModal = ref(false);
const selectedExpense = ref(null);

const dashboard = computed(() => adminStore.dashboard);

const pendingExpenses = computed(() => {
    return adminStore.expenses.filter(e => e.status === 'pending');
});

const budgetPercentage = computed(() => {
    if (!dashboard.value?.budget_limit) return 0;
    return Math.round((dashboard.value.total_approved / dashboard.value.budget_limit) * 100);
});

const currentBudgetPeriod = computed(() => {
    if (!dashboard.value?.month || !dashboard.value?.year) {
        return new Date().toLocaleDateString('en-PH', { month: 'long', year: 'numeric' });
    }
    const months = ['', 'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'];
    return `${months[dashboard.value.month]} ${dashboard.value.year}`;
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

const handleApprove = async (id) => {
    try {
        await adminStore.approveExpense(id);
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
    } catch (error) {
        alert(error.response?.data?.message || 'Failed to reject expense');
    }
};

onMounted(() => {
    adminStore.fetchDashboard();
    adminStore.fetchExpenses({ status: 'pending' });
});
</script>
