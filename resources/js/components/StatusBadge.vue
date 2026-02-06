<template>
    <span
        :class="[
            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
            statusClasses
        ]"
    >
        {{ label }}
    </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        required: true,
    },
});

const statusClasses = computed(() => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        approved: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800',
    };
    return classes[props.status] || 'bg-gray-100 text-gray-800';
});

const label = computed(() => {
    return props.status.charAt(0).toUpperCase() + props.status.slice(1);
});
</script>
