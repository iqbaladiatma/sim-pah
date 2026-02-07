<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    settings: Array,
});

const processing = ref(null);
const commandProcessing = ref(null);

const toggleSetting = (setting) => {
    processing.value = setting.id;
    const newValue = setting.value === '1' ? '0' : '1';
    
    router.patch(route('admin.system_control.update', setting.id), {
        value: newValue
    }, {
        preserveScroll: true,
        onFinish: () => {
            processing.value = null;
        }
    });
};

const updateTextSetting = (setting) => {
    processing.value = setting.id;
    router.patch(route('admin.system_control.update', setting.id), {
        value: setting.value
    }, {
        preserveScroll: true,
        onFinish: () => {
            processing.value = null;
        }
    });
};

const runCommand = (command) => {
    commandProcessing.value = command;
    router.post(route('admin.system_control.command'), {
        command: command
    }, {
        preserveScroll: true,
        onFinish: () => {
            commandProcessing.value = null;
        }
    });
};

const groupedSettings = computed(() => {
    const groups = {};
    props.settings.forEach(s => {
        if (!groups[s.group]) groups[s.group] = [];
        groups[s.group].push(s);
    });
    return groups;
});

const getGroupTitle = (group) => {
    const titles = {
        system: 'Inti Sistem & Maintenance',
        features: 'Manajemen Fitur Lembaga',
        ui: 'Antarmuka & Pengalaman Pengguna'
    };
    return titles[group] || group.toUpperCase();
};

