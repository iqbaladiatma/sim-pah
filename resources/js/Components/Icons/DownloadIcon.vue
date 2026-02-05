<template>
    <svg :class="className" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" :stroke-width="strokeWidth" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
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
