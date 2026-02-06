<template>
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <router-link to="/" class="text-xl font-bold text-indigo-600">
                        ExpenseTracker
                    </router-link>
                    <div class="hidden sm:ml-8 sm:flex sm:space-x-4">
                        <template v-if="authStore.isAdmin">
                            <router-link
                                to="/admin"
                                class="px-3 py-2 text-sm font-medium rounded-md"
                                :class="$route.path === '/admin' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-gray-900'"
                            >
                                Dashboard
                            </router-link>
                            <router-link
                                to="/admin/expenses"
                                class="px-3 py-2 text-sm font-medium rounded-md"
                                :class="$route.path === '/admin/expenses' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-gray-900'"
                            >
                                Expenses
                            </router-link>
                            <router-link
                                to="/admin/budget"
                                class="px-3 py-2 text-sm font-medium rounded-md"
                                :class="$route.path === '/admin/budget' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-gray-900'"
                            >
                                Budget
                            </router-link>
                            <router-link
                                to="/admin/history"
                                class="px-3 py-2 text-sm font-medium rounded-md"
                                :class="$route.path === '/admin/history' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-gray-900'"
                            >
                                History
                            </router-link>
                        </template>
                        <template v-else>
                            <router-link
                                to="/dashboard"
                                class="px-3 py-2 text-sm font-medium rounded-md"
                                :class="$route.path === '/dashboard' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-gray-900'"
                            >
                                Dashboard
                            </router-link>
                            <router-link
                                to="/expenses"
                                class="px-3 py-2 text-sm font-medium rounded-md"
                                :class="$route.path === '/expenses' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-gray-900'"
                            >
                                My Expenses
                            </router-link>
                            <router-link
                                to="/budget"
                                class="px-3 py-2 text-sm font-medium rounded-md"
                                :class="$route.path === '/budget' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-gray-900'"
                            >
                                Budget
                            </router-link>
                            <router-link
                                to="/expenses/new"
                                class="px-3 py-2 text-sm font-medium rounded-md"
                                :class="$route.path === '/expenses/new' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-gray-900'"
                            >
                                New Expense
                            </router-link>
                        </template>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">{{ authStore.user?.name }}</span>
                    <span
                        v-if="authStore.isAdmin"
                        class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 rounded-full"
                    >
                        Admin
                    </span>
                    <button
                        @click="handleLogout"
                        class="text-sm text-gray-600 hover:text-gray-900"
                    >
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const handleLogout = async () => {
    await authStore.logout();
    router.push('/login');
};
</script>
