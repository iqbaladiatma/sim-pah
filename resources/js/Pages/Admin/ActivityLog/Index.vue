<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    activities: Object,
    filters: Object,
    stats: {
        type: Object,
        default: () => ({ total: 0, today: 0, this_week: 0 }),
    },
});

const selectedActivity = ref(null);
const isDetailModalOpen = ref(false);

const viewDetails = (activity) => {
    selectedActivity.value = activity;
    isDetailModalOpen.value = true;
};

const closeModal = () => {
    isDetailModalOpen.value = false;
    selectedActivity.value = null;
};

const getEventColor = (event) => {
    switch (event) {
        case 'created': return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        case 'updated': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
        case 'deleted': return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        default: return 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400';
    }
};

const getEventIcon = (event) => {
    switch (event) {
        case 'created': return '➕';
        case 'updated': return '✏️';
        case 'deleted': return '🗑️';
        default: return '📝';
    }
};
</script>

<template>
    <Head title="Activity Log" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                Activity Log Sistem
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                
                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border-l-4 border-pail-gold shadow-sm">
                        <h3 class="text-gray-400 font-black uppercase tracking-wider text-[10px] mb-2">Total Aktivitas</h3>
                        <div class="text-3xl font-black text-gray-900 dark:text-white">{{ stats.total }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border-l-4 border-blue-500 shadow-sm">
                        <h3 class="text-gray-400 font-black uppercase tracking-wider text-[10px] mb-2">Hari Ini</h3>
                        <div class="text-3xl font-black text-blue-600">{{ stats.today }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border-l-4 border-green-500 shadow-sm">
                        <h3 class="text-gray-400 font-black uppercase tracking-wider text-[10px] mb-2">Minggu Ini</h3>
                        <div class="text-3xl font-black text-green-600">{{ stats.this_week }}</div>
                    </div>
                </div>

                <!-- Activity List -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Riwayat Aktivitas</h3>
                        
                        <!-- Desktop Table -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                                <thead>
                                    <tr class="text-[10px] font-extrabold uppercase text-gray-500 tracking-wider bg-gray-50/80 dark:bg-gray-900/50">
                                        <th class="px-6 py-4 text-left">Event</th>
                                        <th class="px-6 py-4 text-left">User</th>
                                        <th class="px-6 py-4 text-left">Subject</th>
                                        <th class="px-6 py-4 text-left">Description</th>
                                        <th class="px-6 py-4 text-left">Waktu</th>
                                        <th class="px-6 py-4 text-center">Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                    <tr v-for="activity in activities.data" :key="activity.id" class="hover:bg-gray-50/80 dark:hover:bg-gray-900/30 transition text-sm">
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1"
                                                :class="getEventColor(activity.event)">
                                                <span>{{ getEventIcon(activity.event) }}</span>
                                                {{ activity.event }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-lg bg-pail-gold/10 text-pail-gold flex items-center justify-center font-black text-xs">
                                                    {{ activity.causer?.name?.charAt(0) || '?' }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-900 dark:text-white text-xs">{{ activity.causer?.name || 'System' }}</div>
                                                    <div class="text-[10px] text-gray-500">{{ activity.causer?.role || '-' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ activity.subject_type?.split('\\').pop() || '-' }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-xs text-gray-600 dark:text-gray-400">{{ activity.description }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-xs text-gray-500">{{ new Date(activity.created_at).toLocaleString('id-ID') }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button v-if="activity.properties && Object.keys(activity.properties).length > 0"
                                                @click="viewDetails(activity)"
                                                class="px-3 py-1.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition shadow-sm font-bold text-xs">
                                                👁️ View
                                            </button>
                                            <span v-else class="text-xs text-gray-400">-</span>
                                        </td>
                                    </tr>
                                    <tr v-if="activities.data.length === 0">
                                        <td colspan="6" class="px-6 py-20 text-center text-gray-400 italic font-medium">Belum ada aktivitas tercatat.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile View -->
                        <div class="md:hidden space-y-4">
                            <div v-for="activity in activities.data" :key="activity.id" class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1"
                                        :class="getEventColor(activity.event)">
                                        <span>{{ getEventIcon(activity.event) }}</span>
                                        {{ activity.event }}
                                    </span>
                                    <span class="text-[10px] text-gray-500">{{ new Date(activity.created_at).toLocaleString('id-ID', { dateStyle: 'short', timeStyle: 'short' }) }}</span>
                                </div>
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-8 h-8 rounded-lg bg-pail-gold/10 text-pail-gold flex items-center justify-center font-black text-xs">
                                        {{ activity.causer?.name?.charAt(0) || '?' }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-xs">{{ activity.causer?.name || 'System' }}</div>
                                        <div class="text-[10px] text-gray-500">{{ activity.causer?.role || '-' }}</div>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">{{ activity.description }}</p>
                                <button v-if="activity.properties && Object.keys(activity.properties).length > 0"
                                    @click="viewDetails(activity)"
                                    class="w-full py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-bold text-xs">
                                    👁️ View Details
                                </button>
                            </div>
                            <div v-if="activities.data.length === 0" class="p-16 text-center text-gray-400 italic font-medium">Belum ada aktivitas tercatat.</div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <Pagination :links="activities.links" />
            </div>
        </div>

        <!-- Detail Modal -->
        <div v-if="isDetailModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" @click="closeModal">
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-2xl p-8 border border-gray-100 dark:border-gray-700" @click.stop>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">📋 Detail Perubahan</h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div v-if="selectedActivity" class="space-y-4">
                    <!-- Activity Info -->
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4 border border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 font-medium block mb-1">Event</span>
                                <span class="px-2 py-1 rounded-lg text-[10px] font-black uppercase inline-flex items-center gap-1"
                                    :class="getEventColor(selectedActivity.event)">
                                    {{ getEventIcon(selectedActivity.event) }} {{ selectedActivity.event }}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 font-medium block mb-1">User</span>
                                <span class="text-gray-900 dark:text-white font-bold">{{ selectedActivity.causer?.name || 'System' }}</span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-gray-500 dark:text-gray-400 font-medium block mb-1">Description</span>
                                <span class="text-gray-900 dark:text-white">{{ selectedActivity.description }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Properties -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-2xl p-4 border border-blue-200 dark:border-blue-800">
                        <h4 class="font-bold text-blue-900 dark:text-blue-300 mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Detail Perubahan
                        </h4>
                        <div class="space-y-2">
                            <div v-for="(value, key) in selectedActivity.properties" :key="key" class="flex items-start justify-between py-2 border-b border-blue-200 dark:border-blue-800 last:border-0">
                                <span class="text-sm font-medium text-blue-700 dark:text-blue-400 capitalize">{{ key.replace(/_/g, ' ') }}:</span>
                                <span class="text-sm text-blue-900 dark:text-blue-200 font-bold ml-3 text-right">{{ value }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Timestamp -->
                    <div class="text-center pt-4 border-t border-gray-200 dark:border-gray-700">
                        <span class="text-xs text-gray-500">Waktu: {{ new Date(selectedActivity.created_at).toLocaleString('id-ID', { dateStyle: 'full', timeStyle: 'medium' }) }}</span>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button @click="closeModal" class="px-6 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-bold">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
