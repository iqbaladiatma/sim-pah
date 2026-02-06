<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            compliance_rate: 0,
            active_checklists: 0,
            total_procedures: 0,
            total_logs: 0,
            recent_logs_list: []
        })
    },
    groups: {
        type: Object,
        default: () => ({})
    },
    maintenanceTrend: {
        type: Array,
        default: () => []
    },
    isoCompliance: {
        type: Array,
        default: () => []
    },
    months: {
        type: Array,
        default: () => []
    }
});

// Custom Chart Helpers
const getMaxTrend = computed(() => {
    return props.maintenanceTrend?.length > 0 ? Math.max(...props.maintenanceTrend, 1) : 1;
});

const getTrendPoint = (val, index) => {
    const monthsLen = props.months?.length || 0;
    if (monthsLen < 2) return "0,100";
    const x = (index / (monthsLen - 1)) * 100;
    const y = 100 - (val / getMaxTrend.value) * 100;
    return `${x},${y}`;
};

const trendPath = computed(() => {
    if (!props.maintenanceTrend || props.maintenanceTrend.length < 2) return "0,100 100,100";
    return props.maintenanceTrend.map((v, i) => getTrendPoint(v, i)).join(' ');
});

// Pie Chart Calculations
const totalGroupCount = computed(() => {
    if (!props.groups) return 0;
    return Object.values(props.groups).reduce((a, b) => a + b, 0);
});
const groupSlices = computed(() => {
    let accumulated = 0;
    if (!props.groups) return [];
    const entries = Object.entries(props.groups);
    if (entries.length === 0) return [];
    return entries.map(([name, count]) => {
        const percentage = totalGroupCount.value > 0 ? (count / totalGroupCount.value) * 100 : 0;
        const start = accumulated;
        accumulated += percentage;
        return { name, count, percentage, start };
    });
});

const getSectorColor = (val) => {
    if (val >= 90) return 'text-green-500';
    if (val >= 75) return 'text-pail-gold';
    return 'text-red-500';
};

const formatGroupTitle = (slug) => {
    if (!slug) return '';
    const titles = {
        'aset': 'Manajemen Aset',
        'sarpras': 'Sarana Prasarana',
        'proyek': 'Proyek & Perbaikan',
        'logistik': 'Logistik',
        'kebersihan': 'Kebersihan',
        'lainnya': 'Lain-lain'
    };
    return titles[slug] || (typeof slug === 'string' ? slug.toUpperCase() : '');
};
</script>

