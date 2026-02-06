<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

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

const icons = {
    LibraryIcon, ClipboardCheckIcon, ToolsIcon, ExclamationIcon, 
    OfficeBuildingIcon, SwitchHorizontalIcon, CashIcon, TrashIcon,
    ShoppingCartIcon, LightningBoltIcon, SparklesIcon, SunIcon,
    TruckIcon, ChipIcon, IdentificationIcon, MapPinIcon
};

const props = defineProps({
    procedures: Array,
    currentGroup: String
});

const getGroupTitle = (group) => {
    const titles = {
        'aset': 'Manajemen Aset & Inventaris',
        'sarpras': 'Sarana, Prasarana & Fasilitas',
        'kendaraan': 'Armada & Parkir Area',
        'logistik': 'Logistik & Peminjaman',
        'iso': 'Kepatuhan & Audit ISO'
    };
    return titles[group] || 'Seluruh Prosedur ISO URT';
};
</script>

<template>
    <Head :title="getGroupTitle(currentGroup)" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-4 sm:px-0">
                <div class="flex items-center gap-4">
                    <div class="w-1.5 h-8 md:w-2 md:h-10 bg-pail-gold rounded-full shrink-0"></div>
                    <div>
                        <h2 class="text-xl md:text-2xl font-black text-gray-800 dark:text-gray-200 uppercase tracking-tighter leading-tight">
                            {{ getGroupTitle(currentGroup) }}
                        </h2>
                        <p class="text-[8px] md:text-[10px] font-black text-pail-gold uppercase tracking-widest mt-1">Sistem Informasi Manajemen Unit Rumah Tangga</p>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-6 md:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Group Info Header -->
                <div v-if="currentGroup" class="mb-6 md:mb-10 flex flex-col md:flex-row md:items-center justify-between bg-white dark:bg-gray-800 p-6 md:p-8 rounded-[1.5rem] md:rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm gap-4">
                    <div class="flex flex-col md:flex-row md:items-center gap-4">
                        <div class="inline-block px-4 py-2 bg-gray-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest self-start md:self-auto">
                            MODUL: {{ currentGroup }}
                        </div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-tight italic">
                            Menampilkan {{ procedures.length }} prosedur kerja terotorisasi.
                        </span>
                    </div>
                    <Link :href="route('admin.procedures.index')" class="text-[10px] font-black text-pail-gold uppercase tracking-widest hover:underline md:text-right">
                        Lihat Semua Prosedur &rarr;
                    </Link>
                </div>

                <!-- Procedures Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-8">
                    <Link 
                        v-for="(proc, key) in procedures" 
                        :key="key"
                        :href="proc.url"
                        class="bg-white dark:bg-gray-800 p-6 md:p-8 rounded-[1.5rem] md:rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 group relative overflow-hidden flex flex-col items-center text-center"
                    >
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 opacity-[0.03] group-hover:opacity-[0.07] transition-opacity pointer-events-none font-black text-[120px] select-none flex items-center justify-center text-gray-900 dark:text-white transform -rotate-12 translate-x-8 translate-y-8">
                             {{ (proc.id || '').charAt(0).toUpperCase() }}
                        </div>

                        <div class="w-16 h-16 rounded-2xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-gray-400 group-hover:bg-pail-gold group-hover:text-white transition-all duration-500 mb-6 shadow-inner border border-gray-100 dark:border-gray-700 relative z-10">
                            <component :is="icons[proc.icon]" class="w-8 h-8" />
                        </div>
                        
                        <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter mb-3 relative z-10 leading-tight group-hover:text-pail-gold transition-colors">
                            {{ proc.title }}
                        </h4>
                        
                        <div class="mt-auto pt-4 relative z-10">
                            <span class="text-[9px] font-black text-pail-gold uppercase tracking-[0.3em] border-b-2 border-pail-gold/20 group-hover:border-pail-gold transition-all">
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
