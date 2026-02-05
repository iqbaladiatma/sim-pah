<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: Object,
    recent_requests: Array,
    recent_activity: Array,
});

const user = usePage().props.auth.user;

const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Selamat Pagi';
    if (hour < 18) return 'Selamat Siang';
    return 'Selamat Malam';
});

const formatTime = (dateString) => {
    return new Date(dateString).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-200">
                Dashboard URT - Mataram
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-10">
                
                <!-- Hero Section -->
                <div class="relative bg-gradient-to-br from-gray-900 to-gray-800 rounded-[2.5rem] p-10 shadow-2xl overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-pail-gold opacity-10 rounded-full blur-3xl"></div>
                    <div class="relative z-10">
                        <h1 class="text-3xl font-black text-white mb-2">{{ greeting }}, {{ user.name }}!</h1>
                        <p class="text-gray-400 max-w-xl">
                            Selamat datang di Sistem Informasi Manajemen Pondok Abu Hurairah. 
                            Pantau semua aktivitas inventaris dan pengajuan dari sini.
                        </p>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <!-- Institutions Card -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-2xl text-blue-600 dark:text-blue-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Lembaga</span>
                        </div>
                        <div class="text-3xl font-black text-gray-900 dark:text-white">{{ stats.total_institutions }}</div>
                        <p class="text-xs text-gray-500 mt-1">Total Unit Terdaftar</p>
                    </div>

                    <!-- Users Card -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-2xl text-purple-600 dark:text-purple-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pengguna</span>
                        </div>
                        <div class="text-3xl font-black text-gray-900 dark:text-white">{{ stats.total_users }}</div>
                        <p class="text-xs text-gray-500 mt-1">Akun Aktif</p>
                    </div>

                    <!-- Pending Requests Card -->
                    <Link :href="route('admin.requests.index')" class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition group relative overflow-hidden">
                        <div v-if="stats.pending_requests > 0" class="absolute top-0 right-0 w-16 h-16 bg-red-500 rounded-bl-full -mr-8 -mt-8 opacity-20 animate-pulse"></div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-2xl text-red-600 dark:text-red-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider group-hover:text-red-500 transition-colors">Pending</span>
                        </div>
                        <div class="text-3xl font-black text-gray-900 dark:text-white">{{ stats.pending_requests }}</div>
                        <p class="text-xs text-gray-500 mt-1 group-hover:text-red-500 transition-colors">Menunggu Persetujuan</p>
                    </Link>

                    <!-- Low Stock Card -->
                    <Link :href="route('admin.items.index')" class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-2xl text-orange-600 dark:text-orange-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider group-hover:text-orange-500 transition-colors">Low Stock</span>
                        </div>
                        <div class="text-3xl font-black text-gray-900 dark:text-white">{{ stats.low_stock_items }}</div>
                        <p class="text-xs text-gray-500 mt-1 group-hover:text-orange-500 transition-colors">Barang Menipis</p>
                    </Link>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Recent Requests -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-black text-gray-900 dark:text-white">Pengajuan Terbaru</h3>
                            <Link :href="route('admin.requests.index')" class="text-sm font-bold text-pail-gold hover:text-yellow-600 flex items-center gap-1">
                                Lihat Semua
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </Link>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                            <div v-if="recent_requests.length === 0" class="p-10 text-center text-gray-400">
                                <p class="text-sm italic">Belum ada pengajuan masuk.</p>
                            </div>
                            <div v-else class="divide-y divide-gray-100 dark:divide-gray-700">
                                <div v-for="req in recent_requests" :key="req.id" class="p-6 hover:bg-gray-50 dark:hover:bg-gray-900/50 transition flex items-center justify-between group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center font-bold text-gray-500 text-xs">
                                            {{ req.user?.institution?.code?.substring(0, 2) || 'AD' }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 dark:text-white text-sm group-hover:text-pail-gold transition-colors">{{ req.title }}</h4>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] font-bold uppercase text-gray-400 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded">{{ req.type }}</span>
                                                <span class="text-xs text-gray-500">{{ req.user?.institution?.name || 'Admin Pusat' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <span class="px-3 py-1 text-[10px] font-black uppercase tracking-wider rounded-lg"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800': req.status === 'pending',
                                                'bg-green-100 text-green-800': req.status === 'approved',
                                                'bg-red-100 text-red-800': req.status === 'rejected'
                                            }">
                                            {{ req.status }}
                                        </span>
                                        <Link :href="route('admin.requests.edit', req.id)" class="text-gray-300 hover:text-pail-gold">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Side Activity & Actions -->
                    <div class="space-y-8">
                        
                        <!-- Quick Actions -->
                        <div>
                            <h3 class="text-lg font-black text-gray-900 dark:text-white mb-4">Aksi Cepat</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <Link :href="route('admin.users.create')" class="p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md hover:border-pail-gold/50 transition text-center group">
                                    <div class="w-10 h-10 mx-auto bg-gray-50 dark:bg-gray-700 text-gray-500 group-hover:bg-pail-gold group-hover:text-white rounded-full flex items-center justify-center transition-colors mb-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                    </div>
                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-400">Add User</span>
                                </Link>
                                <Link :href="route('admin.institutions.create')" class="p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md hover:border-pail-gold/50 transition text-center group">
                                    <div class="w-10 h-10 mx-auto bg-gray-50 dark:bg-gray-700 text-gray-500 group-hover:bg-pail-gold group-hover:text-white rounded-full flex items-center justify-center transition-colors mb-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </div>
                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-400">Add Unit</span>
                                </Link>
                                <Link :href="route('admin.requests.create')" class="col-span-2 p-4 bg-pail-gold rounded-2xl shadow-lg shadow-pail-gold/20 hover:shadow-xl hover:bg-yellow-600 transition text-center flex items-center justify-center gap-2 text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    <span class="font-bold text-sm">Buat Pengajuan (Manual)</span>
                                </Link>
                            </div>
                        </div>

                        <!-- Recent Logs -->
                        <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                            <h3 class="text-sm font-black uppercase text-gray-400 tracking-wider mb-4">Aktivitas Terkini</h3>
                            <ul class="space-y-4">
                                <li v-for="log in recent_activity" :key="log.id" class="flex gap-3 relative">
                                    <div class="absolute left-[5px] top-6 bottom-[-16px] w-0.5 bg-gray-100 dark:bg-gray-700 last:hidden"></div>
                                    <div class="w-3 h-3 rounded-full bg-gray-200 dark:bg-gray-600 mt-1.5 shrink-0 border-2 border-white dark:border-gray-800 ring-1 ring-gray-100 dark:ring-gray-700"></div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-800 dark:text-gray-300 line-clamp-2">
                                            <span class="font-bold text-gray-900 dark:text-white">{{ log.causer?.name || 'System' }}</span>: {{ log.description }}
                                        </p>
                                        <span class="text-[10px] text-gray-400">{{ formatTime(log.created_at) }}</span>
                                    </div>
                                </li>
                                <li v-if="recent_activity.length === 0" class="text-xs text-gray-400 italic">Tidak ada aktivitas.</li>
                            </ul>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
