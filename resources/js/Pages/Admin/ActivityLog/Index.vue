<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import Pagination from "@/Components/Pagination.vue";
import ConfirmModal from "@/Components/ConfirmModal.vue";

const props = defineProps({
    activities: Object,
    filters: Object,
    stats: {
        type: Object,
        default: () => ({ total: 0, today: 0, this_week: 0 }),
    },
});

const filterForm = ref({
    user_id: props.filters.user_id || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
    event: props.filters.event || '',
    subject_type: props.filters.subject_type || '',
    role: props.filters.role || '',
});

watch(filterForm, (newFilters) => {
    router.get(route('admin.activity_log.index'), newFilters, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}, { deep: true });

// Clear Logs Confirmation Modal State
const showClearModal = ref(false);

const confirmClearLogs = () => {
    showClearModal.value = true;
};

const executeClearLogs = () => {
    router.delete(route('admin.activity_log.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            showClearModal.value = false;
        }
    });
};

const closeClearModal = () => {
    showClearModal.value = false;
};

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
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                    Activity Log Sistem
                </h2>
                <div class="flex items-center gap-4">
                    <button v-if="Object.values(filters).some(x => x)" @click="router.visit(route('admin.activity_log.index'))" class="text-[10px] font-black text-red-500 uppercase tracking-widest hover:bg-red-50 px-4 py-2 rounded-xl transition-all">
                        Reset Filter
                    </button>
                    <button @click="confirmClearLogs" class="px-6 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all shadow-lg shadow-red-600/20 font-black text-xs uppercase tracking-widest flex items-center gap-2">
                        🗑️ Bersihkan Log
                    </button>
                </div>
            </div>
        </template>

        <div class="pt-6 pb-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
                
                <!-- Stats Overview -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm">
                        <h3 class="text-gray-400 font-black uppercase tracking-widest text-[10px] mb-1">Total Log</h3>
                        <div class="text-4xl font-black tracking-tighter">{{ stats.total }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm text-blue-600">
                        <h3 class="text-gray-400 font-black uppercase tracking-widest text-[10px] mb-1">Hari Ini</h3>
                        <div class="text-4xl font-black tracking-tighter">{{ stats.today }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm text-green-600">
                        <h3 class="text-gray-400 font-black uppercase tracking-widest text-[10px] mb-1">Minggu Ini</h3>
                        <div class="text-4xl font-black tracking-tighter">{{ stats.this_week }}</div>
                    </div>
                    <div class="hidden md:block bg-gradient-to-br from-gray-900 to-black p-8 rounded-[2.5rem] shadow-xl col-span-1">
                        <div class="flex flex-col justify-center h-full">
                            <h3 class="text-gray-400 font-black uppercase tracking-widest text-[10px] mb-1">Audit Trail</h3>
                            <div class="text-sm font-black text-white uppercase tracking-widest leading-tight">Keamanan Data Terjamin</div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Filter Role</label>
                            <select v-model="filterForm.role" class="w-full h-12 border-gray-100 rounded-xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-xs font-black uppercase tracking-widest focus:ring-pail-gold">
                                <option value="">Semua Role</option>
                                <option value="admin">Admin & Superadmin</option>
                                <option value="lembaga">Lembaga</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Jenis Event</label>
                            <select v-model="filterForm.event" class="w-full h-12 border-gray-100 rounded-xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-xs font-black uppercase tracking-widest focus:ring-pail-gold">
                                <option value="">Semua Event</option>
                                <option value="created">Created</option>
                                <option value="updated">Updated</option>
                                <option value="deleted">Deleted</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Rentang Waktu</label>
                            <div class="flex items-center gap-3">
                                <input v-model="filterForm.date_from" type="date" class="flex-1 h-12 border-gray-100 rounded-xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-xs font-black focus:ring-pail-gold">
                                <span class="text-gray-300 font-black uppercase text-[10px]">s/d</span>
                                <input v-model="filterForm.date_to" type="date" class="flex-1 h-12 border-gray-100 rounded-xl bg-gray-50/50 dark:bg-gray-900 dark:border-gray-700 text-xs font-black focus:ring-pail-gold">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Desktop Table View -->
                <div class="hidden md:block bg-white dark:bg-gray-800 shadow-2xl rounded-[3rem] border border-gray-100 dark:border-gray-700 overflow-hidden text-sm">
                    <div class="p-8">
                        <table class="min-w-full">
                            <thead>
                                <tr class="text-[10px] font-black uppercase text-gray-400 tracking-[0.2em] border-b border-gray-100 dark:border-gray-700">
                                    <th class="px-6 py-6 text-left">Event</th>
                                    <th class="px-6 py-6 text-left">Pelaksana</th>
                                    <th class="px-6 py-6 text-left">Subjek</th>
                                    <th class="px-6 py-6 text-left">Deskripsi</th>
                                    <th class="px-6 py-6 text-left">Timestamp</th>
                                    <th class="px-6 py-6 text-right">Opsi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                <tr v-for="activity in activities.data" :key="activity.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-all group">
                                    <td class="px-6 py-8">
                                        <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest inline-flex items-center gap-2 border shadow-sm"
                                            :class="getEventColor(activity.event)">
                                            <span>{{ getEventIcon(activity.event) }}</span>
                                            {{ activity.event }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-8">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center font-black text-gray-600 dark:text-gray-300 group-hover:bg-pail-gold group-hover:text-white transition-all shadow-sm">
                                                {{ activity.causer?.name?.charAt(0) || '?' }}
                                            </div>
                                            <div>
                                                <div class="font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{ activity.causer?.name || 'SYSTEM' }}</div>
                                                <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ activity.causer?.role || '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-8 text-gray-400 font-black uppercase tracking-widest text-[9px]">
                                        {{ activity.subject_type?.split('\\').pop() || '-' }}
                                    </td>
                                    <td class="px-6 py-8 font-medium text-gray-500 max-w-[200px] truncate group-hover:text-gray-900 dark:group-hover:text-white transition-colors">
                                        {{ activity.description }}
                                    </td>
                                    <td class="px-6 py-8 text-gray-400 font-mono tracking-tighter text-xs">
                                        {{ new Date(activity.created_at).toLocaleString('id-ID') }}
                                    </td>
                                    <td class="px-6 py-8 text-right">
                                        <button v-if="activity.properties && Object.keys(activity.properties).length > 0"
                                            @click="viewDetails(activity)"
                                            class="px-5 py-2 bg-gray-900 text-white rounded-xl hover:bg-black transition-all shadow-lg font-black text-[9px] uppercase tracking-widest">
                                            Lihat Detail
                                        </button>
                                        <span v-else class="text-[10px] font-black text-gray-200 uppercase tracking-widest">Tanpa Meta</span>
                                    </td>
                                </tr>
                                <tr v-if="activities.data.length === 0">
                                    <td colspan="6" class="px-6 py-20 text-center">
                                        <div class="text-gray-400 font-black uppercase tracking-widest text-xs italic">Log aktivitas kosong</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile View -->
                <div class="md:hidden space-y-6">
                    <div v-for="activity in activities.data" :key="activity.id" class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-8">
                            <div class="flex items-center justify-between mb-6">
                                <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest inline-flex items-center gap-2 border shadow-sm"
                                    :class="getEventColor(activity.event)">
                                    {{ getEventIcon(activity.event) }} {{ activity.event }}
                                </span>
                                <span class="text-[10px] font-mono text-gray-400">{{ new Date(activity.created_at).toLocaleString('id-ID', { dateStyle: 'short', timeStyle: 'short' }) }}</span>
                            </div>
                            
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-12 h-12 rounded-2xl bg-pail-gold text-white flex items-center justify-center font-black text-xl shadow-lg shadow-pail-gold/20">
                                    {{ activity.causer?.name?.charAt(0) || '?' }}
                                </div>
                                <div>
                                    <div class="font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{ activity.causer?.name || 'SYSTEM' }}</div>
                                    <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ activity.causer?.role || '-' }}</div>
                                </div>
                            </div>

                            <p class="text-sm font-medium text-gray-500 mb-6 italic">"{{ activity.description }}"</p>
                            
                            <button v-if="activity.properties && Object.keys(activity.properties).length > 0"
                                @click="viewDetails(activity)"
                                class="w-full py-4 bg-gray-900 text-white rounded-2xl shadow-xl shadow-black/10 font-black text-[10px] uppercase tracking-widest transition-all active:scale-95">
                                Lihat Meta Data Perubahan
                            </button>
                        </div>
                    </div>
                    <div v-if="activities.data.length === 0" class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-20 text-center border border-gray-100 dark:border-gray-700 italic text-gray-400 font-black uppercase tracking-widest text-xs">Log aktivitas kosong</div>
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    <Pagination :links="activities.links" />
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <div v-if="isDetailModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-xl p-4" @click="closeModal">
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] shadow-2xl w-full max-w-2xl p-12 border border-white/20 relative overflow-hidden" @click.stop>
                <div class="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-pail-gold opacity-5 rounded-full blur-[100px]"></div>
                
                <header class="relative z-10 flex items-center justify-between mb-10">
                    <div>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter uppercase mb-1">Log Audit Meta</h3>
                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest italic">Rincian parameter perubahan objek sistem.</p>
                    </div>
                    <button @click="closeModal" class="w-12 h-12 rounded-2xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-gray-400 hover:text-red-500 transition-all border border-gray-100 dark:border-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </header>

                <div v-if="selectedActivity" class="relative z-10 space-y-8">
                    <div class="grid grid-cols-2 gap-8 p-8 bg-gray-50 dark:bg-gray-900 rounded-[2.5rem] border border-gray-100 dark:border-gray-800">
                        <div>
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Aksi Sistem</span>
                            <span class="px-3 py-1 rounded-xl text-[9px] font-black uppercase tracking-widest inline-flex items-center gap-2 border shadow-sm"
                                :class="getEventColor(selectedActivity.event)">
                                {{ getEventIcon(selectedActivity.event) }} {{ selectedActivity.event }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Inisiator</span>
                            <span class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{ selectedActivity.causer?.name || 'SYSTEM CORE' }}</span>
                        </div>
                    </div>

                    <div class="p-8 bg-pail-gold/5 rounded-[2.5rem] border border-pail-gold/10">
                        <h4 class="text-[10px] font-black text-pail-gold uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-pail-gold animate-pulse"></span>
                            Perubahan Data Objek
                        </h4>
                        <div class="space-y-4 max-h-[300px] overflow-y-auto pr-4 custom-scrollbar">
                            <div v-for="(value, key) in selectedActivity.properties" :key="key" class="group flex flex-col gap-1 p-4 bg-white dark:bg-gray-900/80 rounded-2xl border border-pail-gold/10 shadow-sm">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ key.replace(/_/g, ' ') }}</span>
                                <span class="text-xs font-black text-gray-900 dark:text-white break-words lowercase italic text-right">{{ value }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-center pt-8 border-t border-gray-100 dark:border-gray-800">
                        <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest">Waktu Eksekusi: {{ new Date(selectedActivity.created_at).toLocaleString('id-ID', { dateStyle: 'full', timeStyle: 'medium' }) }}</span>
                    </div>
                </div>

                <div class="relative z-10 mt-10">
                    <button @click="closeModal" class="w-full py-5 bg-gray-900 text-white rounded-2xl hover:bg-black font-black uppercase tracking-[0.2em] text-[10px] transition-all shadow-xl shadow-black/10">
                        Selesai Meninjau
                    </button>
                </div>
            </div>
        </div>

        <!-- Clear Logs Confirmation Modal -->
        <ConfirmModal
            :show="showClearModal"
            title="Hapus Semua Log"
            message="Apakah Anda yakin ingin menghapus SEMUA riwayat aktivitas? Tindakan ini tidak dapat dibatalkan dan akan menghapus seluruh audit trail sistem."
            confirm-text="Ya, Hapus Semua"
            cancel-text="Batal"
            variant="danger"
            @confirm="executeClearLogs"
            @close="closeClearModal"
        />
    </AuthenticatedLayout>
</template>
