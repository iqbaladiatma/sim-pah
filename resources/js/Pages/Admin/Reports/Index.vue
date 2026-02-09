<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { formatRupiah } from "@/Utils/format";

defineProps({
    stats: Array,
});
</script>

<template>
    <Head title="Laporan Institusi" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                    Laporan Per Lembaga
                </h2>
                <div class="flex items-center gap-3">
                    <span class="px-5 py-2 rounded-xl bg-gray-900 text-white text-[10px] font-black uppercase tracking-widest border border-black shadow-lg shadow-black/20">
                        Periode: {{ new Date().toLocaleString('id-ID', { month: 'long', year: 'numeric' }) }}
                    </span>
                </div>
            </div>
        </template>

        <div class="pt-6 pb-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
                
                <!-- Summary Bar -->
                <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-black p-10 rounded-[3rem] shadow-2xl border border-white/5 relative overflow-hidden">
                    <div class="absolute right-0 top-0 -mt-20 -mr-20 w-80 h-80 bg-pail-gold opacity-10 rounded-full blur-[100px]"></div>
                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
                        <div>
                            <h3 class="text-white font-black text-2xl uppercase tracking-tighter mb-1">Analisis Profitabilitas & Serapan</h3>
                            <p class="text-gray-400 text-xs font-medium uppercase tracking-widest italic">Monitoring real-time alokasi sumber daya antar lembaga.</p>
                        </div>
                        <div class="flex items-center gap-10">
                            <div>
                                <p class="text-pail-gold text-[10px] font-black uppercase tracking-widest mb-1">Total Lembaga</p>
                                <p class="text-white text-3xl font-black tracking-tighter">{{ stats.length }}</p>
                            </div>
                            <div class="h-10 w-px bg-gray-700"></div>
                            <div>
                                <p class="text-green-500 text-[10px] font-black uppercase tracking-widest mb-1">Total Anggaran Terpakai</p>
                                <p class="text-white text-3xl font-black tracking-tighter font-mono">{{ formatRupiah(stats.reduce((acc, curr) => acc + (parseFloat(curr.total_cost) || 0), 0)) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid of Institutions -->
                <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-8">
                    <div v-for="inst in stats" :key="inst.code" 
                        class="bg-white dark:bg-gray-800 p-4 sm:p-6 md:p-10 rounded-[1.5rem] sm:rounded-[2rem] md:rounded-[3rem] border border-gray-100 dark:border-gray-700 shadow-xl group hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 relative overflow-hidden flex flex-col">
                        
                        <!-- Accent Background -->
                        <div class="absolute top-0 right-0 w-16 h-16 sm:w-32 sm:h-32 bg-gray-50 dark:bg-gray-900 rounded-bl-[2rem] sm:rounded-bl-[4rem] group-hover:bg-pail-gold/10 transition-colors duration-500"></div>

                        <header class="relative z-10 mb-4 sm:mb-8">
                            <div class="flex items-center justify-between mb-4 sm:mb-6">
                                <div class="flex items-center gap-2 sm:gap-4">
                                    <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-lg sm:rounded-2xl bg-gray-900 dark:bg-pail-gold flex items-center justify-center text-pail-gold dark:text-gray-900 font-black text-xs sm:text-xl shadow-lg border border-white/10 shrink-0">
                                        {{ inst.name.charAt(0) }}
                                    </div>
                                    <span class="px-2 sm:px-4 py-1 sm:py-1.5 rounded-lg sm:rounded-xl bg-gray-100 dark:bg-gray-900 border border-gray-200/50 dark:border-gray-700 text-[6px] sm:text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.1em] sm:tracking-[0.2em] group-hover:bg-pail-gold group-hover:text-white group-hover:border-pail-gold transition-all duration-500 truncate max-w-[30px] sm:max-w-none">
                                        {{ inst.code }}
                                    </span>
                                </div>
                                <div v-if="inst.pending_requests > 0" class="flex items-center gap-1 sm:gap-2">
                                    <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full bg-red-500 animate-ping"></span>
                                    <span class="text-[6px] sm:text-[9px] font-black text-red-500 uppercase tracking-widest">{{ inst.pending_requests }} <span class="hidden sm:inline">MENUNGGU</span></span>
                                </div>
                            </div>
                            <h3 class="text-[10px] sm:text-base md:text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-tight line-clamp-2 min-h-[1.5rem] sm:min-h-[3.5rem]">{{ inst.name }}</h3>
                        </header>

                        <div class="relative z-10 grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-6 mb-4 sm:mb-10">
                            <div class="bg-gray-50 dark:bg-gray-900/50 p-3 sm:p-6 rounded-xl sm:rounded-[2rem] border border-gray-100 dark:border-gray-800 shadow-sm">
                                <p class="text-[6px] sm:text-[9px] text-gray-400 font-black uppercase tracking-widest mb-0.5 sm:mb-1">Aset</p>
                                <p class="text-sm sm:text-3xl font-black text-gray-900 dark:text-white tracking-tighter">{{ inst.items_count }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-900/50 p-3 sm:p-6 rounded-xl sm:rounded-[2rem] border border-gray-100 dark:border-gray-800 shadow-sm">
                                <p class="text-[6px] sm:text-[9px] text-gray-400 font-black uppercase tracking-widest mb-0.5 sm:mb-1">Req</p>
                                <p class="text-sm sm:text-3xl font-black text-gray-900 dark:text-white tracking-tighter">{{ inst.requests_count }}</p>
                            </div>
                        </div>

                        <!-- Footer Cost -->
                        <div class="relative z-10 mt-auto pt-3 sm:pt-8 border-t border-gray-50 dark:border-gray-700/50">
                            <div class="flex items-end justify-between gap-1">
                                <div class="space-y-0.5 sm:space-y-1 min-w-0">
                                    <p class="text-[6px] sm:text-[9px] text-gray-400 font-black uppercase tracking-widest truncate">Total Biaya</p>
                                    <p class="text-[8px] sm:text-2xl font-black text-pail-gold font-mono tracking-tighter truncate">{{ formatRupiah(inst.total_cost) }}</p>
                                </div>
                                <Link :href="route('admin.requests.index', { institution: inst.id })" 
                                    class="w-7 h-7 sm:w-14 sm:h-14 bg-gray-900 dark:bg-gray-700 rounded-lg sm:rounded-2xl flex items-center justify-center border border-gray-800 dark:border-gray-600 text-pail-gold hover:scale-110 active:scale-95 transition-all shadow-xl group/btn shrink-0"
                                    title="Lihat Pengajuan">
                                    <svg class="w-3.5 h-3.5 sm:w-6 sm:h-6 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="stats.length === 0" class="bg-white dark:bg-gray-800 rounded-[3rem] shadow-2xl border border-gray-100 dark:border-gray-700 p-24 text-center">
                    <div class="w-24 h-24 bg-gray-50 dark:bg-gray-900 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 shadow-inner">
                        <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <p class="text-gray-400 font-black uppercase tracking-widest text-sm italic">Belum ada data statistik tersedia.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
