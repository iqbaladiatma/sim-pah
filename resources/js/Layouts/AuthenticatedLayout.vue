<script setup>
import { ref, computed } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import { Link, usePage } from "@inertiajs/vue3";

const isSidebarOpen = ref(false);

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
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col lg:flex-row">
        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 z-[60] w-72 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-transform duration-300 transform lg:translate-x-0 lg:static lg:inset-0"
            :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="h-full flex flex-col pb-28 lg:pb-0">
                <!-- Sidebar Header -->
                <div class="h-16 flex items-center px-8 border-b border-gray-100 dark:border-gray-700">
                    <Link :href="dashboardUrl" class="flex items-center gap-3">
                        <ApplicationLogo class="h-8 w-auto fill-current text-pail-gold" />
                        <span class="font-black text-xl text-gray-900 dark:text-white tracking-tighter">SIM PAH</span>
                    </Link>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 px-4 py-8 space-y-1.5 overflow-y-auto">
                    <div class="px-4 mb-2">
                        <p class="text-[10px] font-black uppercase text-gray-400 tracking-[0.2em]">Menu Utama</p>
                    </div>

                    <NavLink :href="dashboardUrl" :active="route().current('dashboard') || route().current('admin.dashboard')">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <span>Dashboard</span>
                        </div>
                    </NavLink>

                    <!-- Admin Management Section -->
                    <template v-if="['super admin', 'admin'].includes(user.role)">
                        <div class="px-4 mt-6 mb-2">
                            <p class="text-[10px] font-black uppercase text-gray-400 tracking-[0.2em]">Master Data</p>
                        </div>
                        <NavLink :href="route('admin.institutions.index')" :active="route().current('admin.institutions.*')">
                            Lembaga
                        </NavLink>
                        <NavLink :href="route('admin.rooms.index')" :active="route().current('admin.rooms.*')">
                            Ruangan
                        </NavLink>
                        <NavLink :href="route('admin.users.index')" :active="route().current('admin.users.*')">
                            Users
                        </NavLink>
                    </template>

                    <div class="px-4 mt-6 mb-2">
                        <p class="text-[10px] font-black uppercase text-gray-400 tracking-[0.2em]">Operasional</p>
                    </div>

                    <NavLink :href="itemsUrl" :active="route().current('items.*') || route().current('admin.items.*')">
                        Data Barang
                    </NavLink>

                    <NavLink v-if="['super admin', 'admin'].includes(user.role)" :href="route('admin.item_requests.index')" :active="route().current('admin.item_requests.*')">
                        Persetujuan Stok
                    </NavLink>

                    <NavLink :href="requestsUrl" :active="route().current('requests.*') || route().current('admin.requests.*')">
                        Pengajuan Umum
                    </NavLink>

                    <template v-if="['super admin', 'admin'].includes(user.role)">
                        <div class="px-4 mt-6 mb-2 text-gray-400 uppercase tracking-widest text-[10px] font-black">Sistem</div>
                        <NavLink :href="route('admin.logs.index')" :active="route().current('admin.logs.*')">Log Aktivitas</NavLink>
                        <NavLink :href="route('admin.reports.index')" :active="route().current('admin.reports.*')">Laporan Unit</NavLink>
                    </template>
                </nav>

                <!-- Sidebar Footer -->
                <div class="p-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/20">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-pail-gold flex items-center justify-center text-white font-black shadow-lg shadow-pail-gold/20">
                            {{ user.name.charAt(0) }}
                        </div>
                        <div class="flex-1 min-w-0">
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
            <header class="hidden lg:flex h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 items-center justify-between px-8 sticky top-0 z-40">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em]">
                    Sistem Manajemen URT
                </div>

                <div class="flex items-center gap-4">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="flex items-center text-sm font-bold text-gray-600 hover:text-pail-gold transition">
                                <span>Pengaturan</span>
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
