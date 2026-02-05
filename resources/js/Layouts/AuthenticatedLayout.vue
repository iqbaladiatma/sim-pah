<script setup>
import { ref, computed } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import { Link, usePage } from "@inertiajs/vue3";

const isSidebarOpen = ref(false);
const isSidebarCollapsed = ref(false);

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
        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 z-[60] bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 ease-in-out transform lg:translate-x-0 lg:static lg:inset-0"
            :class="[
                isSidebarOpen ? 'translate-x-0' : '-translate-x-full',
                isSidebarCollapsed ? 'lg:w-24' : 'lg:w-72 w-72'
            ]"
        >
            <div class="h-full flex flex-col overflow-x-hidden pb-28 lg:pb-0">
                <!-- Sidebar Header -->
                <div class="h-16 flex items-center px-6 border-b border-gray-100 dark:border-gray-700 overflow-hidden">
                    <Link :href="dashboardUrl" class="flex items-center gap-3">
                        <ApplicationLogo class="h-8 w-8 min-w-[2rem] fill-current text-pail-gold shrink-0 transition-transform duration-300" :class="isSidebarCollapsed ? 'mx-auto' : ''" />
                        <span v-if="!isSidebarCollapsed" class="font-black text-xl text-gray-900 dark:text-white tracking-tighter whitespace-nowrap transition-opacity duration-300">SIM PAH</span>
                    </Link>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 px-3 py-6 space-y-1.5 overflow-y-auto overflow-x-hidden scrollbar-hide">
                    <div class="px-3 mb-2" v-if="!isSidebarCollapsed">
                        <p class="text-[10px] font-black uppercase text-gray-400 tracking-[0.2em] whitespace-nowrap">Menu Utama</p>
                    </div>

                    <NavLink :href="dashboardUrl" :active="route().current('dashboard') || route().current('admin.dashboard')">
                        <div class="flex items-center min-w-0" :class="isSidebarCollapsed ? 'justify-center mx-auto' : ''">
                            <svg class="w-5 h-5 shrink-0" :class="isSidebarCollapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <span v-if="!isSidebarCollapsed" class="whitespace-nowrap transition-opacity duration-300">Dashboard</span>
                        </div>
                    </NavLink>

                    <!-- Admin Management Section -->
                    <template v-if="['super admin', 'admin'].includes(user.role)">
                        <div class="px-3 mt-6 mb-2" v-if="!isSidebarCollapsed">
                            <p class="text-[10px] font-black uppercase text-gray-400 tracking-[0.2em] whitespace-nowrap">Master Data</p>
                        </div>
                        <NavLink :href="route('admin.institutions.index')" :active="route().current('admin.institutions.*')">
                            <div class="flex items-center min-w-0" :class="isSidebarCollapsed ? 'justify-center mx-auto' : ''">
                                <svg class="w-5 h-5 shrink-0" :class="isSidebarCollapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span v-if="!isSidebarCollapsed" class="whitespace-nowrap transition-opacity duration-300">Lembaga</span>
                            </div>
                        </NavLink>
                        <NavLink :href="route('admin.rooms.index')" :active="route().current('admin.rooms.*')">
                            <div class="flex items-center min-w-0" :class="isSidebarCollapsed ? 'justify-center mx-auto' : ''">
                                <svg class="w-5 h-5 shrink-0" :class="isSidebarCollapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span v-if="!isSidebarCollapsed" class="whitespace-nowrap transition-opacity duration-300">Ruangan</span>
                            </div>
                        </NavLink>
                        <NavLink :href="route('admin.users.index')" :active="route().current('admin.users.*')">
                            <div class="flex items-center min-w-0" :class="isSidebarCollapsed ? 'justify-center mx-auto' : ''">
                                <svg class="w-5 h-5 shrink-0" :class="isSidebarCollapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <span v-if="!isSidebarCollapsed" class="whitespace-nowrap transition-opacity duration-300">Users</span>
                            </div>
                        </NavLink>
                    </template>

                    <div class="px-3 mt-6 mb-2" v-if="!isSidebarCollapsed">
                        <p class="text-[10px] font-black uppercase text-gray-400 tracking-[0.2em] whitespace-nowrap">Operasional</p>
                    </div>

                    <NavLink :href="itemsUrl" :active="route().current('items.*') || route().current('admin.items.*')">
                        <div class="flex items-center min-w-0" :class="isSidebarCollapsed ? 'justify-center mx-auto' : ''">
                            <svg class="w-5 h-5 shrink-0" :class="isSidebarCollapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <span v-if="!isSidebarCollapsed" class="whitespace-nowrap transition-opacity duration-300">Data Barang</span>
                        </div>
                    </NavLink>

                    <NavLink v-if="['super admin', 'admin'].includes(user.role)" :href="route('admin.item_requests.index')" :active="route().current('admin.item_requests.*')">
                        <div class="flex items-center min-w-0" :class="isSidebarCollapsed ? 'justify-center mx-auto' : ''">
                            <svg class="w-5 h-5 shrink-0" :class="isSidebarCollapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            <span v-if="!isSidebarCollapsed" class="whitespace-nowrap transition-opacity duration-300">Persetujuan Stok</span>
                        </div>
                    </NavLink>

                    <NavLink :href="requestsUrl" :active="route().current('requests.*') || route().current('admin.requests.*')">
                        <div class="flex items-center min-w-0" :class="isSidebarCollapsed ? 'justify-center mx-auto' : ''">
                            <svg class="w-5 h-5 shrink-0" :class="isSidebarCollapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                            <span v-if="!isSidebarCollapsed" class="whitespace-nowrap transition-opacity duration-300">Pengajuan Umum</span>
                        </div>
                    </NavLink>

                    <template v-if="['super admin', 'admin'].includes(user.role)">
                        <div class="px-3 mt-6 mb-2 text-gray-400 uppercase tracking-widest text-[10px] font-black whitespace-nowrap" v-if="!isSidebarCollapsed">Sistem</div>
                        <NavLink :href="route('admin.logs.index')" :active="route().current('admin.logs.*')">
                            <div class="flex items-center min-w-0" :class="isSidebarCollapsed ? 'justify-center mx-auto' : ''">
                                <svg class="w-5 h-5 shrink-0" :class="isSidebarCollapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span v-if="!isSidebarCollapsed" class="whitespace-nowrap transition-opacity duration-300">Log Aktivitas</span>
                            </div>
                        </NavLink>
                        <NavLink :href="route('admin.reports.index')" :active="route().current('admin.reports.*')">
                            <div class="flex items-center min-w-0" :class="isSidebarCollapsed ? 'justify-center mx-auto' : ''">
                                <svg class="w-5 h-5 shrink-0" :class="isSidebarCollapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span v-if="!isSidebarCollapsed" class="whitespace-nowrap transition-opacity duration-300">Laporan Unit</span>
                            </div>
                        </NavLink>
                    </template>
                </nav>

                <!-- Sidebar Footer -->
                <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/20 overflow-hidden">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 min-w-[2.5rem] rounded-xl bg-pail-gold flex items-center justify-center text-white font-black shadow-lg shadow-pail-gold/20 shrink-0">
                            {{ user.name.charAt(0) }}
                        </div>
                        <div v-if="!isSidebarCollapsed" class="flex-1 min-w-0 transition-opacity duration-300">
                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ user.name }}</p>
                            <p class="text-[10px] font-black text-pail-gold uppercase tracking-wider">{{ user.role }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 min-h-screen">
            <!-- Desktop/Tablet Header -->
            <header class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 items-center justify-between px-4 lg:px-8 sticky top-0 z-40 flex">
                <div class="flex items-center gap-4">
                    <!-- Sidebar Toggle (Desktop & Mobile) -->
                    <button 
                        @click="isSidebarCollapsed = !isSidebarCollapsed" 
                        class="hidden lg:flex p-2 rounded-xl text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    >
                        <svg class="w-6 h-6 transition-transform duration-300" :class="isSidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                    </button>

                    <button 
                        @click="isSidebarOpen = !isSidebarOpen" 
                        class="lg:hidden p-2 rounded-xl text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                    </button>

                    <div class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em] hidden sm:block">
                        Sistem Manajemen URT
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="flex items-center text-sm font-bold text-gray-600 dark:text-gray-300 hover:text-pail-gold transition">
                                <span class="hidden md:inline">{{ user.name }}</span>
                                <div class="md:hidden w-8 h-8 rounded-full bg-pail-gold/10 text-pail-gold flex items-center justify-center font-bold">{{ user.name.charAt(0) }}</div>
                                <svg class="ms-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </button>
                        </template>
                        <template #content>
                            <DropdownLink :href="route('profile.edit')"> Profil </DropdownLink>
                            <div class="border-t border-gray-100 dark:border-gray-700"></div>
                            <DropdownLink :href="route('logout')" method="post" as="button" class="text-red-600"> Keluar </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <!-- Page Heading -->
            <div v-if="$slots.header" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 lg:shadow-sm">
                <div class="px-6 py-6 lg:px-10">
                    <slot name="header" />
                </div>
            </div>

            <!-- Page Content -->
            <main class="flex-1 p-6 lg:p-10 mb-20 lg:mb-0">
                <!-- Flash Messages -->
                <div v-if="$page.props.flash.success" class="mb-8 p-4 bg-green-50 border-l-4 border-green-500 rounded-2xl flex items-center justify-between">
                    <p class="text-sm text-green-700 font-bold">✅ {{ $page.props.flash.success }}</p>
                    <button @click="$page.props.flash.success = null" class="text-green-500 hover:text-green-700">✕</button>
                </div>
                <div v-if="$page.props.flash.error" class="mb-8 p-4 bg-red-50 border-l-4 border-red-500 rounded-2xl flex items-center justify-between">
                    <p class="text-sm text-red-700 font-bold">❌ {{ $page.props.flash.error }}</p>
                    <button @click="$page.props.flash.error = null" class="text-red-500 hover:text-red-700">✕</button>
                </div>

                <slot />
            </main>
        </div>

        <!-- Mobile Bottom Nav -->
        <nav class="lg:hidden fixed bottom-4 left-4 right-4 bg-white/90 dark:bg-gray-800/90 backdrop-blur-lg border border-gray-200 dark:border-gray-700 h-16 rounded-2xl shadow-2xl flex items-center justify-around px-2 z-[70]">
            <Link :href="dashboardUrl" class="flex flex-col items-center justify-center w-full h-full transition rounded-xl" :class="route().current('dashboard') || route().current('admin.dashboard') ? 'text-pail-gold bg-pail-gold/5' : 'text-gray-400'">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="text-[9px] font-black uppercase mt-1">Beranda</span>
            </Link>

            <Link :href="itemsUrl" class="flex flex-col items-center justify-center w-full h-full transition rounded-xl" :class="route().current('items.*') || route().current('admin.items.*') ? 'text-pail-gold bg-pail-gold/5' : 'text-gray-400'">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <span class="text-[9px] font-black uppercase mt-1">Stok</span>
            </Link>

            <Link :href="requestsUrl" class="flex flex-col items-center justify-center w-full h-full transition rounded-xl" :class="route().current('requests.*') || route().current('admin.requests.*') ? 'text-pail-gold bg-pail-gold/5' : 'text-gray-400'">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <span class="text-[9px] font-black uppercase mt-1">Request</span>
            </Link>

            <button @click="isSidebarOpen = !isSidebarOpen" class="flex flex-col items-center justify-center w-full h-full transition rounded-xl" :class="isSidebarOpen ? 'text-pail-gold bg-pail-gold/5' : 'text-gray-400'">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                <span class="text-[9px] font-black uppercase mt-1">Menu</span>
            </button>
        </nav>

        <!-- Overlay for mobile sidebar -->
        <div 
            v-if="isSidebarOpen" 
            @click="isSidebarOpen = false"
            class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm lg:hidden transition-opacity duration-300"
        ></div>
    </div>
</template>

<style scoped>
/* Bottom navigation glass effect improvements */
nav {
    box-shadow: 0 -10px 40px -10px rgba(0,0,0,0.1);
}
</style>