const serverActions = [
    { id: 'optimize', label: 'Optimize System', desc: 'Cache config, routes, and views for production.', icon: 'M13 10V3L4 14h7v7l9-11h-7z' },
    { id: 'clear-cache', label: 'Clear App Cache', desc: 'Wipe all application cache (Redis/File).', icon: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16' },
    { id: 'clear-view', label: 'Clear View Cache', desc: 'Force re-compile all Blade and Inertia views.', icon: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' },
    { id: 'clear-logs', label: 'Flush System Logs', desc: 'Empty laravel.log file immediately.', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
];
</script>

<template>
    <Head title="System Orchestrator" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-12 h-12 rounded-2xl bg-pail-gold/10 flex items-center justify-center text-pail-gold shadow-inner border border-pail-gold/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter uppercase">System Orchestrator</h1>
                    </div>
                    <p class="text-sm text-gray-400 font-bold uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-pail-gold animate-pulse"></span>
                        Pusat Kontrol Utama Seluruh Fungsi Aplikasi
                    </p>
                </div>
            </div>
        </template>

        <div class="max-w-6xl mx-auto space-y-12">
            <!-- Warning Banner -->
            <div class="p-6 bg-red-500/10 border border-red-500/20 rounded-3xl flex items-start gap-5 backdrop-blur-md">
                <div class="w-12 h-12 rounded-2xl bg-red-500/20 flex items-center justify-center text-red-500 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-black text-red-600 dark:text-red-400 uppercase tracking-tighter text-lg">High-Level System Operations</h3>
                    <p class="text-sm text-red-600/80 dark:text-red-400/80 font-bold leading-relaxed">
                        Modul ini dirancang untuk manipulasi status operasional secara masif. Beritahu pengguna sebelum meluncurkan Maintenance Mode.
                    </p>
                </div>
            </div>

            <!-- Server Commands Section -->
            <div class="space-y-6">
                <div class="flex items-center gap-4 px-4">
                    <h2 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em]">Hardware & Server Actions</h2>
                    <div class="flex-1 h-px bg-gray-100 dark:bg-gray-800"></div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <button 
                        v-for="action in serverActions" 
                        :key="action.id"
                        @click="runCommand(action.id)"
                        :disabled="commandProcessing !== null"
                        class="p-5 bg-white dark:bg-gray-800 rounded-[2rem] border border-gray-100 dark:border-gray-700/50 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 text-left group disabled:opacity-50"
                    >
                        <div class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-gray-400 group-hover:text-pail-gold mb-4 transition-colors">
                            <svg v-if="commandProcessing !== action.id" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="action.icon"></path>
                            </svg>
                            <svg v-else class="animate-spin h-5 w-5 text-pail-gold" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <h4 class="font-black text-gray-900 dark:text-white uppercase tracking-tighter text-sm mb-1 group-hover:text-pail-gold transition-colors">{{ action.label }}</h4>
                        <p class="text-[10px] text-gray-400 font-bold leading-tight">{{ action.desc }}</p>
                    </button>
                </div>
            </div>

            <!-- Settings Groups -->
            <div v-for="(settings, group) in groupedSettings" :key="group" class="space-y-6">
                <div class="flex items-center gap-4 px-4">
                    <h2 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em]">{{ getGroupTitle(group) }}</h2>
                    <div class="flex-1 h-px bg-gray-100 dark:bg-gray-800"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div v-for="setting in settings" :key="setting.id" 
                        class="p-6 bg-white dark:bg-gray-800 rounded-[2rem] border border-gray-100 dark:border-gray-700/50 shadow-sm hover:shadow-xl transition-all duration-500 group relative overflow-hidden"
                    >
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-gray-50 dark:bg-gray-700/30 rounded-full blur-2xl group-hover:bg-pail-gold/10 transition-colors"></div>
                        
                        <div class="flex items-start justify-between relative z-10 gap-4">
                            <div class="flex-1">
                                <h4 class="font-black text-gray-900 dark:text-white uppercase tracking-tighter text-lg mb-1 group-hover:text-pail-gold transition-colors">{{ setting.label }}</h4>
                                <p class="text-[10px] font-black text-pail-gold uppercase tracking-widest mb-3 opacity-60">{{ setting.key }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-bold leading-relaxed">
                                    {{ setting.description }}
                                </p>
                            </div>

                            <div class="shrink-0 pt-1">
                                <!-- Toggle Switch (for booleans) -->
                                <button 
                                    v-if="setting.type === 'boolean'"
                                    @click="toggleSetting(setting)"
                                    :disabled="processing === setting.id"
                                    class="relative inline-flex h-8 w-16 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-pail-gold focus:ring-offset-2 disabled:opacity-50"
                                    :class="setting.value === '1' ? 'bg-green-500 shadow-[0_0_15px_-5px_#22c55e]' : 'bg-gray-200 dark:bg-gray-700'"
                                >
                                    <span class="sr-only">Toggle Status</span>
                                    <span
                                        aria-hidden="true"
                                        class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow-xl ring-0 transition duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] mt-0.5"
                                        :class="setting.value === '1' ? 'translate-x-8' : 'translate-x-1'"
                                    >
                                        <div v-if="processing === setting.id" class="w-full h-full flex items-center justify-center">
                                             <svg class="animate-spin h-3 w-3 text-pail-gold" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                    </span>
                                </button>

                                <!-- Text Input (for text types) -->
                                <div v-else class="flex flex-col items-end gap-2">
                                     <input 
                                        type="text" 
                                        v-model="setting.value" 
                                        @blur="updateTextSetting(setting)"
                                        class="w-48 px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-pail-gold focus:border-pail-gold transition-all"
                                    />
                                    <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Auto-save on blur</span>
                                </div>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="mt-6 flex items-center gap-2">
                             <div 
                                class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all"
                                :class="setting.value === '1' ? 'bg-green-50 text-green-600 border border-green-200 shadow-sm' : 'bg-red-50 text-red-600 border border-red-100'"
                            >
                                <span class="flex items-center gap-1.5">
                                    <span v-if="setting.value === '1'" class="w-1 h-1 rounded-full bg-green-500 animate-pulse"></span>
                                    {{ setting.value === '1' ? 'AKTIF' : 'NON-AKTIF' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Developer Lab -->
            <div class="p-10 bg-gradient-to-br from-gray-900 via-gray-900 to-black rounded-[3rem] border border-gray-800 shadow-2xl relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-80 h-80 bg-pail-gold opacity-5 rounded-full blur-[100px] group-hover:opacity-10 transition-opacity"></div>
                <div class="absolute left-0 bottom-0 w-64 h-64 bg-blue-500 opacity-5 rounded-full blur-[100px]"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row items-center gap-10">
                    <div class="shrink-0">
                        <div class="w-24 h-24 rounded-[2rem] bg-gray-800/50 flex flex-col items-center justify-center text-pail-gold border border-white/5 shadow-2xl backdrop-blur-sm relative group-hover:scale-110 transition-transform duration-700">
                             <svg class="w-10 h-10 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                            <span class="text-[8px] font-black uppercase tracking-[0.2em] opacity-50">PROTOTYPE</span>
                        </div>
                    </div>

                    <div class="flex-1 text-center md:text-left">
                        <h2 class="text-3xl font-black text-white uppercase tracking-tighter mb-4 leading-none">Developer Lab & Component Playground</h2>
                        <p class="text-gray-400 font-bold leading-relaxed mb-8 max-w-xl">
                            Ruang isolasi untuk simulasi UI, pengujian breaking changes, dan validasi design tokens sebelum didorong ke repositori utama.
                        </p>
                        <div class="flex flex-wrap items-center gap-4 justify-center md:justify-start">
                            <Link :href="route('admin.system_control.playground')" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white rounded-2xl border border-white/10 font-black text-[10px] uppercase tracking-widest transition-all">
                                Launch Design Lab
                            </Link>
                            <div class="px-6 py-3 rounded-2xl bg-pail-gold/10 border border-pail-gold/20 text-[10px] font-black text-pail-gold uppercase tracking-[0.3em] flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-pail-gold animate-ping"></span>
                                Lab Live & Online
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.translate-x-8 {
    transform: translateX(2rem);
}
</style>
