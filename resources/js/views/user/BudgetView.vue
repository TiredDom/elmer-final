<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-6">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Budget Management</h1>
            <p class="text-gray-600 mt-1">Showing budget for {{ currentMonthName }}</p>
        </div>

            <!-- Current Budget Card -->
            <AppCard class="mb-8">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Current Month Budget</h3>
                        <p class="text-sm text-gray-500">
                            {{ monthName(currentBudget.month) }} {{ currentBudget.year }}
                        </p>
                    </div>
                    <AppButton
                        @click="showAdvanceConfirm = true"
                        variant="primary"
                        :disabled="advancing"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Advance to {{ nextMonthName }}
                    </AppButton>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Budget Limit</p>
                        <p class="text-2xl font-bold text-gray-900">₱{{ formatAmount(currentBudget.budget_limit) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Total Approved</p>
                        <p class="text-2xl font-bold text-green-600">₱{{ formatAmount(currentBudget.total_approved) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Remaining Budget</p>
                        <p class="text-2xl font-bold" :class="currentBudget.remaining_budget > 0 ? 'text-blue-600' : 'text-red-600'">
                            ₱{{ formatAmount(currentBudget.remaining_budget) }}
                        </p>
                    </div>
                </div>

                <!-- Budget Progress Bar -->
                <div class="mt-6">
                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                        <span>Budget Usage</span>
                        <span>{{ budgetPercentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div
                            class="h-3 rounded-full transition-all duration-300"
                            :class="budgetPercentage >= 90 ? 'bg-red-600' : budgetPercentage >= 70 ? 'bg-yellow-500' : 'bg-green-600'"
                            :style="{ width: `${Math.min(budgetPercentage, 100)}%` }"
                        ></div>
                    </div>
                </div>
            </AppCard>

            <!-- Budget History -->
            <AppCard>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Budget History</h3>

                <div v-if="loading" class="text-center py-8">
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

        <!-- Advance Month Confirmation Modal -->
        <div v-if="showAdvanceConfirm" class="fixed inset-0 z-50 overflow-y-auto" @click.self="showAdvanceConfirm = false">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-30"></div>
                <div class="relative bg-white rounded-lg max-w-md w-full p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Advance to {{ nextMonthName }}?</h3>
                    <p class="text-gray-600 mb-6">
                        This will:
                    </p>
                    <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                        <li>Save {{ currentMonthName }} budget data to history</li>
                        <li>Move to {{ nextMonthName }}</li>
                        <li>Reset the total approved amount to ₱0.00</li>
                        <li>Keep the budget limit at ₱{{ formatAmount(currentBudget.budget_limit) }}</li>
                        <li>All expense records will be preserved</li>
                    </ul>
                    <div class="flex justify-end space-x-3">
                        <AppButton @click="showAdvanceConfirm = false" variant="secondary">
                            Cancel
                        </AppButton>
                        <AppButton @click="advanceMonth" variant="primary" :disabled="advancing">
                            {{ advancing ? 'Advancing...' : 'Yes, Advance Month' }}
                        </AppButton>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AppCard from '../../components/AppCard.vue';
import AppButton from '../../components/AppButton.vue';
import budgetService from '../../services/budgetService';

const currentBudget = ref({
    month: 0,
    year: 0,
    budget_limit: 0,
    total_approved: 0,
    remaining_budget: 0,
});

const history = ref([]);
const loading = ref(false);
const advancing = ref(false);
const showAdvanceConfirm = ref(false);

const budgetPercentage = computed(() => {
    if (currentBudget.value.budget_limit === 0) return 0;
    return Math.round((currentBudget.value.total_approved / currentBudget.value.budget_limit) * 100);
});

const currentMonthName = computed(() => {
    if (!currentBudget.value.month || !currentBudget.value.year) return '';
    return `${monthName(currentBudget.value.month)} ${currentBudget.value.year}`;
});

const nextMonthName = computed(() => {
    if (!currentBudget.value.month || !currentBudget.value.year) return '';
    let nextMonth = currentBudget.value.month + 1;
    let nextYear = currentBudget.value.year;

    if (nextMonth > 12) {
        nextMonth = 1;
        nextYear++;
    }

    return `${monthName(nextMonth)} ${nextYear}`;
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

const fetchCurrentBudget = async () => {
    try {
        const response = await budgetService.getCurrentBudget();
        currentBudget.value = response.data;
    } catch (error) {
        console.error('Failed to fetch current budget:', error);
    }
};

const fetchHistory = async () => {
    loading.value = true;
    try {
        const response = await budgetService.getBudgetHistory();
        history.value = response.data.data;
    } catch (error) {
        console.error('Failed to fetch budget history:', error);
    } finally {
        loading.value = false;
    }
};

const advanceMonth = async () => {
    advancing.value = true;
    try {
        const response = await budgetService.advanceMonth();
        alert(response.data.message);
        showAdvanceConfirm.value = false;

        // Refresh data
        await fetchCurrentBudget();
        await fetchHistory();
    } catch (error) {
        console.error('Failed to advance month:', error);
        alert('Failed to advance month. Please try again.');
    } finally {
        advancing.value = false;
    }
};

onMounted(() => {
    fetchCurrentBudget();
    fetchHistory();
});
</script>

