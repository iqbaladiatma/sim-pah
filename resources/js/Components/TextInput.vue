<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-full border-gray-300 shadow-sm focus:border-pail-gold focus:ring-pail-gold dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-pail-gold dark:focus:ring-pail-gold"
        v-model="model"
        ref="input"
    />
</template>
