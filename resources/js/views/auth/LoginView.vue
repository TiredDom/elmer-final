<template>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-white">ExpenseTracker</h1>
                <h2 class="mt-6 text-2xl font-semibold text-white">Sign in to your account</h2>
            </div>
            <AppCard>
                <form @submit.prevent="handleLogin" class="space-y-4">
                    <div v-if="error" class="p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-600">{{ error }}</p>
                    </div>
                    <AppInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        label="Email address"
                        placeholder="you@example.com"
                        required
                        :error="errors.email"
                    />
                    <AppInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        label="Password"
                        placeholder="••••••••"
                        required
                        :error="errors.password"
                    />
                    <AppButton
                        type="submit"
                        class="w-full"
                        :loading="authStore.loading"
                    >
                        Sign in
                    </AppButton>
                </form>
                <template #footer>
                    <p class="text-center text-sm text-gray-600">
                        Don't have an account?
                        <router-link to="/register" class="text-indigo-600 hover:text-indigo-500 font-medium">
                            Register
                        </router-link>
                    </p>
                </template>
            </AppCard>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import AppCard from '../../components/AppCard.vue';
import AppInput from '../../components/AppInput.vue';
import AppButton from '../../components/AppButton.vue';

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
    email: '',
    password: '',
});

const errors = reactive({
    email: '',
    password: '',
});

const error = ref('');

const handleLogin = async () => {
    error.value = '';
    errors.email = '';
    errors.password = '';

    try {
        await authStore.login(form);
        if (authStore.isAdmin) {
            router.push('/admin');
        } else {
            router.push('/dashboard');
        }
    } catch (e) {
        if (e.response?.data?.errors) {
            const apiErrors = e.response.data.errors;
            errors.email = apiErrors.email?.[0] || '';
            errors.password = apiErrors.password?.[0] || '';
        } else {
            error.value = e.response?.data?.message || 'Login failed';
        }
    }
};
</script>
