<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

// Use local icons from the project
import LibraryIcon from '@/Components/Icons/PackageIcon.vue';
import ClipboardCheckIcon from '@/Components/Icons/CheckCircleIcon.vue';
import ToolsIcon from '@/Components/Icons/SettingsIcon.vue';
import ExclamationIcon from '@/Components/Icons/InfoIcon.vue';
import OfficeBuildingIcon from '@/Components/Icons/FolderIcon.vue';
import SwitchHorizontalIcon from '@/Components/Icons/SparklesIcon.vue';
import CashIcon from '@/Components/Icons/DiamondIcon.vue';
import TrashIcon from '@/Components/Icons/TrashIcon.vue';
import ShoppingCartIcon from '@/Components/Icons/PackageIcon.vue';
import LightningBoltIcon from '@/Components/Icons/ZapIcon.vue';
import SparklesIcon from '@/Components/Icons/SparklesIcon.vue';
import SunIcon from '@/Components/Icons/SparklesIcon.vue';
import TruckIcon from '@/Components/Icons/PackageIcon.vue';
import ChipIcon from '@/Components/Icons/ZapIcon.vue';
import IdentificationIcon from '@/Components/Icons/UserIcon.vue';
import MapPinIcon from '@/Components/Icons/LocationIcon.vue';

// Safe mapping for missing icons
import DocumentIcon from '@/Components/Icons/DocumentIcon.vue';
import FolderIcon from '@/Components/Icons/FolderIcon.vue';
import CalendarIcon from '@/Components/Icons/CalendarIcon.vue';
import ClockIcon from '@/Components/Icons/ClockIcon.vue';
import SearchIcon from '@/Components/Icons/SearchIcon.vue';
import InboxIcon from '@/Components/Icons/InboxIcon.vue';

const icons = {
    LibraryIcon, 
    ClipboardCheckIcon, 
    ToolsIcon, 
    ExclamationIcon, 
    OfficeBuildingIcon, 
    SwitchHorizontalIcon, 
    CashIcon, 
    TrashIcon,
    ShoppingCartIcon, 
    LightningBoltIcon, 
    SparklesIcon, 
    SunIcon: SparklesIcon,
    TruckIcon, 
    ChipIcon, 
    IdentificationIcon, 
    MapPinIcon,
    BookOpenIcon: DocumentIcon, 
    HomeIcon: FolderIcon, 
    CalendarIcon, 
    CollectionIcon: FolderIcon,
    DocumentTextIcon: DocumentIcon, 
    CubeIcon: LibraryIcon, 
    UserGroupIcon: IdentificationIcon, 
    EyeIcon: SearchIcon,
    ClockIcon, 
    DesktopComputerIcon: ToolsIcon, 
    TagIcon: CashIcon, 
    ShieldCheckIcon: ClipboardCheckIcon,
    BoxIcon: LibraryIcon, 
    ArchiveIcon: InboxIcon,
    WindIcon: SparklesIcon,
    ColorSwatchIcon: SparklesIcon,
    FilterIcon: SparklesIcon,
    BeakerIcon: SparklesIcon,
    RefreshIcon: SparklesIcon,
    ScaleIcon: SparklesIcon,
    KeyIcon: ToolsIcon,
    DocumentReportIcon: DocumentIcon,
    PresentationChartLineIcon: ToolsIcon,
};

const props = defineProps({
    procedures: Array,
    currentGroup: String
});

const getGroupTitle = (group) => {
    const titles = {
        'aset': 'Manajemen Aset & Inventaris',
        'sarpras': 'Sarana, Prasarana & Fasilitas',
        'proyek': 'Perbaikan & Proyek Kegiatan',
        'logistik': 'Logistik & Pengadaan',
        'kebersihan': 'Pemeliharaan Kebersihan',
        'lainnya': 'Kendaraan & Lainnya'
    };
    return titles[group] || 'Seluruh Prosedur ISO URT';
};

const searchQuery = ref('');

const filteredProcedures = computed(() => {
    if (!searchQuery.value) return props.procedures;
    
    const query = searchQuery.value.toLowerCase();
    return props.procedures.filter(proc => 
        proc.title.toLowerCase().includes(query) || 
        (proc.category && proc.category.toLowerCase().includes(query))
    );
});
</script>

