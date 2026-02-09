<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { formatRupiah } from "@/Utils/format";
import { ref, watch, onMounted, onUnmounted } from "vue";
import PlusIcon from "@/Components/Icons/PlusIcon.vue";
import InboxIcon from "@/Components/Icons/InboxIcon.vue";
import ZapIcon from "@/Components/Icons/ZapIcon.vue";

import FolderIcon from "@/Components/Icons/FolderIcon.vue";

const props = defineProps({
    requests: Object,
    mode: {
        type: String,
        default: 'list'
    },
    institutions: Object, // For folder view
    institution: Object, // For list view
    stats: Object,
    filters: Object,
});

// Reactive filter states
const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || 'all');
const typeFilter = ref(props.filters?.type || 'all');

// Custom dropdown states
const showStatusDropdown = ref(false);
const showTypeDropdown = ref(false);

// Status options with colors
const statusOptions = [
    { value: 'all', label: 'Semua Status', color: 'bg-gray-400' },
    { value: 'pending', label: 'Tinjauan', color: 'bg-yellow-500' },
    { value: 'processed', label: 'Diproses', color: 'bg-blue-500' },
    { value: 'approved', label: 'Disetujui', color: 'bg-green-500' },
    { value: 'rejected', label: 'Ditolak', color: 'bg-red-500' },
    { value: 'completed', label: 'Selesai', color: 'bg-purple-500' },
];

// Type options with icons
const typeOptions = [
    { value: 'all', label: 'Semua Kategori', icon: 'grid' },
    { value: 'utilitas', label: 'Utilitas', icon: 'bolt' },
    { value: 'barang_habis_pakai', label: 'Barang Habis Pakai', icon: 'box' },
    { value: 'darurat', label: 'Darurat', icon: 'alert' },
];

const getSelectedStatus = () => statusOptions.find(o => o.value === statusFilter.value) || statusOptions[0];
const getSelectedType = () => typeOptions.find(o => o.value === typeFilter.value) || typeOptions[0];

const selectStatus = (value) => {
    statusFilter.value = value;
    showStatusDropdown.value = false;
};

const selectType = (value) => {
    typeFilter.value = value;
    showTypeDropdown.value = false;
};

// Close dropdowns on outside click
const closeDropdowns = (e) => {
    if (!e.target.closest('.status-dropdown')) showStatusDropdown.value = false;
    if (!e.target.closest('.type-dropdown')) showTypeDropdown.value = false;
};

onMounted(() => document.addEventListener('click', closeDropdowns));
onUnmounted(() => document.removeEventListener('click', closeDropdowns));

// Debounce for search
let searchTimeout = null;

const applyFilters = () => {
    router.get(route('admin.requests.index'), {
        search: search.value || undefined,
        status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
        type: typeFilter.value !== 'all' ? typeFilter.value : undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Watch search with debounce
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 400);
});

// Watch filters without debounce
watch([statusFilter, typeFilter], () => {
    applyFilters();
});

const clearFilters = () => {
    search.value = '';
    statusFilter.value = 'all';
    typeFilter.value = 'all';
    router.get(route('admin.requests.index'));
};

const getMonthName = (date) => {
    const months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];
    return months[new Date(date).getMonth()];
};

const getDay = (date) => {
    return new Date(date).getDate().toString().padStart(2, '0');
};

const getStatusLabel = (status) => {
    switch (status) {
        case "pending": return "Tinjauan";
        case "processed": return "Diproses";
        case "approved": return "Disetujui";
        case "rejected": return "Ditolak";
        case "completed": return "Selesai";
        default: return status;
    }
};

const getStatusClasses = (status) => {
    switch (status) {
        case "pending":
            return "bg-yellow-50 text-yellow-600 border-yellow-100 dark:bg-yellow-900/20 dark:text-yellow-400 dark:border-yellow-800/50";
        case "processed":
            return "bg-blue-50 text-blue-600 border-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800/50";
        case "approved":
            return "bg-green-50 text-green-600 border-green-100 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800/50";
        case "rejected":
            return "bg-red-50 text-red-600 border-red-100 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800/50";
        case "completed":
            return "bg-purple-50 text-purple-600 border-purple-100 dark:bg-purple-900/20 dark:text-purple-400 dark:border-purple-800/50";
        default:
            return "bg-gray-50 text-gray-600 border-gray-100";
    }
};

