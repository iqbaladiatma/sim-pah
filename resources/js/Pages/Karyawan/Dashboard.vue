<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { formatRupiah } from "@/Utils/format";
import PackageIcon from "@/Components/Icons/PackageIcon.vue";
import SparklesIcon from "@/Components/Icons/SparklesIcon.vue";

const props = defineProps({
    stats: Object,
    last_requests: Array,
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
    <Head title="Dashboard Unit" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                    Pusat Komando Unit
                </h2>
                <div class="flex items-center gap-2">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-pail-gold opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-pail-gold"></span>
                    </span>
                    <span class="text-xs font-black text-gray-400 uppercase tracking-widest">SISTEM AKTIF</span>
                </div>
            </div>
        </template>

        <div class="pt-6 pb-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-12">
                
                <!-- Ultra-Premium Welcome Hub -->
                <div class="relative bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden group">
                    <div class="absolute -right-24 -top-24 w-96 h-96 bg-pail-gold opacity-5 rounded-full blur-[100px] group-hover:opacity-10 transition-opacity duration-1000"></div>
                    <div class="absolute -left-24 -bottom-24 w-96 h-96 bg-pail-gold opacity-5 rounded-full blur-[100px]"></div>
                    
                    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-10">
                        <div class="flex-1">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-[2px] bg-pail-gold"></div>
                                <span class="text-[10px] font-black text-pail-gold uppercase tracking-[0.4em]">{{ user.institution?.name || 'Unit Operasional' }}</span>
                            </div>
                            <h1 class="text-3xl font-black text-gray-900 dark:text-white mb-3 tracking-tighter uppercase leading-none">{{ greeting }}, {{ user.name.split(' ')[0] }}!</h1>
                            <p class="text-gray-400 max-w-2xl leading-relaxed font-bold text-sm uppercase tracking-tight">
                                Sistem Manajemen Terintegrasi untuk <span class="text-pail-gold">Yayasan Abu Hurairah</span>. Anda memiliki <span class="text-gray-900 dark:text-white underline decoration-pail-gold decoration-4 underline-offset-4">{{ stats.my_pending_requests }} pengajuan aktif</span> yang menunggu protokol verifikasi.
                            </p>
                        </div>
                        <div class="flex items-center gap-6">
                            <Link :href="route('requests.create')" class="px-6 py-4 bg-gray-900 text-pail-gold rounded-[1.5rem] font-black text-xs uppercase tracking-[0.2em] hover:bg-black transition-all shadow-2xl shadow-black/20 flex items-center gap-3 group/btn">
                                Buat Pengajuan
                                <svg class="w-5 h-5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Institution Profile Card -->
                <div class="bg-gradient-to-br from-pail-gold via-[#D4B876] to-[#B89648] rounded-[2.5rem] p-8 shadow-2xl shadow-pail-gold/30 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-[100px]"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-black/10 rounded-full blur-[80px]"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-[1.5rem] bg-white/20 backdrop-blur-md border-2 border-white/30 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-white/70 uppercase tracking-[0.3em] mb-1">Profil Lembaga</p>
                                    <h3 class="text-2xl font-black text-white uppercase tracking-tight leading-none">{{ user.institution?.name || 'Unit Operasional' }}</h3>
                                </div>
                            </div>
                            <span class="px-4 py-2 bg-white/20 backdrop-blur-md rounded-full border border-white/30 text-xs font-black text-white uppercase tracking-wider">
                                {{ user.institution?.code || '-' }}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20">
                                <p class="text-[9px] font-black text-white/60 uppercase tracking-widest mb-2">Pengguna</p>
                                <p class="text-lg font-black text-white">{{ user.name }}</p>
                                <p class="text-xs text-white/70 font-bold mt-1">{{ user.email }}</p>
                            </div>
                            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20">
                                <p class="text-[9px] font-black text-white/60 uppercase tracking-widest mb-2">Role</p>
                                <p class="text-lg font-black text-white uppercase">{{ user.role }}</p>
                            </div>
                            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20">
                                <p class="text-[9px] font-black text-white/60 uppercase tracking-widest mb-2">Status Akun</p>
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                                    <p class="text-lg font-black text-white">AKTIF</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Strategic Intelligence Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <Link :href="route('items.index')" class="bg-white dark:bg-gray-800 p-10 rounded-[3rem] border border-gray-100 dark:border-gray-700 shadow-sm relative group overflow-hidden transition-all duration-500 hover:shadow-2xl hover:-translate-y-1">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50/50 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-700 scale-0 group-hover:scale-110"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-6">
                                <div class="p-4 bg-pail-gold/10 rounded-[1.5rem] group-hover:bg-pail-gold/20 transition">
                                    <PackageIcon className="w-8 h-8 text-pail-gold" />
                                </div>
                            </div>
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-4">Inventaris Unit</h4>
                            <div class="flex items-end gap-2 leading-none mb-3">
                                <div class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter">{{ stats.my_items }}</div>
                                <span class="text-[9px] font-black text-gray-300 uppercase mb-2">Items</span>
                            </div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-tight">Total aset terdaftar</p>
                        </div>
                    </Link>

                    <Link :href="route('requests.index')" class="bg-gradient-to-br from-red-500 to-red-600 p-10 rounded-[3rem] shadow-2xl shadow-red-500/20 relative overflow-hidden group hover:scale-[1.02] transition-all duration-500">
                        <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-6">
                                <div class="p-4 bg-white/20 rounded-[1.5rem] backdrop-blur-md">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            </div>
                            <h4 class="text-[10px] font-black text-red-100 uppercase tracking-[0.3em] mb-4 italic">Menunggu Persetujuan</h4>
                            <div class="text-4xl font-black text-white tracking-tighter leading-none mb-3">{{ stats.my_pending_requests }}</div>
                            <div class="flex items-center gap-2 text-[10px] font-black text-white uppercase">
                                <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                                Pengajuan Aktif
                            </div>
                        </div>
                    </Link>

                    <div class="bg-gradient-to-br from-[#D4B876] via-pail-gold to-[#B89648] p-10 rounded-[3rem] shadow-2xl shadow-pail-gold/30 relative overflow-hidden group hover:-translate-y-1 transition-all duration-500">
                        <div class="absolute top-0 right-0 p-6 opacity-10 group-hover:rotate-12 transition-transform duration-700">
                            <SparklesIcon className="w-20 h-20 text-white" />
                        </div>
                        <div class="relative z-10">
                            <h4 class="text-[10px] font-black text-white/70 uppercase tracking-[0.3em] mb-4">Status Operasional</h4>
                            <div class="text-4xl font-black text-white tracking-tighter leading-none mb-3">{{ stats.my_approved_requests }}</div>
                            <p class="text-xs text-black/40 font-black uppercase tracking-widest italic">Pengajuan Disetujui</p>
                        </div>
                        <Link :href="route('requests.index')" class="relative z-10 mt-10 text-[10px] font-black text-white uppercase tracking-[0.3em] flex items-center gap-4 group/link">
                            Lihat Riwayat
                            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-md group-hover/link:bg-white group-hover/link:text-pail-gold transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7-7 7"></path></svg>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Recent Requests -->
                <div class="space-y-8">
                    <div class="flex items-center justify-between px-6">
                        <div class="flex items-center gap-4">
                            <div class="w-1.5 h-6 bg-pail-gold rounded-full"></div>
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">Aktivitas Pengajuan Terbaru</h3>
                        </div>
                        <Link :href="route('requests.index')" class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] hover:text-pail-gold transition-colors border-b-2 border-transparent hover:border-pail-gold pb-1">
                            Lihat Semua &rarr;
                        </Link>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div v-if="last_requests.length === 0" class="p-40 text-center relative overflow-hidden">
                            <div class="absolute inset-0 bg-gray-50/50 opacity-20 dark:bg-transparent"></div>
                            <div class="relative z-10 flex flex-col items-center text-gray-400">
                                <svg class="w-16 h-16 mb-6 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                <p class="font-black uppercase tracking-[0.3em] text-sm">Belum Ada Pengajuan</p>
                                <p class="text-[10px] text-gray-300 font-bold mt-2 uppercase">Mulai dengan membuat pengajuan baru.</p>
                            </div>
                        </div>
                        <div v-else class="p-4 space-y-4">
                            <div v-for="req in last_requests" :key="req.id" class="p-8 hover:bg-gray-50/80 dark:hover:bg-gray-900/50 rounded-[2.5rem] transition-all flex items-center justify-between group border border-transparent hover:border-gray-100 dark:hover:border-gray-700">
                                <div class="flex items-center gap-8">
                                    <div class="w-16 h-16 rounded-[1.75rem] bg-gray-900 flex flex-col items-center justify-center border-2 border-pail-gold/30 shadow-2xl shadow-black/20 shrink-0">
                                        <span class="text-[9px] font-black text-pail-gold uppercase leading-none mb-1 opacity-70 font-mono">{{ formatDateDay(req.created_at).split(' ')[1] }}</span>
                                        <span class="text-2xl font-black text-white leading-none tracking-tighter font-mono">{{ formatDateDay(req.created_at).split(' ')[0] }}</span>
                                    </div>
                                    <div>
                                        <h4 class="font-black text-gray-900 dark:text-white text-lg tracking-tight uppercase leading-none mb-3 group-hover:text-pail-gold transition-colors">{{ req.title }}</h4>
                                        <div class="flex items-center gap-4">
                                            <div class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-700 px-3 py-1 rounded-full border border-gray-100 dark:border-gray-600 shadow-sm">
                                                <span class="text-[10px] font-black uppercase text-gray-500 dark:text-gray-300 tracking-widest">{{ req.type }}</span>
                                            </div>
                                            <span :class="{
                                                'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400': req.status === 'pending',
                                                'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400': req.status === 'approved',
                                                'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400': req.status === 'rejected'
                                            }" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">
                                                {{ req.status === 'pending' ? 'Menunggu' : req.status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-8">
                                    <div class="text-right hidden sm:block">
                                        <div class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mb-1">Estimasi Biaya</div>
                                        <div class="text-xl font-black text-gray-900 dark:text-white font-mono tracking-tighter">{{ formatRupiah(req.estimated_cost) }}</div>
                                    </div>
                                    <Link :href="route('requests.edit', req.id)" class="w-14 h-14 rounded-2xl bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-gray-400 hover:bg-pail-gold hover:text-white hover:scale-110 transition-all shadow-inner border border-gray-100 dark:border-gray-600 group/btn">
                                        <svg class="w-6 h-6 group-hover/btn:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
