<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    stats: Object,
    recent_activity: Array,
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Admin Dashboard (URT)
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-8">
                    <div class="bg-pail-ivory dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-pail-gold">
                        <div class="text-gray-900 dark:text-gray-100 font-bold text-xl">Lembaga</div>
                        <div class="text-3xl font-bold text-pail-gold">{{ stats.total_institutions }}</div>
                    </div>
                    <div class="bg-pail-ivory dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                        <div class="text-gray-900 dark:text-gray-100 font-bold text-xl">Pending Requests</div>
                        <div class="text-3xl font-bold text-red-500">{{ stats.pending_requests }}</div>
                    </div>
                    <div class="bg-pail-ivory dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-orange-400">
                        <div class="text-gray-900 dark:text-gray-100 font-bold text-xl">Low Stock Items</div>
                        <div class="text-3xl font-bold text-orange-400">{{ stats.low_stock_items }}</div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-4">Recent Activity</h3>
                        <div v-if="recent_activity.length === 0" class="text-gray-500">No recent activity.</div>
                        <ul v-else class="divide-y divide-gray-200">
                            <li v-for="log in recent_activity" :key="log.id" class="py-2">
                                <span class="font-bold">{{ log.description }}</span>
                                <span class="text-sm text-gray-500 ml-2">({{ new Date(log.created_at).toLocaleString() }})</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
