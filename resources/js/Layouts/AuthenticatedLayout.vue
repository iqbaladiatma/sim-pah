<script setup>
import { ref, computed, onMounted } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import { Link, usePage } from "@inertiajs/vue3";
import SettingsIcon from "@/Components/Icons/SettingsIcon.vue";
import LogOutIcon from "@/Components/Icons/LogOutIcon.vue";
import CheckCircleIcon from "@/Components/Icons/CheckCircleIcon.vue";
import XCircleIcon from "@/Components/Icons/XCircleIcon.vue";

const isSidebarOpen = ref(false);
const isSidebarCollapsed = ref(false);
const isDarkMode = ref(false);
const isIsoMenuOpen = ref(usePage().url.includes('procedures'));

// Auto-close sidebar on mobile when navigating
const closeSidebarOnMobile = () => {
    if (window.innerWidth < 1024) {
        isSidebarOpen.value = false;
    }
};

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

onMounted(() => {
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDarkMode.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDarkMode.value = false;
        document.documentElement.classList.remove('dark');
    }
});

const dashboardUrl = computed(() => {
    return ['super admin', 'admin'].includes(user.value?.role)
        ? route('admin.dashboard')
        : route('dashboard');
});

const user = computed(() => usePage().props.auth.user);

const itemsUrl = computed(() => ['super admin', 'admin'].includes(user.value?.role) ? route('admin.items.index') : route('items.index'));
const requestsUrl = computed(() => ['super admin', 'admin'].includes(user.value?.role) ? route('admin.requests.index') : route('requests.index'));
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col lg:flex-row overflow-x-hidden">
        <!-- Sidebar Overlay (Mobile) -->
        <Transition
            enter-active-class="transition-opacity duration-500 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-300 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="isSidebarOpen" @click="isSidebarOpen = false" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-md lg:hidden"></div>
        </Transition>

        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 z-[60] bg-white/90 dark:bg-gray-800/90 backdrop-blur-2xl border-r border-gray-100 dark:border-gray-700/50 transition-all duration-500 ease-[cubic-bezier(0.4,0,0.2,1)] transform lg:translate-x-0 lg:static lg:inset-0 shadow-2xl lg:shadow-none"
            :class="[
                isSidebarOpen ? 'translate-x-0' : '-translate-x-full',
                isSidebarCollapsed ? 'lg:w-20' : 'lg:w-[22rem] w-[75vw] max-w-xs'
            ]"
        >
            <div class="h-full flex flex-col overflow-hidden">
                <!-- Sidebar Branding -->
                <div 
                    class="flex items-center shrink-0 relative overflow-hidden transition-all duration-500"
                    :class="[
                        isSidebarCollapsed ? 'h-24 justify-center px-2' : 'h-24 justify-between px-4 lg:px-8'
                    ]"
                >
                    <Link :href="dashboardUrl" class="flex items-center gap-3">
                        <ApplicationLogo class="h-10 w-10 lg:h-12 lg:w-12 transition-all duration-500" />
                        <div v-if="!isSidebarCollapsed" class="flex flex-col">
                            <span class="font-black text-lg lg:text-2xl text-gray-900 dark:text-white tracking-tighter leading-none mb-0.5 lg:mb-1">SIM PAH</span>
                            <span class="text-[8px] lg:text-[9px] font-black text-pail-gold uppercase tracking-[0.25em] lg:tracking-[0.3em] opacity-60">Edisi Mataram</span>
                        </div>
                    </Link>

                    <!-- Header Theme Toggle -->
                    <button 
                        v-if="!isSidebarCollapsed"
                        @click="toggleDarkMode" 
                        class="flex flex-col items-center gap-1 group transition-all"
                        :title="isDarkMode ? 'Mode Terang' : 'Mode Gelap'"
                    >
                        <div class="w-9 h-9 rounded-xl bg-gray-50/50 dark:bg-gray-900/50 flex items-center justify-center text-gray-400 group-hover:text-pail-gold border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all duration-300">
                             <svg v-if="!isDarkMode" class="w-4 h-4 transition-all duration-500 group-hover:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                            <svg v-else class="w-4 h-4 transition-all duration-500 group-hover:rotate-90 text-pail-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <span class="text-[8px] font-black uppercase text-gray-400 tracking-widest group-hover:text-pail-gold transition-colors">Tema</span>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 px-3 lg:px-4 py-4 lg:py-8 space-y-1.5 lg:space-y-2 overflow-y-auto scrollbar-hide pb-48 lg:pb-32 scroll-smooth">
                    <div class="px-3 lg:px-5 mb-3 lg:mb-4" v-if="!isSidebarCollapsed">
                        <p class="text-[9px] lg:text-[10px] font-black uppercase text-gray-400 tracking-[0.25em] lg:tracking-[0.3em]">Konsol Utama</p>
                    </div>

                    <NavLink :href="dashboardUrl" :active="route().current('dashboard') || route().current('admin.dashboard')" @click="closeSidebarOnMobile" :collapsed="isSidebarCollapsed">
                        <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0 transition-transform group-hover:scale-110" :class="isSidebarCollapsed ? '' : 'mr-3 lg:mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-xs lg:text-sm uppercase">Beranda</span>
                    </NavLink>


                    <!-- Admin Management Section -->
                    <template v-if="['super admin', 'admin'].includes(user.role)">
                        <div class="px-3 lg:px-5 mt-6 lg:mt-10 mb-3 lg:mb-4" v-if="!isSidebarCollapsed">
                            <p class="text-[9px] lg:text-[10px] font-black uppercase text-gray-400 tracking-[0.25em] lg:tracking-[0.3em]">Pusat Institusi</p>
                        </div>
                        <NavLink :href="route('admin.institutions.index')" :active="route().current('admin.institutions.*')" @click="closeSidebarOnMobile" :collapsed="isSidebarCollapsed">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0 transition-transform group-hover:scale-110" :class="isSidebarCollapsed ? '' : 'mr-3 lg:mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-xs lg:text-sm uppercase">Lembaga</span>
                        </NavLink>
                        <NavLink :href="route('admin.rooms.index')" :active="route().current('admin.rooms.*')" @click="closeSidebarOnMobile" :collapsed="isSidebarCollapsed">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0 transition-transform group-hover:scale-110" :class="isSidebarCollapsed ? '' : 'mr-3 lg:mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-xs lg:text-sm uppercase">Ruangan</span>
                        </NavLink>
                        <NavLink :href="route('admin.users.index')" :active="route().current('admin.users.*')" @click="closeSidebarOnMobile" :collapsed="isSidebarCollapsed">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0 transition-transform group-hover:scale-110" :class="isSidebarCollapsed ? '' : 'mr-3 lg:mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-xs lg:text-sm uppercase">Sdm & Akses</span>
                        </NavLink>
                    </template>

                    <div class="px-3 lg:px-5 mt-6 lg:mt-10 mb-3 lg:mb-4" v-if="!isSidebarCollapsed">
                        <p class="text-[9px] lg:text-[10px] font-black uppercase text-gray-400 tracking-[0.25em] lg:tracking-[0.3em]">Mesin Logistik</p>
                    </div>

                    <NavLink :href="itemsUrl" :active="route().current('items.*') || route().current('admin.items.*')" @click="closeSidebarOnMobile" :collapsed="isSidebarCollapsed">
                        <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0 transition-transform group-hover:scale-110" :class="isSidebarCollapsed ? '' : 'mr-3 lg:mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-xs lg:text-sm uppercase">Gudang Aset</span>
                    </NavLink>

                    <NavLink v-if="['super admin', 'admin'].includes(user.role)" :href="route('admin.item_requests.index')" :active="route().current('admin.item_requests.*')" @click="closeSidebarOnMobile" :collapsed="isSidebarCollapsed">
                        <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0 transition-transform group-hover:scale-110" :class="isSidebarCollapsed ? '' : 'mr-3 lg:mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-xs lg:text-sm uppercase">Audit Stok</span>
                    </NavLink>

                    <NavLink :href="requestsUrl" :active="route().current('requests.*') || route().current('admin.requests.*')" @click="closeSidebarOnMobile" :collapsed="isSidebarCollapsed">
                        <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0 transition-transform group-hover:scale-110" :class="isSidebarCollapsed ? '' : 'mr-3 lg:mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                        <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-xs lg:text-sm uppercase">Pengajuan Umum</span>
                    </NavLink>

                    <template v-if="['super admin', 'admin'].includes(user.role)">
                        <!-- ISO Section Header / Toggle -->
                        <div class="px-3 lg:px-5 mt-6 lg:mt-10 mb-1" v-if="!isSidebarCollapsed">
                            <button 
                                @click="isIsoMenuOpen = !isIsoMenuOpen"
                                class="w-full flex items-center justify-between group py-2"
                            >
                                <p class="text-[9px] lg:text-[10px] font-black uppercase text-pail-gold tracking-[0.25em] lg:tracking-[0.3em]">PROSEDUR ISO URT</p>
                                <svg 
                                    class="w-3 h-3 text-pail-gold transition-transform duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]" 
                                    :class="isIsoMenuOpen ? 'rotate-180' : ''"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Divider for collapsed sidebar -->
                        <div v-else class="mx-4 mt-6 mb-2 border-t border-gray-100 dark:border-gray-700/50 pt-4"></div>
                        
                        <Transition
                            enter-active-class="transition-all duration-500 ease-in-out"
                            enter-from-class="max-h-0 opacity-0 overflow-hidden"
                            enter-to-class="max-h-[800px] opacity-100 overflow-hidden"
                            leave-active-class="transition-all duration-400 ease-in-out"
                            leave-from-class="max-h-[800px] opacity-100 overflow-hidden"
                            leave-to-class="max-h-0 opacity-0 overflow-hidden"
                        >
                            <div v-if="isIsoMenuOpen || isSidebarCollapsed" class="space-y-1.5 lg:space-y-2 overflow-hidden">
                                <!-- 1. Modul Aset -->
                                <NavLink :href="route('admin.procedures.index', { group: 'aset' })" :active="$page.url.includes('group=aset')" @click="closeSidebarOnMobile" :collapsed="isSidebarCollapsed">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-[11px] lg:text-xs uppercase ml-3">ISO: Aset</span>
                                </NavLink>

                                <!-- 2. Modul Sarpras -->
                                <NavLink :href="route('admin.procedures.index', { group: 'sarpras' })" :active="$page.url.includes('group=sarpras')" @click="closeSidebarOnMobile" :collapsed="isSidebarCollapsed">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"></path></svg>
                                    <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-[11px] lg:text-xs uppercase ml-3">ISO: Sarpras</span>
                                </NavLink>

                                <!-- 3. Modul Proyek -->
                                <NavLink :href="route('admin.procedures.index', { group: 'proyek' })" :active="$page.url.includes('group=proyek')" @click="closeSidebarOnMobile" :collapsed="isSidebarCollapsed">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                    <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-[11px] lg:text-xs uppercase ml-3">ISO: Proyek</span>
                                </NavLink>

                                <!-- 4. Modul Logistik -->
                                <NavLink :href="route('admin.procedures.index', { group: 'logistik' })" :active="$page.url.includes('group=logistik')" @click="closeSidebarOnMobile" :collapsed="isSidebarCollapsed">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-[11px] lg:text-xs uppercase ml-3">ISO: Logistik</span>
                                </NavLink>

                                <!-- 5. Modul Kebersihan -->
                                <NavLink :href="route('admin.procedures.index', { group: 'kebersihan' })" :active="$page.url.includes('group=kebersihan')" @click="closeSidebarOnMobile" :collapsed="isSidebarCollapsed">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                                    <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-[11px] lg:text-xs uppercase ml-3">ISO: Kebersihan</span>
                                </NavLink>

                                <!-- 6. Modul Kendaraan & Lainnya -->
                                <NavLink :href="route('admin.procedures.index', { group: 'lainnya' })" :active="$page.url.includes('group=lainnya')" @click="closeSidebarOnMobile" :collapsed="isSidebarCollapsed">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                    <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-[11px] lg:text-xs uppercase ml-3">ISO: Lainnya</span>
                                </NavLink>

                                <!-- 7. Modul Audit ISO Master -->
                                <NavLink :href="route('admin.procedures.show', 'ceklist-iso')" :active="route().current('admin.procedures.show', 'ceklist-iso')" @click="closeSidebarOnMobile" :collapsed="isSidebarCollapsed">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-[11px] lg:text-xs uppercase ml-3">Master Ceklist ISO</span>
                                </NavLink>
                            </div>
                        </Transition>

                        <div class="px-3 lg:px-5 mt-6 lg:mt-10 mb-3 lg:mb-4" v-if="!isSidebarCollapsed">
                            <p class="text-[9px] lg:text-[10px] font-black uppercase text-gray-400 tracking-[0.25em] lg:tracking-[0.3em]">Intelijen Sistem</p>
                        </div>
                        <NavLink :href="route('admin.activity_log.index')" :active="route().current('admin.activity_log.*')" @click="closeSidebarOnMobile" :collapsed="isSidebarCollapsed">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0 transition-transform group-hover:scale-110" :class="isSidebarCollapsed ? '' : 'mr-3 lg:mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-xs lg:text-sm uppercase">Audit Trail</span>
                        </NavLink>
                        <NavLink :href="route('admin.reports.index')" :active="route().current('admin.reports.*')" @click="closeSidebarOnMobile" :collapsed="isSidebarCollapsed">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0 transition-transform group-hover:scale-110" :class="isSidebarCollapsed ? '' : 'mr-3 lg:mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-xs lg:text-sm uppercase">Analitik</span>
                        </NavLink>
                    </template>
                </nav>

                <!-- Sidebar Footer (Identity) -->
                <div class="p-3 lg:p-6 shrink-0 border-t border-gray-100 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-900/40 relative overflow-hidden backdrop-blur-md mb-24 lg:mb-0">
                    <div class="absolute -left-10 -bottom-10 w-32 h-32 bg-pail-gold opacity-5 rounded-full blur-3xl"></div>
                    <div class="flex items-center gap-2.5 lg:gap-4 relative z-10 p-2.5 lg:p-4 bg-white dark:bg-gray-800 rounded-2xl lg:rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1 duration-500">
                        <div class="w-9 h-9 lg:w-12 lg:h-12 min-w-[2.25rem] lg:min-w-[3rem] rounded-xl lg:rounded-2xl bg-gradient-to-br from-gray-900 to-black flex items-center justify-center text-pail-gold font-black text-sm lg:text-base shadow-lg shadow-black/20 shrink-0 transform transition-transform group-hover:scale-105">
                            {{ user.name.charAt(0) }}
                        </div>
                        <div v-if="!isSidebarCollapsed" class="flex-1 min-w-0">
                            <p class="text-[10px] lg:text-xs font-black text-gray-900 dark:text-white truncate uppercase tracking-tighter">{{ user.name }}</p>
                            <p class="text-[8px] lg:text-[9px] font-black text-pail-gold uppercase tracking-[0.15em] lg:tracking-[0.2em] inline-flex items-center gap-1.5">
                                <span class="w-1 h-1 rounded-full bg-green-500 animate-pulse"></span>
                                {{ user.role }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 min-h-screen bg-gray-50/50 dark:bg-gray-900">
            <!-- Glass Header -->
            <header class="h-20 bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl border-b border-gray-100 dark:border-gray-700/50 items-center justify-between px-4 sm:px-6 lg:px-12 sticky top-0 z-40 flex shadow-[0_1px_40px_-10px_rgba(0,0,0,0.05)]">
                <div class="flex items-center gap-6">
                    <button 
                        @click="isSidebarCollapsed = !isSidebarCollapsed" 
                        class="hidden lg:flex w-10 h-10 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 text-gray-400 hover:text-pail-gold hover:bg-white dark:hover:bg-gray-800 transition-all shadow-inner border border-gray-100 dark:border-gray-700"
                    >
                        <svg class="w-5 h-5 transition-transform duration-500" :class="isSidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                    </button>

                    <button 
                        @click="isSidebarOpen = !isSidebarOpen" 
                        class="lg:hidden w-10 h-10 flex items-center justify-center rounded-2xl bg-gray-50 text-gray-400 border border-gray-100 shadow-sm"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                    </button>

                    <nav class="hidden sm:flex items-center gap-3">
                        <span class="text-[9px] font-black text-gray-300 uppercase tracking-widest leading-none">Status</span>
                        <div class="px-3 py-1.5 rounded-full bg-green-50 dark:bg-green-900/10 border border-green-100 dark:border-green-800 text-[8px] font-black text-green-600 dark:text-green-500 uppercase tracking-[0.2em] flex items-center gap-2">
                            <span class="w-1 h-1 rounded-full bg-green-500 animate-ping"></span>
                            Node Aktif
                        </div>
                    </nav>
                </div>

                <div class="flex items-center gap-6">
                    <Dropdown align="right" width="60">
                        <template #trigger>
                            <button class="flex items-center gap-3 transition-opacity hover:opacity-80 group">
                                <div class="hidden md:flex flex-col items-end">
                                    <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{ user.name }}</span>
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ user.role }}</span>
                                </div>
                                <div class="w-10 h-10 rounded-2xl bg-pail-gold/10 text-pail-gold flex items-center justify-center font-black shadow-inner border border-pail-gold/20 group-hover:bg-pail-gold group-hover:text-white transition-all duration-500">
                                    {{ user.name.charAt(0) }}
                                </div>
                            </button>
                        </template>
                        <template #content>
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Masuk sebagai</p>
                                <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ user.email }}</p>
                            </div>
                            <DropdownLink :href="route('profile.edit')">
                                <div class="flex items-center gap-3 py-1">
                                    <SettingsIcon class="w-4 h-4 text-gray-400 group-hover:text-pail-gold" />
                                    <span>Konfigurasi Profil</span>
                                </div>
                            </DropdownLink>
                            <div class="border-t border-gray-100 dark:border-gray-700"></div>
                            <DropdownLink :href="route('logout')" method="post" as="button" class="text-red-500 font-bold">
                                <div class="flex items-center gap-3 py-1">
                                    <LogOutIcon class="w-4 h-4 text-red-400 group-hover:text-red-600" />
                                    <span>Keluar Sistem</span>
                                </div>
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <!-- Page-Specific Header Slot -->
            <header v-if="$slots.header" class="bg-white/50 dark:bg-gray-800/50 backdrop-blur-md border-b border-gray-100 dark:border-gray-700/50 transition-all duration-300">
                <div class="px-4 sm:px-8 lg:px-14 py-6 sm:py-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Main Scrollable Canvas -->
            <main class="flex-1 p-4 sm:p-8 lg:p-14 relative z-10 pb-32 lg:pb-14">
                <!-- Performance Grid Pattern Background -->
                <div class="fixed inset-0 pointer-events-none opacity-[0.03] dark:opacity-[0.05] z-0 overflow-hidden">
                    <div class="absolute inset-0" style="background-image: radial-gradient(#000 0.5px, transparent 0.5px); background-size: 24px 24px;"></div>
                </div>

                <!-- Flash Global Notifications -->
                <TransitionGroup
                    enter-active-class="transition duration-500 ease-out"
                    enter-from-class="opacity-0 -translate-y-4 scale-95"
                    enter-to-class="opacity-100 translate-y-0 scale-100"
                    leave-active-class="transition duration-300 ease-in"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 translate-y-4"
                >
                    <div v-if="$page.props.flash.success" key="success" class="mb-6 sm:mb-10 p-4 sm:p-6 bg-white dark:bg-gray-800 border-l-[6px] border-green-500 rounded-2xl sm:rounded-3xl shadow-xl shadow-green-500/5 flex items-center justify-between group">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-green-50 flex items-center justify-center shrink-0">
                                <CheckCircleIcon class="w-5 h-5 sm:w-6 sm:h-6 text-green-500" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-[8px] sm:text-[10px] font-black text-green-600 uppercase tracking-widest mb-0.5">Operasi Berhasil</p>
                                <p class="text-xs sm:text-sm text-gray-900 dark:text-white font-bold truncate">{{ $page.props.flash.success }}</p>
                            </div>
                        </div>
                        <button @click="$page.props.flash.success = null" class="w-8 h-8 rounded-xl hover:bg-gray-50 text-gray-300 hover:text-gray-600 transition-all font-bold shrink-0">✕</button>
                    </div>

                    <div v-if="$page.props.flash.error" key="error" class="mb-6 sm:mb-10 p-4 sm:p-6 bg-white dark:bg-gray-800 border-l-[6px] border-red-500 rounded-2xl sm:rounded-3xl shadow-xl shadow-red-500/5 flex items-center justify-between group">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                                <XCircleIcon class="w-5 h-5 sm:w-6 sm:h-6 text-red-500" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-[8px] sm:text-[10px] font-black text-red-600 uppercase tracking-widest mb-0.5">Gagal Sistem</p>
                                <p class="text-xs sm:text-sm text-gray-900 dark:text-white font-bold truncate">{{ $page.props.flash.error }}</p>
                            </div>
                        </div>
                        <button @click="$page.props.flash.error = null" class="w-8 h-8 rounded-xl hover:bg-gray-50 text-gray-300 hover:text-gray-600 transition-all font-bold shrink-0">✕</button>
                    </div>
                </TransitionGroup>

                <!-- Page Blueprint -->
                <div class="relative z-10 transition-all duration-700">
                    <slot />
                </div>
            </main>

            <!-- Sticky Footer Signature -->
            <footer class="shrink-0 px-4 sm:px-8 lg:px-14 py-8 pb-32 lg:pb-8 border-t border-gray-100 dark:border-gray-700/50 bg-white/30 dark:bg-gray-800/30 backdrop-blur-sm">
                <div class="flex flex-col lg:flex-row items-center justify-between gap-6">
                    <div class="flex flex-col items-center lg:items-start gap-1">
                        <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-[0.2em]">
                            © {{ new Date().getFullYear() }} Pondok Pesantren Abu Hurairah Mataram
                        </p>
                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none">
                            Sistem Informasi Manajemen - Unit Rumah Tangga
                        </p>
                    </div>
                    
                    <div class="flex flex-col lg:flex-row items-center gap-3 lg:gap-6 bg-gray-50 dark:bg-gray-900/50 px-6 py-3 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-inner">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em]">Development By</span>
                        <div class="flex items-center gap-4">
                            <a href="https://instagram.com/iq_html" target="_blank" class="group flex items-center gap-2 transition-all">
                                <div class="w-6 h-6 rounded-lg bg-pink-500/10 flex items-center justify-center text-pink-500 group-hover:bg-pink-500 group-hover:text-white transition-all shadow-sm">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.848 0-3.204.012-3.584.07-4.849.149-3.225 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                </div>
                                <span class="text-[10px] font-black text-gray-500 group-hover:text-pink-600 transition-colors tracking-tighter">@iq_html</span>
                            </a>
                            <div class="w-1 h-1 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                            <a href="https://linkedin.com/in/Iqbaladiatma" target="_blank" class="group flex items-center gap-2 transition-all">
                                <div class="w-6 h-6 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-500 group-hover:bg-blue-600 group-hover:text-white transition-all shadow-sm">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"/></svg>
                                </div>
                                <span class="text-[10px] font-black text-gray-500 group-hover:text-blue-600 transition-colors tracking-tighter">@Iqbaladiatma</span>
                            </a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        <!-- Mobile Smart Bottom Navigation -->
        <nav class="lg:hidden fixed bottom-6 left-6 right-6 bg-white/80 dark:bg-gray-800/80 backdrop-blur-2xl border border-white dark:border-gray-700 h-20 rounded-[2.5rem] shadow-[0_20px_50px_-15px_rgba(0,0,0,0.3)] flex items-center justify-around px-2 z-[70] overflow-hidden">
            <Link :href="dashboardUrl" class="flex flex-col items-center justify-center w-full h-full transition-all duration-500 rounded-3xl group" :class="route().current('dashboard') || route().current('admin.dashboard') ? 'text-pail-gold relative' : 'text-gray-400'">
                <div v-if="route().current('dashboard') || route().current('admin.dashboard')" class="absolute top-0 w-8 h-1 bg-pail-gold rounded-full"></div>
                <svg class="w-6 h-6 transform group-active:scale-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="text-[8px] font-black uppercase mt-1 tracking-widest">Beranda</span>
            </Link>

            <Link :href="itemsUrl" class="flex flex-col items-center justify-center w-full h-full transition-all duration-500 rounded-3xl group" :class="route().current('items.*') || route().current('admin.items.*') ? 'text-pail-gold relative' : 'text-gray-400'">
                <div v-if="route().current('items.*') || route().current('admin.items.*')" class="absolute top-0 w-8 h-1 bg-pail-gold rounded-full"></div>
                <svg class="w-6 h-6 transform group-active:scale-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <span class="text-[8px] font-black uppercase mt-1 tracking-widest">Gudang</span>
            </Link>

            <Link :href="requestsUrl" class="flex flex-col items-center justify-center w-full h-full transition-all duration-500 rounded-3xl group" :class="route().current('requests.*') || route().current('admin.requests.*') ? 'text-pail-gold relative' : 'text-gray-400'">
                <div v-if="route().current('requests.*') || route().current('admin.requests.*')" class="absolute top-0 w-8 h-1 bg-pail-gold rounded-full"></div>
                <svg class="w-6 h-6 transform group-active:scale-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span class="text-[8px] font-black uppercase mt-1 tracking-widest">Pengajuan</span>
            </Link>

            <button @click="isSidebarOpen = !isSidebarOpen" class="flex flex-col items-center justify-center w-full h-full transition-all rounded-3xl text-gray-400 group relative">
                <div v-if="isSidebarOpen" class="absolute top-0 w-8 h-1 bg-gray-400 rounded-full"></div>
                <svg class="w-6 h-6 transform group-active:scale-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                <span class="text-[8px] font-black uppercase mt-1 tracking-widest">Menu</span>
            </button>
        </nav>
    </div>
</template>

<style scoped>
/* Bottom navigation glass effect improvements */
nav {
    box-shadow: 0 -10px 40px -10px rgba(0,0,0,0.1);
}
</style>
