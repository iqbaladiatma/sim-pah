<template>
    <svg :class="className" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" :stroke-width="strokeWidth" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
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
