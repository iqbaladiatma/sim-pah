<template>
    <svg :class="className" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" :stroke-width="strokeWidth" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
</template>

<script setup>
defineProps({
    className: {
        type: String,
        default: 'w-5 h-5'
    },
    strokeWidth: {
        type: Number,
        default: 2
    }
});
</script>
