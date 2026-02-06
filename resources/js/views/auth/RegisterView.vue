<template>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-white">ExpenseTracker</h1>
                <h2 class="mt-6 text-2xl font-semibold text-white">Create your account</h2>
            </div>
            <AppCard>
                <form @submit.prevent="handleRegister" class="space-y-4">
                    <div v-if="error" class="p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-600">{{ error }}</p>
                    </div>
                    <AppInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        label="Full name"
                        placeholder="John Doe"
                        required
                        :error="errors.name"
                    />
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
                        hint="Minimum 8 characters"
                        :error="errors.password"
                    />
                    <AppInput
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        label="Confirm password"
                        placeholder="••••••••"
                        required
                        :error="errors.password_confirmation"
                    />
                    <AppButton
                        type="submit"
                        class="w-full"
                        :loading="authStore.loading"
                    >
                        Create account
                    </AppButton>
                </form>
                <template #footer>
                    <p class="text-center text-sm text-gray-600">
                        Already have an account?
                        <router-link to="/login" class="text-indigo-600 hover:text-indigo-500 font-medium">
                            Sign in
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
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const errors = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const error = ref('');

const handleRegister = async () => {
    error.value = '';
    Object.keys(errors).forEach(key => errors[key] = '');

    try {
        await authStore.register(form);
        router.push('/dashboard');
    } catch (e) {
        if (e.response?.data?.errors) {
            const apiErrors = e.response.data.errors;
            Object.keys(errors).forEach(key => {
                errors[key] = apiErrors[key]?.[0] || '';
            });
        } else {
            error.value = e.response?.data?.message || 'Registration failed';
        }
    }
};
</script>
