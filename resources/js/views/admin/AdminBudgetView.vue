<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-6">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Budget Overview</h1>
            <p class="text-gray-600 mt-1">Monitor monthly budget allocation and usage</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <AppCard title="Current Month Budget">
                <div class="space-y-6">
                    <div class="text-center py-4">
                        <p class="text-4xl font-bold text-indigo-600">
                            ₱{{ formatAmount(dashboard?.remaining_budget || 0) }}
                        </p>
                        <p class="text-gray-500 mt-2">Remaining Budget</p>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">Budget Used</span>
                            <span class="font-medium">{{ budgetPercentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div
                                class="h-4 rounded-full transition-all duration-500"
                                :class="budgetBarClass"
                                :style="{ width: `${Math.min(budgetPercentage, 100)}%` }"
                            ></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900">
                                ₱{{ formatAmount(dashboard?.budget_limit || 0) }}
                            </p>
                            <p class="text-sm text-gray-500">Total Budget</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600">
                                ₱{{ formatAmount(dashboard?.total_approved || 0) }}
                            </p>
                            <p class="text-sm text-gray-500">Total Approved</p>
                        </div>
                    </div>
                </div>
            </AppCard>

            <AppCard title="Monthly Statistics">
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-2 bg-yellow-100 rounded-lg">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="ml-3 text-gray-700">Pending Requests</span>
                        </div>
                        <span class="text-xl font-bold text-yellow-600">{{ dashboard?.pending_count || 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="ml-3 text-gray-700">Approved</span>
                        </div>
                        <span class="text-xl font-bold text-green-600">{{ dashboard?.approved_count || 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-2 bg-red-100 rounded-lg">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="ml-3 text-gray-700">Rejected</span>
                        </div>
                        <span class="text-xl font-bold text-red-600">{{ dashboard?.rejected_count || 0 }}</span>
                    </div>
                </div>
            </AppCard>
        </div>

        <AppCard title="Budget Information" class="mb-8">
            <div class="space-y-4">
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="font-medium text-blue-900">Monthly Budget Limit</h4>
                    <p class="text-sm text-blue-700 mt-1">
                        The monthly budget is set at ₱{{ formatAmount(dashboard?.budget_limit || 10000) }}.
                        Expenses that exceed the remaining budget will be automatically rejected.
                    </p>
                </div>
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg">
                    <h4 class="font-medium text-amber-900">Budget Reset</h4>
                    <p class="text-sm text-amber-700 mt-1">
                        Users can reset the budget manually from their budget page.
                        Current month: {{ currentMonthYear }}
                    </p>
                </div>
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h4 class="font-medium text-gray-900">Admin Restrictions</h4>
                    <p class="text-sm text-gray-700 mt-1">
                        Admins cannot override budget limits or edit submitted expense amounts.
                        This ensures financial integrity and accountability.
                    </p>
                </div>
            </div>
        </AppCard>

        <!-- Budget History -->
        <AppCard title="Budget History">
            <div v-if="loadingHistory" class="text-center py-8">
                <p class="text-gray-500">Loading history...</p>
            </div>
            <div v-else-if="history.length === 0" class="text-center py-8">
                <p class="text-gray-500">No budget history yet.</p>
            </div>
            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budget Limit</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Approved</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remaining</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expenses</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reset Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="record in history" :key="record.id">
                            <td class="px-4 py-3 text-sm text-gray-900">
                                {{ monthName(record.month) }} {{ record.year }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">₱{{ formatAmount(record.budget_limit) }}</td>
                            <td class="px-4 py-3 text-sm text-green-600">₱{{ formatAmount(record.total_approved) }}</td>
                            <td class="px-4 py-3 text-sm" :class="record.remaining_budget > 0 ? 'text-blue-600' : 'text-red-600'">
                                ₱{{ formatAmount(record.remaining_budget) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <span class="text-green-600">{{ record.approved_count }} approved</span> /
                                <span class="text-red-600">{{ record.rejected_count }} rejected</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ formatDate(record.reset_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </AppCard>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAdminExpenseStore } from '../../stores/expense';
import budgetService from '../../services/budgetService';
import AppCard from '../../components/AppCard.vue';

const adminStore = useAdminExpenseStore();
const history = ref([]);
const loadingHistory = ref(false);

const dashboard = computed(() => adminStore.dashboard);

const budgetPercentage = computed(() => {
    if (!dashboard.value?.budget_limit) return 0;
    return Math.round((dashboard.value.total_approved / dashboard.value.budget_limit) * 100);
});

const budgetBarClass = computed(() => {
    if (budgetPercentage.value > 90) return 'bg-red-500';
    if (budgetPercentage.value > 70) return 'bg-yellow-500';
    return 'bg-indigo-600';
});

const currentMonthYear = computed(() => {
    return new Date().toLocaleDateString('en-PH', { month: 'long', year: 'numeric' });
});

const monthName = (month) => {
    const months = ['', 'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'];
    return months[month];
};

const formatAmount = (amount) => {
    return parseFloat(amount).toLocaleString('en-PH', { minimumFractionDigits: 2 });
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const fetchHistory = async () => {
    loadingHistory.value = true;
    try {
        const response = await budgetService.getBudgetHistory();
        history.value = response.data.data;
    } catch (error) {
        console.error('Failed to fetch budget history:', error);
    } finally {
        loadingHistory.value = false;
    }
};

onMounted(() => {
    adminStore.fetchDashboard();
    fetchHistory();
});
</script>