<template>
    <Head title="Dashboard ISO 9001:2015" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 px-4 sm:px-0">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 rounded-[1.5rem] bg-gray-900 border-2 border-pail-gold/30 flex items-center justify-center shadow-2xl relative overflow-hidden group">
                        <div class="absolute inset-0 bg-pail-gold opacity-10 group-hover:scale-150 transition-transform"></div>
                        <svg class="w-8 h-8 text-pail-gold relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black leading-tight text-gray-900 dark:text-white uppercase tracking-tighter">
                            ISO 9001:2015 <span class="text-pail-gold">Panel Command</span>
                        </h2>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mt-1 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            Audit Trace Pemenuhan Standar Real-time
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <button class="px-6 py-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-pail-gold transition-all shadow-sm">
                        Ekspor Laporan
                    </button>
                    <Link :href="route('admin.procedures.index')" class="px-8 py-4 bg-gray-900 text-pail-gold rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-xl shadow-black/20">
                        Terminal Prosedur
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                
                <!-- Ringkasan Hub Pemenuhan -->
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    <!-- Skor Global (Radial Progress) -->
                    <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-[2.5rem] p-10 border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col items-center justify-center relative group overflow-hidden">
                        <div class="absolute inset-0 bg-pail-gold/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-8 text-center">Skor Kepatuhan Global</h4>
                        
                        <div class="relative w-48 h-48 mb-8">
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="8" class="text-gray-50 dark:text-gray-700" />
                                <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="8" stroke-dasharray="283" :stroke-dashoffset="283 - (283 * (stats?.compliance_rate || 0) / 100)" class="text-pail-gold transition-all duration-1000 ease-out" stroke-linecap="round" />
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-5xl font-black text-gray-900 dark:text-white tracking-tighter">{{ stats?.compliance_rate || 0 }}%</span>
                                <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Optimal</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 text-[10px] font-black uppercase tracking-widest">
                            <span class="text-green-500 font-bold">Terintegrasi</span>
                            <span class="text-gray-300">|</span>
                            <span class="text-gray-400">Target: 95%</span>
                        </div>
                    </div>

                    <!-- Grafik Distribusi -->
                    <div class="lg:col-span-3 bg-white dark:bg-gray-800 rounded-[2.5rem] p-10 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-10 opacity-5 group-hover:opacity-10 transition-opacity">
                            <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                        </div>
                        
                        <div class="flex flex-col md:flex-row gap-12 h-full">
                            <!-- Distribusi Sektor (Pie Chart) -->
                            <div class="flex-1">
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-8">Distribusi Prosedur ISO</h4>
                                <div class="flex items-center gap-8">
                                    <div class="relative w-32 h-32 shrink-0">
                                        <svg class="w-full h-full" viewBox="0 0 100 100">
                                            <circle v-for="(slice, i) in groupSlices" :key="i"
                                                cx="50" cy="50" r="30" fill="none" 
                                                stroke="currentColor" stroke-width="40"
                                                :stroke-dasharray="`${slice.percentage} ${100 - slice.percentage}`"
                                                :stroke-dashoffset="-slice.start"
                                                :class="[
                                                    i === 0 ? 'text-gray-900' : 
                                                    i === 1 ? 'text-pail-gold' : 
                                                    i === 2 ? 'text-gray-400' : 
                                                    i === 3 ? 'text-gray-200' : 'text-gray-100'
                                                ]"
                                            />
                                        </svg>
                                    </div>
                                    <div class="space-y-4 w-full">
                                        <div v-for="(count, name, i) in groups" :key="name" class="flex items-center justify-between group/line">
                                            <div class="flex items-center gap-3">
                                                <div class="w-3 h-3 rounded-md" :class="[
                                                    i === 0 ? 'bg-gray-900' : 
                                                    i === 1 ? 'bg-pail-gold' : 
                                                    i === 2 ? 'bg-gray-400' : 
                                                    i === 3 ? 'bg-gray-200' : 'bg-gray-100'
                                                ]"></div>
                                                <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest group-hover/line:text-gray-900 dark:group-hover/line:text-white transition-colors">{{ formatGroupTitle(name) }}</span>
                                            </div>
                                            <span class="text-xs font-black text-gray-900 dark:text-white font-mono">{{ count }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tren Operasional (Area Chart) -->
                            <div class="flex-[1.5] border-l border-gray-50 dark:border-gray-700/50 pl-12 flex flex-col justify-between">
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-4">Aktivitas Pemeliharaan (Bulanan)</h4>
                                <div class="relative h-40 w-full mt-4">
                                    <!-- Garis Grid -->
                                    <div class="absolute inset-0 flex flex-col justify-between">
                                        <div v-for="i in 4" :key="i" class="w-full h-[1px] bg-gray-100/50 dark:bg-gray-700/30"></div>
                                    </div>
                                    <!-- Jalur Area -->
                                    <svg class="absolute inset-0 w-full h-full overflow-visible" preserveAspectRatio="none">
                                        <defs>
                                            <linearGradient id="gradientTrend" x1="0" y1="0" x2="0" y2="1">
                                                <stop offset="0%" stop-color="#C9A658" stop-opacity="0.3" />
                                                <stop offset="100%" stop-color="#C9A658" stop-opacity="0" />
                                            </linearGradient>
                                        </defs>
                                        <path :d="`M 0,100 L ${trendPath} L 100,100 Z`" fill="url(#gradientTrend)" class="transition-all duration-1000" />
                                        <polyline 
                                            fill="none" 
                                            stroke="#C9A658" 
                                            stroke-width="3" 
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            :points="trendPath" 
                                            class="transition-all duration-1000"
                                        />
                                        <!-- Titik-titik -->
                                        <circle v-for="(v, i) in maintenanceTrend" :key="i"
                                            :cx="(months?.length > 1 ? (i / (months.length - 1)) * 100 : 0)" 
                                            :cy="100 - (v / getMaxTrend) * 100" 
                                            r="4" 
                                            fill="white" 
                                            stroke="#C9A658" 
                                            stroke-width="2" 
                                        />
                                    </svg>
                                </div>
                                <div class="flex justify-between mt-6">
                                    <span v-for="m in months" :key="m" class="text-[9px] font-black text-gray-300 uppercase tracking-tighter">{{ m }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kepatuhan Per Klausul ISO (Bar Chart) -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Batang Klausul -->
                    <div class="lg:col-span-2 bg-gray-900 rounded-[2.5rem] p-10 border border-white/5 shadow-2xl relative overflow-hidden group">
                        <div class="absolute -right-20 -top-20 w-64 h-64 bg-pail-gold opacity-5 rounded-full blur-[80px]"></div>
                        <h4 class="text-[10px] font-black text-pail-gold/70 uppercase tracking-[0.3em] mb-12">Kepatuhan Berdasarkan Klausul ISO</h4>
                        
                        <div class="space-y-8">
                            <div v-for="clause in isoCompliance" :key="clause.sector" class="space-y-3 group/bar">
                                <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-white/50">
                                    <span>{{ clause.sector }}</span>
                                    <span class="text-pail-gold">{{ clause.value }}%</span>
                                </div>
                                <div class="w-full h-3 bg-white/5 rounded-full overflow-hidden border border-white/5">
                                    <div class="h-full bg-pail-gold rounded-full transition-all duration-1000 group-hover/bar:brightness-125" :style="{ width: `${clause.value}%` }"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Log Aktivitas Terbaru -->
                    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-10 border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col">
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-8">Pulse Operasional Terbaru</h4>
                        <div class="flex-1 space-y-6 overflow-y-auto max-h-[400px] pr-2">
                            <div v-for="log in stats?.recent_logs_list || []" :key="log.id" class="flex items-start gap-4 group/log">
                                <div class="w-10 h-10 rounded-xl bg-gray-900 flex items-center justify-center text-pail-gold shrink-0 group-hover/log:scale-110 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-tight truncate">{{ log.title || 'Pemeliharaan Rutin' }}</p>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase mt-0.5">{{ log.institution?.name || 'UMUM' }} • {{ log.status }}</p>
                                </div>
                            </div>
                            <div v-if="!stats?.recent_logs_list?.length" class="text-center py-10 opacity-20">
                                <p class="text-[10px] font-black uppercase tracking-widest">Belum ada aktivitas</p>
                            </div>
                        </div>

                        <div class="mt-8 p-6 bg-pail-gold/10 rounded-3xl border border-pail-gold/20 flex flex-col items-center gap-4">
                            <h5 class="text-[8px] font-black text-pail-gold uppercase tracking-[0.4em]">Integrated Framework</h5>
                            <p class="text-[14px] font-black text-gray-900 dark:text-white text-center leading-tight">TOTAL LOG TERDATA: <span class="bg-gray-900 text-pail-gold px-2 py-0.5 rounded ml-1">{{ stats?.total_logs || 0 }}</span></p>
                        </div>
                    </div>
                </div>

                <!-- Strategic Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-50 dark:border-gray-700 shadow-sm group hover:scale-[1.05] transition-all">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-gray-900 flex items-center justify-center text-pail-gold text-xs font-black">ISO</div>
                            <span class="text-[10px] font-black text-gray-900 dark:text-gray-200 uppercase tracking-widest">Total Klausul</span>
                        </div>
                        <div class="text-2xl font-black text-gray-900 dark:text-white tracking-tighter uppercase leading-none">10 Klausul</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-50 dark:border-gray-700 shadow-sm group hover:scale-[1.05] transition-all">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-pail-gold flex items-center justify-center text-white text-xs font-black">C</div>
                            <span class="text-[10px] font-black text-gray-900 dark:text-gray-200 uppercase tracking-widest">Ceklist Aktif</span>
                        </div>
                        <div class="text-2xl font-black text-gray-900 dark:text-white tracking-tighter uppercase leading-none">{{ stats?.active_checklists || 0 }} Unit</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-50 dark:border-gray-700 shadow-sm group hover:scale-[1.05] transition-all">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-gray-900 flex items-center justify-center text-pail-gold text-xs font-black">P</div>
                            <span class="text-[10px] font-black text-gray-900 dark:text-gray-200 uppercase tracking-widest">Prosedur ISO</span>
                        </div>
                        <div class="text-2xl font-black text-gray-900 dark:text-white tracking-tighter uppercase leading-none">{{ stats?.total_procedures || 0 }} Modul</div>
                    </div>
                    <div class="bg-pail-gold p-8 rounded-[2.5rem] shadow-xl group hover:scale-[1.05] transition-all">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-white text-xs font-black">S</div>
                            <span class="text-[10px] font-black text-white uppercase tracking-widest">Sertifikasi</span>
                        </div>
                        <div class="text-2xl font-black text-white tracking-tighter uppercase leading-none italic">Verified</div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
circle, path, polyline {
    transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
