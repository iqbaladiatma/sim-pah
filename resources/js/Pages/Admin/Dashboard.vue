<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { formatRupiah } from "@/Utils/format";

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

const formatDateDay = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                    Dashboard Overview
                </h2>
                <div class="flex items-center gap-2">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    <span class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ stats.online_users }} ONLINE</span>
                </div>
            </div>
        </template>

        <div class="pt-6 pb-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
                
                <!-- Welcome Section -->
                <div class="relative bg-gradient-to-br from-[#D4B876] via-pail-gold to-[#B89648] rounded-[3rem] p-10 shadow-2xl border border-white/20 overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-white opacity-10 rounded-full blur-[100px]"></div>
                    <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-80 h-80 bg-black opacity-5 rounded-full blur-[100px]"></div>
                    
                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div>
                            <h1 class="text-4xl font-black text-white mb-2 tracking-tighter">{{ greeting }}, {{ user.name.split(' ')[0] }}!</h1>
                            <p class="text-white/90 max-w-lg leading-relaxed font-medium">
                                Sistem Informasi Manajemen URT Mataram berjalan normal. Ada <span class="text-white font-black underline">{{ stats.pending_requests }} pengajuan</span> yang menunggu keputusan Anda hari ini.
                            </p>
                        </div>
                        <div class="flex gap-3">
                            <Link :href="route('admin.requests.index')" class="px-8 py-4 bg-white text-pail-gold rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-gray-100 transition-all shadow-xl shadow-black/10">
                                Proses Pengajuan
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Primary Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm relative group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                            <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Lembaga</h4>
                        <div class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter">{{ stats.total_institutions }}</div>
                        <div class="mt-4 flex items-center gap-1 text-[10px] font-bold text-gray-400 uppercase">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            Unit Aktif Terdaftar
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm relative group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                            <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Karyawan</h4>
                        <div class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter">{{ stats.total_users }}</div>
                        <div class="mt-4 flex items-center gap-1 text-[10px] font-bold text-gray-400 uppercase">
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                            Pengguna Sistem
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm relative group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                            <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Inventaris</h4>
                        <div class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter">{{ stats.total_items }}</div>
                        <div class="mt-4 flex items-center gap-1 text-[10px] font-bold text-gray-400 uppercase">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                            Item Terinventarisir
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden">
                        <div class="absolute inset-0 bg-red-500/5 dark:bg-red-500/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Barang Menipis</h4>
                        <div class="text-4xl font-black text-red-600 dark:text-red-500 tracking-tighter">{{ stats.low_stock_items }}</div>
                        <div class="mt-4 flex items-center gap-1 text-[10px] font-bold text-red-500 uppercase italic">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                            Segera Restock!
                        </div>
                    </div>
                </div>

                <!-- Financial & Inventory Detail -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col justify-between">
                        <div>
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Est. Biaya Pending</h4>
                            <div class="text-3xl font-black text-pail-gold tracking-tight">{{ formatRupiah(stats.total_cost_pending) }}</div>
                        </div>
                        <div class="mt-8">
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Biaya Disetujui</div>
                            <div class="text-xl font-bold text-gray-700 dark:text-gray-300 tracking-tight">{{ formatRupiah(stats.total_cost_approved) }}</div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-[#E8D4A2] to-pail-gold p-8 rounded-[2.5rem] shadow-xl shadow-pail-gold/10 flex flex-col justify-between">
                        <div>
                            <h4 class="text-[10px] font-black text-white uppercase tracking-widest mb-1">Inventory Alert</h4>
                            <div class="text-4xl font-black text-white tracking-tight">{{ stats.out_of_stock }}</div>
                            <p class="text-xs text-black/50 font-black uppercase tracking-wider mt-1">Item Habis (Stock 0)</p>
                        </div>
                        <Link :href="route('admin.items.index')" class="mt-8 text-xs font-black text-white uppercase tracking-widest flex items-center gap-2 group underline">
                            Cek Gudang Sekarang
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </Link>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-6">
                        <div class="w-16 h-16 rounded-3xl bg-green-50 dark:bg-green-900/20 flex items-center justify-center text-green-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Session Aktif</h4>
                            <div class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">{{ stats.online_users }} User</div>
                            <p class="text-[10px] text-green-500 font-bold uppercase mt-1 tracking-wider">Realtime Connected</p>
                        </div>
                    </div>
                </div>

                <!-- Bottom Section: Latest & Activity -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Recent Requests -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="flex items-center justify-between px-2">
                            <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">Pengajuan Terbaru</h3>
                            <Link :href="route('admin.requests.index')" class="text-[10px] font-black text-pail-gold uppercase tracking-widest hover:bg-yellow-50 px-3 py-1.5 rounded-lg transition-all">
                                Lihat Semua &rarr;
                            </Link>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-[3rem] shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                            <div v-if="recent_requests.length === 0" class="p-20 text-center text-gray-400">
                                <p class="text-sm italic font-medium">Kotak masuk kosong. Tidak ada pengajuan baru.</p>
                            </div>
                            <div v-else class="divide-y divide-gray-50 dark:divide-gray-800">
                                <div v-for="req in recent_requests" :key="req.id" class="p-8 hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-all flex items-center justify-between group">
                                    <div class="flex items-center gap-6">
                                        <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-700/50 flex flex-col items-center justify-center border border-gray-200 dark:border-gray-700 shadow-sm">
                                            <span class="text-[10px] font-black text-gray-400 uppercase leading-none">{{ formatDateDay(req.created_at).split(' ')[1] }}</span>
                                            <span class="text-xl font-black text-gray-700 dark:text-gray-300 leading-tight">{{ formatDateDay(req.created_at).split(' ')[0] }}</span>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 dark:text-white text-base group-hover:text-pail-gold transition-colors">{{ req.title }}</h4>
                                            <div class="flex items-center gap-3 mt-1">
                                                <span class="text-[10px] font-black uppercase text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded shadow-inner">{{ req.type }}</span>
                                                <span class="text-xs font-bold text-gray-500 uppercase tracking-tighter">{{ req.institution?.name || 'Admin' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-6">
                                        <div class="text-right hidden sm:block">
                                            <div class="text-sm font-black text-gray-900 dark:text-white">{{ formatRupiah(req.estimated_cost) }}</div>
                                            <div class="text-[10px] font-bold text-gray-400 uppercase">Estimasi</div>
                                        </div>
                                        <Link :href="route('admin.requests.edit', req.id)" class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-gray-400 group-hover:bg-pail-gold group-hover:text-white transition-all shadow-inner">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Side Activity Timeline -->
                    <div class="space-y-6">
                        <div class="flex items-center justify-between px-2">
                            <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">Log Aktivitas</h3>
                            <Link :href="route('admin.activity_log.index')" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-colors">
                                Reset Detail
                            </Link>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-[3rem] shadow-xl border border-gray-100 dark:border-gray-700 p-8">
                            <div class="space-y-8 relative">
                                <div class="absolute left-[11px] top-2 bottom-2 w-0.5 bg-gray-100 dark:bg-gray-700/50"></div>
                                
                                <div v-for="log in recent_activity" :key="log.id" class="flex gap-6 relative z-10">
                                    <div class="w-6 h-6 rounded-full bg-white dark:bg-gray-800 border-4 border-gray-100 dark:border-gray-700 flex items-center justify-center shrink-0 shadow-sm">
                                        <div class="w-1.5 h-1.5 rounded-full" :class="log.description.includes('deleted') ? 'bg-red-500' : 'bg-green-500'"></div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800 dark:text-gray-200 leading-tight">
                                            <span class="text-pail-gold">{{ log.causer?.name || 'System' }}</span>
                                            <span class="mx-1 text-gray-400 font-normal">berhasil</span>
                                            {{ log.description }}
                                        </p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ formatTime(log.created_at) }}</span>
                                            <span class="text-[10px] text-gray-300">•</span>
                                            <span class="text-[10px] font-bold text-gray-400 uppercase italic">{{ log.log_name }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="recent_activity.length === 0" class="py-10 text-center text-gray-400 italic text-sm">
                                    Tidak ada aktivitas terekam.
                                </div>
                            </div>
                            
                            <Link :href="route('admin.activity_log.index')" class="mt-10 block w-full py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 rounded-2xl text-center text-xs font-black text-gray-400 uppercase tracking-widest hover:bg-gray-100 transition-all">
                                Lihat Seluruh Log Sistem
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
