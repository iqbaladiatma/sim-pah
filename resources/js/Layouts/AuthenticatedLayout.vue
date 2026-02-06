<script setup>
import { ref, computed } from "vue";
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

// Auto-close sidebar on mobile when navigating
const closeSidebarOnMobile = () => {
    if (window.innerWidth < 1024) {
        isSidebarOpen.value = false;
    }
};

const dashboardUrl = computed(() => {
    return ['super admin', 'admin'].includes(user.value.role)
        ? route('admin.dashboard')
        : route('dashboard');
});

const user = computed(() => usePage().props.auth.user);

const itemsUrl = computed(() => ['super admin', 'admin'].includes(user.value.role) ? route('admin.items.index') : route('items.index'));
const requestsUrl = computed(() => ['super admin', 'admin'].includes(user.value.role) ? route('admin.requests.index') : route('requests.index'));
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
                isSidebarCollapsed ? 'lg:w-28' : 'lg:w-[22rem] w-[75vw] max-w-xs'
            ]"
        >
            <div class="h-full flex flex-col overflow-hidden">
                <!-- Sidebar Branding -->
                <div class="h-20 lg:h-24 flex items-center px-4 lg:px-8 shrink-0 relative overflow-hidden">
                    <Link :href="dashboardUrl" class="flex items-center gap-3">
                        <ApplicationLogo class="h-10 w-10 lg:h-12 lg:w-12" />
                        <div v-if="!isSidebarCollapsed" class="flex flex-col">
                            <span class="font-black text-lg lg:text-2xl text-gray-900 dark:text-white tracking-tighter leading-none mb-0.5 lg:mb-1">SIM PAH</span>
                            <span class="text-[8px] lg:text-[9px] font-black text-pail-gold uppercase tracking-[0.25em] lg:tracking-[0.3em] opacity-60">Edisi Mataram</span>
                        </div>
                    </Link>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 px-3 lg:px-4 py-4 lg:py-8 space-y-1.5 lg:space-y-2 overflow-y-auto scrollbar-hide pb-48 lg:pb-32 scroll-smooth">
                    <div class="px-3 lg:px-5 mb-3 lg:mb-4" v-if="!isSidebarCollapsed">
                        <p class="text-[9px] lg:text-[10px] font-black uppercase text-gray-400 tracking-[0.25em] lg:tracking-[0.3em]">Konsol Utama</p>
                    </div>

                    <NavLink :href="dashboardUrl" :active="route().current('dashboard') || route().current('admin.dashboard')" @click="closeSidebarOnMobile">
                        <div class="flex items-center w-full" :class="isSidebarCollapsed ? 'justify-center py-2' : 'px-2 py-1.5 lg:py-1'">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0 transition-transform group-hover:scale-110" :class="isSidebarCollapsed ? '' : 'mr-3 lg:mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-xs lg:text-sm uppercase">Beranda</span>
                        </div>
                    </NavLink>

                    <!-- Admin Management Section -->
                    <template v-if="['super admin', 'admin'].includes(user.role)">
                        <div class="px-3 lg:px-5 mt-6 lg:mt-10 mb-3 lg:mb-4" v-if="!isSidebarCollapsed">
                            <p class="text-[9px] lg:text-[10px] font-black uppercase text-gray-400 tracking-[0.25em] lg:tracking-[0.3em]">Pusat Institusi</p>
                        </div>
                        <NavLink :href="route('admin.institutions.index')" :active="route().current('admin.institutions.*')" @click="closeSidebarOnMobile">
                            <div class="flex items-center w-full" :class="isSidebarCollapsed ? 'justify-center py-2' : 'px-2 py-1.5 lg:py-1'">
                                <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0 transition-transform group-hover:scale-110" :class="isSidebarCollapsed ? '' : 'mr-3 lg:mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-xs lg:text-sm uppercase">Lembaga</span>
                            </div>
                        </NavLink>
                        <NavLink :href="route('admin.rooms.index')" :active="route().current('admin.rooms.*')" @click="closeSidebarOnMobile">
                            <div class="flex items-center w-full" :class="isSidebarCollapsed ? 'justify-center py-2' : 'px-2 py-1.5 lg:py-1'">
                                <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0 transition-transform group-hover:scale-110" :class="isSidebarCollapsed ? '' : 'mr-3 lg:mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-xs lg:text-sm uppercase">Ruangan</span>
                            </div>
                        </NavLink>
                        <NavLink :href="route('admin.users.index')" :active="route().current('admin.users.*')" @click="closeSidebarOnMobile">
                            <div class="flex items-center w-full" :class="isSidebarCollapsed ? 'justify-center py-2' : 'px-2 py-1.5 lg:py-1'">
                                <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0 transition-transform group-hover:scale-110" :class="isSidebarCollapsed ? '' : 'mr-3 lg:mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-xs lg:text-sm uppercase">Sdm & Akses</span>
                            </div>
                        </NavLink>
                    </template>

                    <div class="px-3 lg:px-5 mt-6 lg:mt-10 mb-3 lg:mb-4" v-if="!isSidebarCollapsed">
                        <p class="text-[9px] lg:text-[10px] font-black uppercase text-gray-400 tracking-[0.25em] lg:tracking-[0.3em]">Mesin Logistik</p>
                    </div>

                    <NavLink :href="itemsUrl" :active="route().current('items.*') || route().current('admin.items.*')" @click="closeSidebarOnMobile">
                        <div class="flex items-center w-full" :class="isSidebarCollapsed ? 'justify-center py-2' : 'px-2 py-1.5 lg:py-1'">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0 transition-transform group-hover:scale-110" :class="isSidebarCollapsed ? '' : 'mr-3 lg:mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-xs lg:text-sm uppercase">Gudang Aset</span>
                        </div>
                    </NavLink>

                    <NavLink v-if="['super admin', 'admin'].includes(user.role)" :href="route('admin.item_requests.index')" :active="route().current('admin.item_requests.*')" @click="closeSidebarOnMobile">
                        <div class="flex items-center w-full" :class="isSidebarCollapsed ? 'justify-center py-2' : 'px-2 py-1.5 lg:py-1'">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0 transition-transform group-hover:scale-110" :class="isSidebarCollapsed ? '' : 'mr-3 lg:mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-xs lg:text-sm uppercase">Audit Stok</span>
                        </div>
                    </NavLink>

                    <NavLink :href="requestsUrl" :active="route().current('requests.*') || route().current('admin.requests.*')" @click="closeSidebarOnMobile">
                        <div class="flex items-center w-full" :class="isSidebarCollapsed ? 'justify-center py-2' : 'px-2 py-1.5 lg:py-1'">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0 transition-transform group-hover:scale-110" :class="isSidebarCollapsed ? '' : 'mr-3 lg:mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                            <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-xs lg:text-sm uppercase">Pengajuan Umum</span>
                        </div>
                    </NavLink>

                    <template v-if="['super admin', 'admin'].includes(user.role)">
                        <div class="px-3 lg:px-5 mt-6 lg:mt-10 mb-3 lg:mb-4" v-if="!isSidebarCollapsed">
                            <p class="text-[9px] lg:text-[10px] font-black uppercase text-gray-400 tracking-[0.25em] lg:tracking-[0.3em]">Intelijen Sistem</p>
                        </div>
                        <NavLink :href="route('admin.activity_log.index')" :active="route().current('admin.activity_log.*')" @click="closeSidebarOnMobile">
                            <div class="flex items-center w-full" :class="isSidebarCollapsed ? 'justify-center py-2' : 'px-2 py-1.5 lg:py-1'">
                                <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0 transition-transform group-hover:scale-110" :class="isSidebarCollapsed ? '' : 'mr-3 lg:mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-xs lg:text-sm uppercase">Audit Trail</span>
                            </div>
                        </NavLink>
                        <NavLink :href="route('admin.reports.index')" :active="route().current('admin.reports.*')" @click="closeSidebarOnMobile">
                            <div class="flex items-center w-full" :class="isSidebarCollapsed ? 'justify-center py-2' : 'px-2 py-1.5 lg:py-1'">
                                <svg class="w-4 h-4 lg:w-5 lg:h-5 shrink-0 transition-transform group-hover:scale-110" :class="isSidebarCollapsed ? '' : 'mr-3 lg:mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span v-if="!isSidebarCollapsed" class="font-black tracking-tighter text-xs lg:text-sm uppercase">Analitik</span>
                            </div>
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
                                    <SettingsIcon className="w-4 h-4 text-gray-400 group-hover:text-pail-gold" />
                                    <span>Konfigurasi Profil</span>
                                </div>
                            </DropdownLink>
                            <div class="border-t border-gray-100 dark:border-gray-700"></div>
                            <DropdownLink :href="route('logout')" method="post" as="button" class="text-red-500 font-bold">
                                <div class="flex items-center gap-3 py-1">
                                    <LogOutIcon className="w-4 h-4 text-red-400 group-hover:text-red-600" />
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
                                <CheckCircleIcon className="w-5 h-5 sm:w-6 sm:h-6 text-green-500" />
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
                                <XCircleIcon className="w-5 h-5 sm:w-6 sm:h-6 text-red-500" />
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
