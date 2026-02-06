<template>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Submit New Expense</h1>
            <p class="text-gray-600 mt-1">Fill in the details for your expense request</p>
        </div>

        <AppCard>
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <div v-if="successMessage" class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-600">{{ successMessage }}</p>
                </div>
                <div v-if="errorMessage" class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-600">{{ errorMessage }}</p>
                </div>

                <AppInput
                    id="title"
                    v-model="form.title"
                    type="text"
                    label="Expense Title"
                    placeholder="e.g., Office Supplies"
                    required
                    :error="errors.title"
                />

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description
                    </label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="3"
                        placeholder="Provide additional details about this expense..."
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    ></textarea>
                    <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
                </div>

                <AppInput
                    id="amount"
                    v-model="form.amount"
                    type="number"
                    label="Amount (₱)"
                    placeholder="0.00"
                    required
                    hint="Amount must be greater than zero"
                    :error="errors.amount"
                />

                <div class="flex gap-3">
                    <AppButton
                        type="submit"
                        :loading="expenseStore.loading"
                    >
                        Submit Expense
                    </AppButton>
                    <AppButton
                        type="button"
                        variant="secondary"
                        @click="router.back()"
                    >
                        Cancel
                    </AppButton>
                </div>
            </form>
        </AppCard>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useExpenseStore } from '../../stores/expense';
import AppCard from '../../components/AppCard.vue';
import AppInput from '../../components/AppInput.vue';
import AppButton from '../../components/AppButton.vue';

const router = useRouter();
const expenseStore = useExpenseStore();

const form = reactive({
    title: '',
    description: '',
    amount: '',
});

const errors = reactive({
    title: '',
    description: '',
    amount: '',
});

const successMessage = ref('');
const errorMessage = ref('');

const handleSubmit = async () => {
    successMessage.value = '';
    errorMessage.value = '';
    Object.keys(errors).forEach(key => errors[key] = '');

    try {
        const response = await expenseStore.createExpense({
            ...form,
            amount: parseFloat(form.amount),
        });

        if (response.expense.status === 'rejected') {
            errorMessage.value = response.message;
        } else {
            successMessage.value = response.message;
            form.title = '';
            form.description = '';
            form.amount = '';
            setTimeout(() => {
                router.push('/expenses');
            }, 1500);
        }
    } catch (e) {
        if (e.response?.data?.errors) {
            const apiErrors = e.response.data.errors;
            Object.keys(errors).forEach(key => {
                errors[key] = apiErrors[key]?.[0] || '';
            });
        } else {
            errorMessage.value = e.response?.data?.message || 'Failed to submit expense';
        }
    }
};
</script>
