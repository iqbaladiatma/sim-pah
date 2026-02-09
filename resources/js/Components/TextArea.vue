<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: String,
    rows: {
        type: Number,
        default: 3,
    },
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <textarea
        class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-pail-gold focus:ring-pail-gold dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-pail-gold dark:focus:ring-pail-gold transition-all duration-300 placeholder:text-gray-300 dark:placeholder:text-gray-600"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
        :rows="rows"
    ></textarea>
</template>
