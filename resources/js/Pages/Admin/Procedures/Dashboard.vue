<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

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
    distribution: {
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

// Hover state for interactive charts
const hoveredClause = ref(null);
const hoveredTrend = ref(null);

// Helper: Get friendly message based on score
const getScoreMessage = computed(() => {
    const score = props.stats?.compliance_rate || 0;
    if (score >= 90) return { text: 'Luar biasa!', desc: 'Dokumentasi sudah sangat lengkap dan rapi.', color: 'text-green-500' };
    if (score >= 75) return { text: 'Hampir sempurna!', desc: 'Tinggal sedikit lagi untuk mencapai target.', color: 'text-pail-gold' };
    if (score >= 50) return { text: 'Terus semangat!', desc: 'Masih ada beberapa yang perlu dilengkapi.', color: 'text-yellow-500' };
    return { text: 'Ayo mulai!', desc: 'Masih banyak yang perlu didokumentasikan.', color: 'text-red-500' };
});

// Calculate circumference for progress ring
const circumference = 2 * Math.PI * 45;
const progressOffset = computed(() => {
    const progress = props.stats?.compliance_rate || 0;
    return circumference - (progress / 100) * circumference;
});

// Get progress color based on value
const getProgressColor = (value) => {
    if (value >= 90) return 'bg-green-500';
    if (value >= 75) return 'bg-pail-gold';
    if (value >= 50) return 'bg-yellow-500';
    return 'bg-red-400';
};

const getProgressColorClass = (value) => {
    if (value >= 90) return 'text-green-500';
    if (value >= 75) return 'text-pail-gold';
    if (value >= 50) return 'text-yellow-500';
    return 'text-red-400';
};

// Chart calculations for trend - using percentage-based for flexible sizing
const getMaxTrend = computed(() => {
    return props.maintenanceTrend?.length > 0 ? Math.max(...props.maintenanceTrend, 1) : 1;
});

// Calculate percentage position (0-100)
const getChartX = (index) => {
    const len = props.months?.length || 1;
    if (len <= 1) return 50;
    return (index / (len - 1)) * 100;
};

const getChartY = (val) => {
    // Use min-max normalization to fill more of the chart area
    const min = Math.min(...props.maintenanceTrend);
    const max = getMaxTrend.value;
    const range = max - min;
    
    // If all values are the same, center the line
    if (range === 0) return 50;
    
    // Normalize between 0 and 1
    const normalized = (val - min) / range;
    
    // Map to chart area: 15% (top) to 85% (bottom)
    // Higher normalized value = lower Y position (closer to top)
    return 85 - (normalized * 70);
};

// SVG path string for the line
const trendLinePath = computed(() => {
    if (!props.maintenanceTrend || props.maintenanceTrend.length < 2) return '';
    return props.maintenanceTrend.map((val, i) => {
        const x = getChartX(i);
        const y = getChartY(val);
        return `${i === 0 ? 'M' : 'L'} ${x} ${y}`;
    }).join(' ');
});

// SVG path for area fill (closed path to bottom)
const trendAreaPath = computed(() => {
    if (!props.maintenanceTrend || props.maintenanceTrend.length < 2) return '';
    const linePath = props.maintenanceTrend.map((val, i) => {
        const x = getChartX(i);
        const y = getChartY(val);
        return `${i === 0 ? 'M' : 'L'} ${x} ${y}`;
    }).join(' ');
    return `${linePath} L 100 85 L 0 85 Z`;
});

// Donut chart calculations for distribution
const totalDistribution = computed(() => {
    return Object.values(props.distribution || {}).reduce((a, b) => a + b, 0);
});

const distributionSlices = computed(() => {
    let accumulated = 0;
    const entries = Object.entries(props.distribution || {});
    const colors = ['#1f2937', '#C9A658', '#6b7280', '#9ca3af', '#d1d5db'];
    
    return entries.map(([name, count], i) => {
        const percentage = totalDistribution.value > 0 ? (count / totalDistribution.value) * 100 : 0;
        const start = accumulated;
        accumulated += percentage;
        return { name, count, percentage, start, color: colors[i % colors.length] };
    });
});
</script>

<template>
    <Head title="Bagaimana Progress Kita?" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-4 sm:px-0">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black leading-tight text-gray-900 dark:text-white tracking-tight flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-pail-gold/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-pail-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        Bagaimana Progress Kita?
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mt-1 ml-13">
                        Pantau kelengkapan dokumentasi ISO 9001:2015 dengan mudah
                    </p>
                </div>
                <Link :href="route('admin.procedures.index')" class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-pail-gold text-white dark:text-gray-900 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-black dark:hover:bg-yellow-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Kelola Prosedur
                </Link>
            </div>
        </template>

        <div class="py-6 sm:py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">
                
                <!-- DESKTOP: Side-by-side layout -->
                <div class="hidden lg:grid lg:grid-cols-12 gap-6">
                    
                    <!-- Left: Big Progress Ring -->
                    <div class="lg:col-span-4 bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 rounded-3xl p-8 text-center relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-40 h-40 bg-pail-gold/10 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
                        
                        <div class="relative z-10">
                            <h3 class="text-white/60 text-xs font-bold uppercase tracking-wider mb-6 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                Kelengkapan Dokumentasi
                            </h3>

                            <div class="relative w-44 h-44 mx-auto mb-6 group cursor-pointer">
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                    <circle cx="50" cy="50" r="45" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="10"/>
                                    <circle 
                                        cx="50" cy="50" r="45" 
                                        fill="none" 
                                        stroke="#C9A658" 
                                        stroke-width="10"
                                        stroke-linecap="round"
                                        :stroke-dasharray="circumference"
                                        :stroke-dashoffset="progressOffset"
                                        class="transition-all duration-1000 ease-out group-hover:stroke-yellow-400"
                                    />
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-5xl font-black text-white tracking-tighter group-hover:scale-110 transition-transform">
                                        {{ stats?.compliance_rate || 0 }}%
                                    </span>
                                    <span class="text-pail-gold text-xs font-bold mt-1">Tercapai</span>
                                </div>
                            </div>

                            <p class="text-xl font-black mb-1" :class="getScoreMessage.color">
                                {{ getScoreMessage.text }}
                            </p>
                            <p class="text-white/50 text-xs">
                                {{ getScoreMessage.desc }}
                            </p>

                            <!-- Mini stats -->
                            <div class="grid grid-cols-3 gap-2 mt-8 pt-6 border-t border-white/10">
                                <div class="text-center">
                                    <div class="text-2xl font-black text-pail-gold">{{ stats?.total_procedures || 0 }}</div>
                                    <div class="text-[10px] text-white/40 font-semibold uppercase">Modul</div>
                                </div>
                                <div class="text-center border-x border-white/10">
                                    <div class="text-2xl font-black text-pail-gold">{{ stats?.active_checklists || 0 }}</div>
                                    <div class="text-[10px] text-white/40 font-semibold uppercase">Checklist</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-black text-pail-gold">{{ stats?.total_logs || 0 }}</div>
                                    <div class="text-[10px] text-white/40 font-semibold uppercase">Catatan</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Center: Interactive Area Chart -->
                    <div class="lg:col-span-5 bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-pail-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                </svg>
                                Tren Aktivitas Bulanan
                            </h3>
                            <span class="text-xs text-gray-400 font-medium">{{ months?.length || 0 }} bulan terakhir</span>
                        </div>

                        <!-- Interactive Area Chart -->
                        <div class="relative h-48 mb-8">
                            <!-- Background SVG for line and area (stretched to fit) -->
                            <svg class="absolute inset-0 w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                                <!-- Grid lines - positioned to match chart Y range (15-85) -->
                                <line x1="0" y1="15" x2="100" y2="15" stroke="currentColor" stroke-width="0.3" class="text-gray-100 dark:text-gray-700"/>
                                <line x1="0" y1="38" x2="100" y2="38" stroke="currentColor" stroke-width="0.3" class="text-gray-100 dark:text-gray-700"/>
                                <line x1="0" y1="62" x2="100" y2="62" stroke="currentColor" stroke-width="0.3" class="text-gray-100 dark:text-gray-700"/>
                                <line x1="0" y1="85" x2="100" y2="85" stroke="currentColor" stroke-width="0.3" class="text-gray-100 dark:text-gray-700"/>
                                
                                <!-- Gradient definition -->
                                <defs>
                                    <linearGradient id="areaGradDesktop" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#C9A658" stop-opacity="0.35"/>
                                        <stop offset="100%" stop-color="#C9A658" stop-opacity="0.02"/>
                                    </linearGradient>
                                </defs>
                                
                                <!-- Area fill -->
                                <path 
                                    v-if="trendAreaPath" 
                                    :d="trendAreaPath" 
                                    fill="url(#areaGradDesktop)" 
                                    class="transition-all duration-500"
                                />
                                
                                <!-- Line -->
                                <path 
                                    v-if="trendLinePath" 
                                    :d="trendLinePath" 
                                    fill="none" 
                                    stroke="#C9A658" 
                                    stroke-width="2" 
                                    stroke-linecap="round" 
                                    stroke-linejoin="round" 
                                    class="transition-all duration-500"
                                    vector-effect="non-scaling-stroke"
                                />
                            </svg>
                            
                            <!-- Interactive Data Points (HTML elements - not distorted) -->
                            <div 
                                v-for="(val, i) in maintenanceTrend" 
                                :key="'point-'+i"
                                class="absolute transform -translate-x-1/2 -translate-y-1/2 cursor-pointer z-10"
                                :style="{
                                    left: `${getChartX(i)}%`,
                                    top: `${getChartY(val)}%`
                                }"
                                @mouseenter="hoveredTrend = i"
                                @mouseleave="hoveredTrend = null"
                            >
                                <!-- Outer glow when hovered -->
                                <div 
                                    class="absolute inset-0 rounded-full bg-pail-gold/30 transition-all duration-200"
                                    :class="hoveredTrend === i ? 'scale-[3] opacity-100' : 'scale-0 opacity-0'"
                                ></div>
                                <!-- The dot -->
                                <div 
                                    class="relative w-3 h-3 rounded-full bg-white border-2 border-pail-gold transition-all duration-200"
                                    :class="hoveredTrend === i ? 'scale-150 border-[3px] shadow-lg' : ''"
                                ></div>
                                
                                <!-- Tooltip -->
                                <div 
                                    v-show="hoveredTrend === i"
                                    class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 whitespace-nowrap z-20"
                                >
                                    <div class="bg-gray-900 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xl">
                                        {{ val }} aktivitas
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Month labels -->
                            <div class="absolute -bottom-6 left-0 right-0 flex justify-between">
                                <span 
                                    v-for="(m, i) in months" 
                                    :key="m" 
                                    class="text-[10px] font-bold uppercase transition-colors cursor-pointer"
                                    :class="hoveredTrend === i ? 'text-pail-gold' : 'text-gray-400'"
                                    @mouseenter="hoveredTrend = i"
                                    @mouseleave="hoveredTrend = null"
                                >{{ m }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Donut Chart Distribution -->
                    <div class="lg:col-span-3 bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
                        <h3 class="font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-pail-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                            </svg>
                            Distribusi
                        </h3>

                        <!-- Donut Chart -->
                        <div class="relative w-32 h-32 mx-auto mb-6">
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                <circle 
                                    v-for="(slice, i) in distributionSlices" 
                                    :key="i"
                                    cx="50" cy="50" r="35" 
                                    fill="none"
                                    :stroke="slice.color"
                                    stroke-width="20"
                                    :stroke-dasharray="`${slice.percentage * 2.2} ${220 - slice.percentage * 2.2}`"
                                    :stroke-dashoffset="-slice.start * 2.2"
                                    class="transition-all duration-500 cursor-pointer hover:opacity-80"
                                />
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-lg font-black text-gray-900 dark:text-white">{{ totalDistribution }}</span>
                            </div>
                        </div>

                        <!-- Legend -->
                        <div class="space-y-2">
                            <div v-for="(slice, i) in distributionSlices" :key="i" class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-sm" :style="{ backgroundColor: slice.color }"></div>
                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ slice.name }}</span>
                                </div>
                                <span class="text-xs font-bold text-gray-900 dark:text-white">{{ slice.count }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MOBILE: Stacked layout -->
                <div class="lg:hidden space-y-6">
                    <!-- Mobile Progress Card -->
                    <div class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 rounded-2xl p-6 text-center relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-pail-gold/10 rounded-full blur-2xl translate-x-1/2 -translate-y-1/2"></div>
                        
                        <div class="relative z-10">
                            <div class="flex items-center justify-center gap-6">
                                <div class="relative w-28 h-28">
                                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                        <circle cx="50" cy="50" r="45" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="8"/>
                                        <circle 
                                            cx="50" cy="50" r="45" 
                                            fill="none" stroke="#C9A658" stroke-width="8" stroke-linecap="round"
                                            :stroke-dasharray="circumference" :stroke-dashoffset="progressOffset"
                                            class="transition-all duration-1000"
                                        />
                                    </svg>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                                        <span class="text-3xl font-black text-white">{{ stats?.compliance_rate || 0 }}%</span>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <p class="text-lg font-black" :class="getScoreMessage.color">{{ getScoreMessage.text }}</p>
                                    <p class="text-white/50 text-xs mt-1">{{ getScoreMessage.desc }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-3 mt-6 pt-5 border-t border-white/10">
                                <div class="text-center">
                                    <div class="text-xl font-black text-pail-gold">{{ stats?.total_procedures || 0 }}</div>
                                    <div class="text-[9px] text-white/40 font-semibold uppercase">Modul</div>
                                </div>
                                <div class="text-center border-x border-white/10">
                                    <div class="text-xl font-black text-pail-gold">{{ stats?.active_checklists || 0 }}</div>
                                    <div class="text-[9px] text-white/40 font-semibold uppercase">Checklist</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl font-black text-pail-gold">{{ stats?.total_logs || 0 }}</div>
                                    <div class="text-[9px] text-white/40 font-semibold uppercase">Catatan</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Per Area - Works for both -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl lg:rounded-3xl p-5 sm:p-8 border border-gray-100 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-pail-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            Progress Per Area
                        </h3>
                        <span class="text-xs text-gray-400 font-medium hidden sm:block">Target: 95%</span>
                    </div>

                    <div class="space-y-4">
                        <div 
                            v-for="(clause, index) in isoCompliance" 
                            :key="clause.sector"
                            class="group cursor-pointer"
                            @mouseenter="hoveredClause = index"
                            @mouseleave="hoveredClause = null"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <!-- SVG Icons based on sector -->
                                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110" 
                                         :class="hoveredClause === index ? 'bg-pail-gold/20' : 'bg-gray-100 dark:bg-gray-700'">
                                        <!-- Asset icon -->
                                        <svg v-if="clause.sector.toLowerCase().includes('aset')" class="w-4 h-4 sm:w-5 sm:h-5" :class="hoveredClause === index ? 'text-pail-gold' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        <!-- Building icon -->
                                        <svg v-else-if="clause.sector.toLowerCase().includes('sarpras')" class="w-4 h-4 sm:w-5 sm:h-5" :class="hoveredClause === index ? 'text-pail-gold' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <!-- Truck icon -->
                                        <svg v-else-if="clause.sector.toLowerCase().includes('logistik')" class="w-4 h-4 sm:w-5 sm:h-5" :class="hoveredClause === index ? 'text-pail-gold' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                                        </svg>
                                        <!-- Sparkles icon (cleaning) -->
                                        <svg v-else-if="clause.sector.toLowerCase().includes('kebersihan')" class="w-4 h-4 sm:w-5 sm:h-5" :class="hoveredClause === index ? 'text-pail-gold' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                        </svg>
                                        <!-- Car icon -->
                                        <svg v-else-if="clause.sector.toLowerCase().includes('kendaraan')" class="w-4 h-4 sm:w-5 sm:h-5" :class="hoveredClause === index ? 'text-pail-gold' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10 M17 16V8a1 1 0 011-1h2l3 4v5"></path>
                                        </svg>
                                        <!-- Default clipboard icon -->
                                        <svg v-else class="w-4 h-4 sm:w-5 sm:h-5" :class="hoveredClause === index ? 'text-pail-gold' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 dark:text-white text-sm">{{ clause.sector }}</h4>
                                        <p class="text-[10px] text-gray-400 hidden sm:block">Dokumentasi & prosedur</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xl sm:text-2xl font-black tracking-tight transition-transform" 
                                          :class="[getProgressColorClass(clause.value), { 'scale-110': hoveredClause === index }]">
                                        {{ clause.value }}%
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Progress bar with animation -->
                            <div class="w-full h-2.5 sm:h-3 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div 
                                    class="h-full rounded-full transition-all duration-700 ease-out"
                                    :class="[getProgressColor(clause.value), { 'brightness-110': hoveredClause === index }]"
                                    :style="{ width: `${clause.value}%` }"
                                ></div>
                            </div>
                            
                            <!-- Status message -->
                            <p class="mt-2 text-xs font-medium flex items-center gap-1.5" :class="clause.value >= 90 ? 'text-green-600' : clause.value >= 75 ? 'text-pail-gold' : 'text-gray-400'">
                                <svg v-if="clause.value >= 90" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                                {{ clause.value >= 90 ? 'Lengkap dan rapi!' : clause.value >= 75 ? 'Hampir selesai, tinggal sedikit lagi' : 'Masih perlu dilengkapi' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl lg:rounded-3xl p-5 sm:p-8 border border-gray-100 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-pail-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Aktivitas Terbaru
                        </h3>
                        <Link :href="route('admin.procedures.index')" class="text-xs text-pail-gold font-bold hover:underline flex items-center gap-1">
                            Lihat Semua
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </Link>
                    </div>

                    <div v-if="stats?.recent_logs_list?.length > 0" class="space-y-3">
                        <div 
                            v-for="(log, index) in stats.recent_logs_list.slice(0, 5)" 
                            :key="log.id || index"
                            class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors group"
                        >
                            <div class="w-10 h-10 rounded-xl bg-pail-gold/10 flex items-center justify-center text-pail-gold shrink-0 group-hover:bg-pail-gold group-hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-gray-900 dark:text-white text-sm truncate">
                                    {{ log.title || 'Pemeliharaan Rutin' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ log.institution?.name || 'Umum' }} • 
                                    <span class="text-pail-gold font-semibold">{{ log.status || 'Tercatat' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-10">
                        <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-400 font-medium">Belum ada aktivitas tercatat</p>
                        <p class="text-gray-300 text-sm mt-1">Mulai catat kegiatan pemeliharaan Anda</p>
                    </div>
                </div>

                <!-- Tips Section -->
                <div class="bg-gradient-to-r from-pail-gold/10 to-yellow-50 dark:from-pail-gold/5 dark:to-gray-800 border border-pail-gold/20 rounded-2xl p-5 sm:p-6">
                    <div class="flex flex-col sm:flex-row items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-pail-gold/20 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-pail-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 dark:text-white mb-2">Tips Meningkatkan Progress</h4>
                            <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-pail-gold shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Pastikan semua checklist bulanan diisi tepat waktu
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-pail-gold shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Upload foto bukti untuk setiap pemeliharaan
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-pail-gold shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Update data inventaris secara berkala
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Mobile Action Button -->
                <div class="lg:hidden text-center">
                    <Link 
                        :href="route('admin.procedures.index')" 
                        class="inline-flex items-center gap-2 px-8 py-4 bg-pail-gold text-white rounded-2xl font-bold text-sm hover:bg-yellow-600 transition-colors shadow-xl shadow-pail-gold/20 w-full justify-center"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Kelola Prosedur ISO
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
circle, path {
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
