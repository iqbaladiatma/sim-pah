<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: Array,
});
</script>

<template>
    <div v-if="links.length > 3">
        <div class="flex flex-wrap -mb-1 justify-center mt-6 gap-2">
            <template v-for="(link, p) in links" :key="p">
                <div v-if="link.url === null" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded-xl" v-html="link.label" />
                <Link v-else
                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded-xl hover:bg-white focus:border-pail-gold focus:text-pail-gold transition"
                    :class="{ 'bg-pail-gold text-white border-pail-gold font-bold': link.active }" :href="link.url" v-html="link.label" />
            </template>
        </div>
    </div>
</template>
