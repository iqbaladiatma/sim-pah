<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        required: true,
    },
    value: {
        default: null,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <input
        type="checkbox"
        :value="value"
        v-model="proxyChecked"
        class="rounded-full border-gray-300 text-pail-gold shadow-sm focus:ring-pail-gold dark:border-gray-700 dark:bg-gray-900 dark:focus:ring-pail-gold dark:focus:ring-offset-gray-800"
    />
</template>