const getStatusDotColor = (status) => {
    switch (status) {
        case "pending": return "bg-yellow-500";
        case "processed": return "bg-blue-500";
        case "approved": return "bg-green-500";
        case "rejected": return "bg-red-500";
        case "completed": return "bg-purple-500";
        default: return "bg-gray-500";
    }
};

const getTypeLabel = (type) => {
    switch (type) {
        case "utilitas": return "Utilitas";
        case "barang_habis_pakai": return "Barang Habis Pakai";
        case "darurat": return "Darurat";
        default: return type;
    }
};

const hasActiveFilters = () => {
    return search.value || statusFilter.value !== 'all' || typeFilter.value !== 'all';
};
</script>

<template>
    <Head title="Manajemen Pengajuan (Admin)" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-4 sm:px-0">
                <div class="flex items-center gap-4">
                    <div class="w-1.5 h-8 md:w-2 md:h-10 bg-pail-gold rounded-full shrink-0"></div>
                    <div>
                        <h2 class="text-xl md:text-2xl font-black text-gray-800 dark:text-gray-200 uppercase tracking-tighter leading-tight">
                            Manajemen Pengajuan
                        </h2>
                        <p class="text-[8px] md:text-[10px] font-black text-pail-gold uppercase tracking-widest mt-1">Sistem Informasi Manajemen Unit Rumah Tangga</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
                     <span class="hidden lg:flex px-5 py-2.5 rounded-xl bg-gray-900 text-white text-[10px] font-black uppercase tracking-widest border border-black shadow-lg shadow-black/20">
                        Total Audit: {{ requests.total }}
                    </span>
                    <a 
                        :href="route('admin.requests.export')" 
                        class="px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all shadow-lg shadow-green-600/20 font-black text-[10px] sm:text-xs uppercase tracking-widest flex items-center justify-center gap-2 group"
                    >
                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export Excel
                    </a>
                    <Link 
                        :href="route('admin.requests.create')" 
                        class="px-6 py-3 bg-pail-gold text-white rounded-xl hover:bg-yellow-600 transition-all shadow-lg shadow-pail-gold/20 font-black text-[10px] sm:text-xs uppercase tracking-widest flex items-center justify-center gap-2"
                    >
                        <PlusIcon class="w-4 h-4" />
                        Buat Baru
                    </Link>
                </div>
            </div>
        </template>

        <div class="pt-6 pb-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-2 gap-3 sm:gap-6 px-4 sm:px-0">
                    <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                        <div class="relative z-10">
                            <h3 class="text-gray-400 font-black uppercase tracking-widest text-[8px] sm:text-[9px] mb-1">Menunggu Review</h3>
                            <div class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tighter">{{ stats.pending }}</div>
                            <div class="text-[8px] sm:text-[9px] font-black text-yellow-600 uppercase tracking-widest mt-1.5">Butuh Persetujuan</div>
                        </div>
                        <div class="absolute -right-4 -bottom-4 w-16 sm:w-20 h-16 sm:h-20 bg-yellow-50 dark:bg-yellow-900/10 rounded-full group-hover:scale-110 transition-transform duration-700"></div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                        <div class="relative z-10">
                            <h3 class="text-gray-400 font-black uppercase tracking-widest text-[8px] sm:text-[9px] mb-1">Anggaran Pending</h3>
                            <div class="text-base sm:text-xl font-black text-gray-900 dark:text-white font-mono tracking-tighter">{{ formatRupiah(stats.total_cost_pending) }}</div>
                            <div class="text-[8px] sm:text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1.5 italic">Estimasi Serapan</div>
                        </div>
                        <div class="absolute -right-4 -bottom-4 w-16 sm:w-20 h-16 sm:h-20 bg-gray-50 dark:bg-gray-900/20 rounded-full group-hover:scale-110 transition-transform duration-700"></div>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-green-600 p-4 sm:p-6 rounded-2xl sm:rounded-3xl shadow-xl shadow-green-500/10 relative overflow-hidden group">
                        <div class="relative z-10">
                            <h3 class="text-white/70 font-black uppercase tracking-widest text-[8px] sm:text-[9px] mb-1">Realisasi Anggaran</h3>
                            <div class="text-base sm:text-xl font-black text-white font-mono tracking-tighter">{{ formatRupiah(stats.total_cost_approved) }}</div>
                            <div class="text-[8px] sm:text-[9px] font-black text-white uppercase tracking-widest mt-1.5">{{ stats.approved }} Pengajuan Disetujui</div>
                        </div>
                        <div class="absolute -right-4 -bottom-4 w-16 sm:w-20 h-16 sm:h-20 bg-white/10 rounded-full group-hover:scale-110 transition-transform duration-700"></div>
                    </div>

                    <div class="bg-gradient-to-br from-gray-900 to-black p-4 sm:p-6 rounded-2xl sm:rounded-3xl shadow-xl relative overflow-hidden group">
                        <div class="relative z-10">
                            <h3 class="text-gray-500 font-black uppercase tracking-widest text-[8px] sm:text-[9px] mb-1">Efisiensi Biaya</h3>
                            <div class="text-xl sm:text-xl font-black text-white tracking-tighter">{{ stats.rejected }} DITOLAK</div>
                            <div class="text-[8px] sm:text-[9px] font-black text-pail-gold uppercase tracking-widest mt-1.5">Optimalisasi Aset</div>
                        </div>
                        <div class="absolute -right-4 -bottom-4 w-16 sm:w-20 h-16 sm:h-20 bg-white/5 rounded-full group-hover:scale-110 transition-transform duration-700"></div>
                    </div>
                </div>

                <!-- Search & Filter Section -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700 shadow-sm mx-4 sm:mx-0">
                    <div class="flex flex-col lg:flex-row gap-4">
                        <!-- Search Box -->
                        <div class="flex-1 relative">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input 
                                v-model="search"
                                type="text" 
                                placeholder="Cari judul, deskripsi, atau lembaga..."
                                class="w-full pl-12 pr-4 py-3.5 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-pail-gold focus:border-pail-gold text-sm font-medium text-gray-900 dark:text-white placeholder-gray-400 transition-all"
                            />
                            <button 
                                v-if="search"
                                @click="search = ''"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Filters -->
                        <div class="flex flex-col sm:flex-row gap-3">
                            <!-- Status Filter (Custom Dropdown) -->
                            <div class="relative status-dropdown">
                                <button 
                                    @click.stop="showStatusDropdown = !showStatusDropdown"
                                    class="w-full sm:w-52 px-4 py-3.5 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl text-sm font-bold text-gray-700 dark:text-gray-300 cursor-pointer transition-all hover:border-pail-gold flex items-center justify-between gap-3"
                                    :class="{ 'ring-2 ring-pail-gold border-pail-gold': showStatusDropdown }"
                                >
                                    <div class="flex items-center gap-3">
                                        <span :class="[getSelectedStatus().color, 'w-3 h-3 rounded-full shrink-0']"></span>
                                        <span class="truncate">{{ getSelectedStatus().label }}</span>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform" :class="{ 'rotate-180': showStatusDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <!-- Dropdown Menu -->
                                <div 
                                    v-show="showStatusDropdown"
                                    class="absolute z-50 top-full left-0 right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-2xl overflow-hidden"
                                >
                                    <button
                                        v-for="option in statusOptions"
                                        :key="option.value"
                                        @click="selectStatus(option.value)"
                                        class="w-full px-4 py-3 flex items-center gap-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors"
                                        :class="{ 'bg-pail-gold/10 text-pail-gold': statusFilter === option.value }"
                                    >
                                        <span :class="[option.color, 'w-3 h-3 rounded-full shrink-0']"></span>
                                        <span>{{ option.label }}</span>
                                        <svg v-if="statusFilter === option.value" class="w-4 h-4 ml-auto text-pail-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Type Filter (Custom Dropdown) -->
                            <div class="relative type-dropdown">
                                <button 
                                    @click.stop="showTypeDropdown = !showTypeDropdown"
                                    class="w-full sm:w-56 px-4 py-3.5 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl text-sm font-bold text-gray-700 dark:text-gray-300 cursor-pointer transition-all hover:border-pail-gold flex items-center justify-between gap-3"
                                    :class="{ 'ring-2 ring-pail-gold border-pail-gold': showTypeDropdown }"
                                >
                                    <div class="flex items-center gap-3">
                                        <!-- Grid Icon for All -->
                                        <svg v-if="getSelectedType().icon === 'grid'" class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                        </svg>
                                        <!-- Bolt Icon for Utilitas -->
                                        <svg v-else-if="getSelectedType().icon === 'bolt'" class="w-4 h-4 text-yellow-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                        <!-- Box Icon for Barang Habis Pakai -->
                                        <svg v-else-if="getSelectedType().icon === 'box'" class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        <!-- Alert Icon for Darurat -->
                                        <svg v-else-if="getSelectedType().icon === 'alert'" class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        <span class="truncate">{{ getSelectedType().label }}</span>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform" :class="{ 'rotate-180': showTypeDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <!-- Dropdown Menu -->
                                <div 
                                    v-show="showTypeDropdown"
                                    class="absolute z-50 top-full left-0 right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-2xl overflow-hidden"
                                >
                                    <button
                                        v-for="option in typeOptions"
                                        :key="option.value"
                                        @click="selectType(option.value)"
                                        class="w-full px-4 py-3 flex items-center gap-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors"
                                        :class="{ 'bg-pail-gold/10 text-pail-gold': typeFilter === option.value }"
                                    >
                                        <!-- Grid Icon -->
                                        <svg v-if="option.icon === 'grid'" class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                        </svg>
                                        <!-- Bolt Icon -->
                                        <svg v-else-if="option.icon === 'bolt'" class="w-4 h-4 text-yellow-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                        <!-- Box Icon -->
                                        <svg v-else-if="option.icon === 'box'" class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        <!-- Alert Icon -->
                                        <svg v-else-if="option.icon === 'alert'" class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        <span>{{ option.label }}</span>
                                        <svg v-if="typeFilter === option.value" class="w-4 h-4 ml-auto text-pail-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Clear Filters -->
                            <button 
                                v-if="hasActiveFilters()"
                                @click="clearFilters"
                                class="px-4 py-3.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-red-100 dark:hover:bg-red-900/30 transition-all flex items-center justify-center gap-2 border border-red-100 dark:border-red-800/50"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Reset
                            </button>
                        </div>
                    </div>

                    <!-- Active Filters Tags -->
                    <div v-if="hasActiveFilters()" class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Filter Aktif:</span>
                        <span v-if="search" class="px-3 py-1 bg-pail-gold/10 text-pail-gold rounded-lg text-[10px] font-black uppercase tracking-wider border border-pail-gold/20">
                            "{{ search }}"
                        </span>
                        <span v-if="statusFilter !== 'all'" class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-black uppercase tracking-wider border border-blue-100">
                            {{ getStatusLabel(statusFilter) }}
                        </span>
                        <span v-if="typeFilter !== 'all'" class="px-3 py-1 bg-purple-50 text-purple-600 rounded-lg text-[10px] font-black uppercase tracking-wider border border-purple-100">
                            {{ getTypeLabel(typeFilter) }}
                        </span>
                    </div>
                </div>

                <!-- Section Header -->
                <div class="flex items-center gap-4 sm:gap-6 px-4 sm:px-0">
                    <h2 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em] sm:tracking-[0.5em] whitespace-nowrap">Daftar Pengajuan</h2>
                    <div class="flex-1 h-px bg-gray-100 dark:bg-gray-800"></div>
                    <div class="flex items-center gap-2">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest hidden sm:inline">{{ requests.data.length }} dari {{ requests.total }}</span>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 flex items-center justify-center">
                            <ZapIcon className="w-4 h-4 sm:w-5 sm:h-5 text-pail-gold" />
                        </div>
                    </div>
                </div>

                <!-- Premium Proposal Row List -->
                <div class="bg-white dark:bg-gray-800 rounded-[2rem] sm:rounded-[3rem] p-4 sm:p-8 border border-gray-100 dark:border-gray-700 shadow-sm mx-4 sm:mx-0">
                    <div class="space-y-4">
                        <!-- List Items -->
                        <div 
                            v-for="req in requests.data" 
                            :key="req.id"
                            class="p-4 sm:p-6 bg-gray-50 dark:bg-gray-900/50 rounded-2xl sm:rounded-3xl flex flex-col gap-4 group hover:bg-white dark:hover:bg-gray-900 border border-transparent hover:border-gray-100 dark:hover:border-gray-700 transition-all cursor-pointer shadow-none hover:shadow-2xl"
                        >
                            <!-- Mobile Layout -->
                            <div class="flex items-start gap-4 w-full md:hidden">
                                <!-- Date Box -->
                                <div class="w-14 h-14 rounded-xl bg-gray-900 flex flex-col items-center justify-center border-2 border-pail-gold/30 shadow-xl shrink-0 group-hover:scale-105 transition-transform">
                                    <span class="text-[7px] font-black text-pail-gold uppercase leading-none mb-0.5 opacity-70">{{ getMonthName(req.created_at) }}</span>
                                    <span class="text-xl font-black text-white leading-none tracking-tighter">{{ getDay(req.created_at) }}</span>
                                </div>
                                
                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-1.5">
                                        <span class="px-2 py-0.5 bg-white dark:bg-gray-800 rounded-md text-[8px] font-black text-gray-400 uppercase tracking-wider border border-gray-100 dark:border-gray-700">ID: #RE-{{ req.id.toString().padStart(4, '0') }}</span>
                                        <span :class="[getStatusClasses(req.status), 'px-2 py-0.5 rounded-lg text-[8px] font-black uppercase tracking-wider border flex items-center gap-1']">
                                            <span :class="[getStatusDotColor(req.status), 'w-1.5 h-1.5 rounded-full', req.status === 'pending' ? 'animate-pulse' : '']"></span>
                                            {{ getStatusLabel(req.status) }}
                                        </span>
                                    </div>
                                    <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight leading-tight mb-1.5 line-clamp-2 group-hover:text-pail-gold transition-colors">{{ req.title }}</h4>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <div class="flex items-center gap-1.5">
                                            <div class="w-1 h-1 rounded-full bg-pail-gold animate-pulse"></div>
                                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-wide">{{ req.institution?.name || 'Unknown' }}</span>
                                        </div>
                                        <div class="w-0.5 h-0.5 rounded-full bg-gray-300 hidden sm:block"></div>
                                        <span class="text-[9px] font-black text-pail-gold uppercase tracking-wide hidden sm:inline">{{ getTypeLabel(req.type) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Mobile Bottom Section -->
                            <div class="flex items-center justify-between gap-4 pt-3 border-t border-gray-100 dark:border-gray-800 md:hidden">
                                <div>
                                    <p class="text-[8px] font-black text-gray-300 uppercase tracking-wider mb-0.5">Estimasi</p>
                                    <p class="text-lg font-black text-gray-900 dark:text-white font-mono tracking-tighter">{{ formatRupiah(req.estimated_cost) }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a 
                                        v-if="req.photo_evidence" 
                                        :href="`/storage/${req.photo_evidence}`" 
                                        target="_blank" 
                                        class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-400 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all border border-gray-200 dark:border-gray-700"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </a>
                                    <Link 
                                        :href="route('admin.requests.edit', req.id)"
                                        class="px-5 py-2.5 bg-gray-900 text-white rounded-xl hover:bg-pail-gold transition-all shadow-lg font-black text-[9px] uppercase tracking-widest flex items-center gap-2 group/btn"
                                    >
                                        {{ req.status === 'pending' ? 'Tinjau' : 'Detail' }}
                                        <svg class="w-3 h-3 group-hover/btn:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                                    </Link>
                                </div>
                            </div>

                            <!-- Desktop Layout -->
                            <div class="hidden md:flex items-center justify-between gap-8 w-full">
                                <div class="flex items-center gap-6 flex-1">
                                    <!-- Date Box -->
                                    <div class="w-16 h-16 rounded-2xl bg-gray-900 flex flex-col items-center justify-center border-2 border-pail-gold/30 shadow-xl shrink-0 group-hover:scale-110 transition-transform">
                                        <span class="text-[8px] font-black text-pail-gold uppercase leading-none mb-1 opacity-70">{{ getMonthName(req.created_at) }}</span>
                                        <span class="text-2xl font-black text-white leading-none tracking-tighter">{{ getDay(req.created_at) }}</span>
                                    </div>
                                    
                                    <!-- Content -->
                                    <div class="min-w-0 flex-1">
                                        <span class="px-3 py-1 bg-white dark:bg-gray-800 rounded-lg text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] border border-gray-100 dark:border-gray-700 mb-2 inline-block">ID: #RE-{{ req.id.toString().padStart(4, '0') }}</span>
                                        <h4 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-none mb-2 truncate group-hover:text-pail-gold transition-colors">{{ req.title }}</h4>
                                        <div class="flex items-center gap-4">
                                            <div class="flex items-center gap-2">
                                                <div class="w-1.5 h-1.5 rounded-full bg-pail-gold animate-pulse"></div>
                                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ req.institution?.name || 'Unknown' }}</span>
                                            </div>
                                            <div class="w-1 h-1 rounded-full bg-gray-200"></div>
                                            <span class="text-[10px] font-black text-pail-gold uppercase tracking-widest">Kategori: {{ getTypeLabel(req.type) }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Right Section -->
                                <div class="flex items-center gap-6 lg:gap-10 shrink-0">
                                    <div class="text-right">
                                        <p class="text-[9px] font-black text-gray-300 uppercase tracking-[0.2em] mb-1">Estimasi</p>
                                        <p class="text-2xl font-black text-gray-900 dark:text-white font-mono tracking-tighter">{{ formatRupiah(req.estimated_cost) }}</p>
                                    </div>
                                    
                                    <div :class="[getStatusClasses(req.status), 'px-5 py-2 rounded-2xl text-[10px] font-black uppercase tracking-widest border flex items-center gap-2']">
                                        <span :class="[getStatusDotColor(req.status), 'w-2 h-2 rounded-full', req.status === 'pending' ? 'animate-pulse' : '']"></span>
                                        {{ getStatusLabel(req.status) }}
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <a 
                                            v-if="req.photo_evidence" 
                                            :href="`/storage/${req.photo_evidence}`" 
                                            target="_blank" 
                                            class="w-11 h-11 rounded-xl bg-gray-100 dark:bg-gray-900 text-gray-400 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm border border-gray-200 dark:border-gray-700"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </a>
                                        <Link 
                                            :href="route('admin.requests.edit', req.id)"
                                            class="h-11 px-6 bg-gray-900 text-white rounded-xl hover:bg-pail-gold transition-all shadow-xl shadow-black/10 font-black text-[9px] uppercase tracking-[0.2em] flex items-center gap-2 group/btn"
                                        >
                                            {{ req.status === 'pending' ? 'Proses' : 'Detail' }}
                                            <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-if="requests.data.length === 0" class="py-20 sm:py-32 flex flex-col items-center justify-center">
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center mb-6 border border-gray-100 dark:border-gray-700">
                                <InboxIcon className="w-10 h-10 sm:w-12 sm:h-12 text-gray-300 dark:text-gray-600" />
                            </div>
                            <p class="text-gray-400 font-black uppercase tracking-widest text-[10px] sm:text-xs text-center leading-loose max-w-xs">
                                {{ hasActiveFilters() ? 'Tidak ada pengajuan yang sesuai dengan filter.' : 'Antrian pengajuan kosong. Seluruh permohonan telah selesai ditinjau.' }}
                            </p>
                            <button 
                                v-if="hasActiveFilters()"
                                @click="clearFilters"
                                class="mt-6 px-6 py-3 bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-pail-gold hover:text-white transition-all"
                            >
                                Reset Filter
                            </button>
                            <Link 
                                v-else
                                :href="route('admin.requests.create')" 
                                class="mt-8 px-8 py-4 bg-gray-900 text-pail-gold rounded-2xl font-black text-[10px] uppercase tracking-widest flex items-center gap-3 hover:bg-pail-gold hover:text-white transition-all shadow-2xl"
                            >
                                <PlusIcon class="w-4 h-4" />
                                Buat Pengajuan Baru
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="requests.links && requests.links.length > 3" class="flex items-center justify-center gap-2 px-4 sm:px-0">
                    <template v-for="(link, index) in requests.links" :key="index">
                        <Link 
                            v-if="link.url"
                            :href="link.url"
                            class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center font-black text-xs transition-all"
                            :class="link.active 
                                ? 'bg-pail-gold text-white shadow-lg shadow-pail-gold/30' 
                                : 'bg-white dark:bg-gray-800 text-gray-400 border border-gray-100 dark:border-gray-700 hover:border-pail-gold hover:text-pail-gold'"
                            v-html="link.label"
                        />
                        <span 
                            v-else 
                            class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center font-black text-xs bg-gray-50 dark:bg-gray-900 text-gray-300 cursor-not-allowed"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
