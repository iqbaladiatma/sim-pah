<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
    collapsed: {
        type: Boolean,
        default: false,
    },
});

const classes = computed(() => {
    const base = 'flex items-center rounded-xl transition-all duration-200';
    const padding = props.collapsed ? 'px-0 justify-center py-3' : 'px-4 py-3';
    const colors = props.active
        ? 'bg-pail-gold/10 text-pail-gold font-bold'
        : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white';
    
    return `${base} ${padding} ${colors}`;
});
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
