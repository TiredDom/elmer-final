<template>
    <div class="mb-4">
        <label
            v-if="label"
            :for="id"
            class="block text-sm font-medium text-gray-700 mb-1"
        >
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        <input
            :id="id"
            :type="type"
            :value="modelValue"
            :placeholder="placeholder"
            :disabled="disabled"
            :required="required"
            @input="$emit('update:modelValue', $event.target.value)"
            :class="[
                'block w-full px-3 py-2 border rounded-lg shadow-sm text-sm transition-colors duration-200',
                'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500',
                error ? 'border-red-300 text-red-900 placeholder-red-300' : 'border-gray-300',
                { 'bg-gray-50 cursor-not-allowed': disabled }
            ]"
        />
        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
        <p v-if="hint && !error" class="mt-1 text-sm text-gray-500">{{ hint }}</p>
    </div>
</template>

<script setup>
defineProps({
    id: String,
    label: String,
    type: {
        type: String,
        default: 'text',
    },
    modelValue: [String, Number],
    placeholder: String,
    disabled: Boolean,
    required: Boolean,
    error: String,
    hint: String,
});

defineEmits(['update:modelValue']);
</script>
