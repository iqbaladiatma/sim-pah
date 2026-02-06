<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: Array,
});
</script>

<template>
    <div v-if="links.length > 3">
        <div class="flex flex-wrap justify-center mt-8 gap-1.5 sm:gap-2">
            <template v-for="(link, p) in links" :key="p">
                <div v-if="link.url === null" 
                    class="px-3 py-2 sm:px-4 sm:py-3 text-[10px] sm:text-xs leading-4 text-gray-400 border border-gray-100 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-900/50" 
                    v-html="link.label" 
                />
                <Link v-else
                    class="px-3 py-2 sm:px-4 sm:py-3 text-[10px] sm:text-xs leading-4 border rounded-xl transition-all duration-300"
                    :class="link.active 
                        ? 'bg-pail-gold text-white border-pail-gold font-bold shadow-lg shadow-pail-gold/20' 
                        : 'bg-white dark:bg-gray-800 border-gray-100 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:border-pail-gold hover:text-pail-gold'" 
                    :href="link.url" 
                    v-html="link.label" 
                />
            </template>
        </div>
    </div>
</template>
