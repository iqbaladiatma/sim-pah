<template>
    <svg :class="className" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" :stroke-width="strokeWidth" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
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
