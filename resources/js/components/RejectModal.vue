<template>
    <div
        v-if="expense"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        @click.self="$emit('close')"
    >
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Reject Expense</h3>
                <p class="text-sm text-gray-500 mt-1">{{ expense.title }} - ₱{{ formatAmount(expense.amount) }}</p>
            </div>
            <div class="px-6 py-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Reason for rejection <span class="text-red-500">*</span>
                </label>
                <textarea
                    v-model="reason"
                    rows="4"
                    placeholder="Please provide a reason for rejecting this expense..."
                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                    :class="{ 'border-red-300': error }"
                ></textarea>
                <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end gap-3">
                <button
                    @click="$emit('close')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                >
                    Cancel
                </button>
                <button
                    @click="handleReject"
                    :disabled="loading"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50"
                >
                    {{ loading ? 'Rejecting...' : 'Reject Expense' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    expense: Object,
});

const emit = defineEmits(['close', 'reject']);

const reason = ref('');
const error = ref('');
const loading = ref(false);

const formatAmount = (amount) => {
    return parseFloat(amount).toLocaleString('en-PH', { minimumFractionDigits: 2 });
};

const handleReject = () => {
    error.value = '';
    if (!reason.value.trim()) {
        error.value = 'Please provide a reason for rejection';
        return;
    }
    loading.value = true;
    emit('reject', reason.value);
};
</script>
