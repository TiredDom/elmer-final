<template>
    <div class="min-h-screen" :class="authStore.isAuthenticated ? 'bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500' : 'bg-gray-50'">
        <AppNavbar v-if="authStore.isAuthenticated" />
        <main :class="authStore.isAuthenticated ? 'pb-8' : ''">
            <router-view />
        </main>
    </div>
</template>

<script setup>
import { useAuthStore } from './stores/auth';
import AppNavbar from './components/AppNavbar.vue';

const authStore = useAuthStore();
</script>