<template>
    <Head :title="getGroupTitle(currentGroup)" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-8 bg-pail-gold rounded-full shrink-0"></div>
                    <div>
                        <h2 class="text-xl font-black text-gray-800 dark:text-gray-200 uppercase tracking-tighter leading-tight">
                            {{ getGroupTitle(currentGroup) }}
                        </h2>
                        <p class="text-[9px] font-black text-pail-gold uppercase tracking-widest mt-0.5">Sistem Informasi Manajemen Unit Rumah Tangga</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                    <Link 
                        :href="route('admin.procedures.dashboard')" 
                        class="flex items-center justify-center gap-2 px-5 py-2.5 bg-pail-gold text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-yellow-600 transition-all shadow-lg hover:shadow-pail-gold/20"
                    >
                        📊 Dashboard ISO
                    </Link>
                    <a 
                        :href="route('admin.procedures.export_all')" 
                        target="_blank"
                        class="flex items-center justify-center gap-2 px-5 py-2.5 bg-gray-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-pail-gold transition-all shadow-lg hover:shadow-pail-gold/20"
                    >
                        <DocumentIcon class="w-4 h-4" />
                        Export Seluruh
                    </a>
                </div>
            </div>
        </template>

        <div class="py-6 md:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Group Info Header -->
                <div v-if="currentGroup" class="mb-6 flex flex-col md:flex-row md:items-center justify-between bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm gap-4">
                    <div class="flex flex-col md:flex-row md:items-center gap-4">
                        <div class="inline-block px-3 py-1.5 bg-gray-900 text-white rounded-lg text-[9px] font-black uppercase tracking-widest self-start md:self-auto">
                            MODUL: {{ currentGroup }}
                        </div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tight italic">
                            Menampilkan {{ filteredProcedures.length }} prosedur kerja terotorisasi.
                        </span>
                    </div>
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="relative min-w-[200px]">
                            <SearchIcon class="absolute left-4 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" />
                            <input 
                                v-model="searchQuery"
                                type="text" 
                                placeholder="CARI PROSEDUR..."
                                class="w-full bg-gray-50 dark:bg-gray-900/50 border-0 rounded-xl pl-11 pr-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-gray-900 dark:text-white focus:ring-2 focus:ring-pail-gold"
                            />
                        </div>
                        <Link :href="route('admin.procedures.index')" class="flex items-center text-[10px] font-black text-pail-gold uppercase tracking-widest hover:underline md:text-right">
                            Lihat Semua &rarr;
                        </Link>
                    </div>
                </div>

                <!-- Search box when no currentGroup (on all procedures page) -->
                <div v-else class="mb-8 flex justify-end px-4 sm:px-0">
                    <div class="relative w-full md:w-1/3">
                        <SearchIcon class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input 
                            v-model="searchQuery"
                            type="text" 
                            placeholder="CARI PROSEDUR ISO..."
                            class="w-full bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl pl-12 pr-6 py-3.5 text-[10px] font-black uppercase tracking-widest text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-pail-gold transition-all"
                        />
                    </div>
                </div>

                <!-- Procedures Grid -->
                <div class="grid grid-cols-3 sm:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-6">
                    <Link 
                        v-for="(proc, key) in filteredProcedures" 
                        :key="key"
                        :href="proc.url"
                        class="bg-white dark:bg-gray-800 p-3 md:p-6 rounded-2xl md:rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 group relative overflow-hidden flex flex-col items-center text-center justify-between min-h-[140px] md:min-h-[220px]"
                    >
                        <!-- Background Pattern -->
                        <div class="hidden md:flex absolute inset-0 opacity-[0.03] group-hover:opacity-[0.07] transition-opacity pointer-events-none font-black text-[100px] select-none items-center justify-center text-gray-900 dark:text-white transform -rotate-12 translate-x-8 translate-y-8">
                             {{ (proc.id || '').charAt(0).toUpperCase() }}
                        </div>

                        <!-- Count Badge - Desktop -->
                        <div class="hidden md:flex absolute top-4 right-4 items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black transition-all z-10"
                             :class="proc.count > 0 ? 'bg-pail-gold/10 text-pail-gold' : 'bg-gray-100 dark:bg-gray-700 text-gray-400'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>{{ proc.count }} data</span>
                        </div>

                        <!-- Count Badge - Mobile (compact) -->
                        <div class="md:hidden absolute top-2 right-2 px-2 py-1 rounded-full text-[9px] font-black z-10"
                             :class="proc.count > 0 ? 'bg-pail-gold text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500'">
                            {{ proc.count }}
                        </div>

                        <!-- Icon -->
                        <div class="w-10 h-10 md:w-14 md:h-14 rounded-xl md:rounded-2xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-gray-400 group-hover:bg-pail-gold group-hover:text-white transition-all duration-500 mb-2 md:mb-4 shadow-inner border border-gray-100 dark:border-gray-700 relative z-10 shrink-0 mt-2 md:mt-0">
                            <component :is="icons[proc.icon] || icons.LibraryIcon" class="w-5 h-5 md:w-7 md:h-7" />
                        </div>
                        
                        <!-- Title -->
                        <h4 class="text-[8px] md:text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight md:tracking-tighter mb-1 md:mb-2 relative z-10 leading-tight group-hover:text-pail-gold transition-colors break-words w-full px-1 line-clamp-3 md:line-clamp-2">
                            {{ proc.title }}
                        </h4>

                        <!-- Stats info - Mobile & Desktop -->
                        <div class="text-center relative z-10 mb-1 hidden md:block" v-if="proc.count > 0">
                            <span class="text-[10px] font-bold text-gray-400">
                                {{ proc.count }} entri tercatat
                            </span>
                        </div>
                        
                        <!-- Action Button - Desktop only -->
                        <div class="mt-auto pt-2 md:pt-3 relative z-10 hidden md:block">
                            <span class="text-[9px] font-black text-pail-gold uppercase tracking-[0.2em] border-b-2 border-pail-gold/20 group-hover:border-pail-gold transition-all">
                                Buka Modul &rarr;
                            </span>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.group:hover .rotate-3 {
    transform: rotate(0deg) scale(1.1);
}
</style>
