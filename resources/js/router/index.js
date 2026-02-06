import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
    {
        path: '/',
        redirect: '/dashboard',
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('../views/auth/LoginView.vue'),
        meta: { guest: true },
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('../views/auth/RegisterView.vue'),
        meta: { guest: true },
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('../views/user/DashboardView.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/expenses',
        name: 'expenses',
        component: () => import('../views/user/ExpensesView.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/expenses/new',
        name: 'expenses.create',
        component: () => import('../views/user/ExpenseCreateView.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/admin',
        name: 'admin.dashboard',
        component: () => import('../views/admin/AdminDashboardView.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/expenses',
        name: 'admin.expenses',
        component: () => import('../views/admin/AdminExpensesView.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/budget',
        name: 'admin.budget',
        component: () => import('../views/admin/AdminBudgetView.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/history',
        name: 'admin.history',
        component: () => import('../views/admin/AdminHistoryView.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next({ name: 'login' });
        return;
    }

    if (to.meta.requiresAdmin && !authStore.isAdmin) {
        next({ name: 'dashboard' });
        return;
    }

    if (to.meta.guest && authStore.isAuthenticated) {
        if (authStore.isAdmin) {
            next({ name: 'admin.dashboard' });
        } else {
            next({ name: 'dashboard' });
        }
        return;
    }

    next();
});

export default router;
