<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { formatRupiah } from "@/Utils/format";
import InboxIcon from "@/Components/Icons/InboxIcon.vue";
import MoonIcon from "@/Components/Icons/MoonIcon.vue";

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
    <Head title="Dashboard Admin" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                    Ikhtisar Dashboard
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
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-12">
                
                <!-- Ultra-Premium Welcome Hub -->
                <div class="relative bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden group">
                    <div class="absolute -right-24 -top-24 w-96 h-96 bg-pail-gold opacity-5 rounded-full blur-[100px] group-hover:opacity-10 transition-opacity duration-1000"></div>
                    <div class="absolute -left-24 -bottom-24 w-96 h-96 bg-pail-gold opacity-5 rounded-full blur-[100px]"></div>
                    
                    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-10">
                        <div class="flex-1">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-[2px] bg-pail-gold"></div>
                                <span class="text-[10px] font-black text-pail-gold uppercase tracking-[0.4em]">Markas Pusat Administrasi</span>
                            </div>
                            <h1 class="text-3xl font-black text-gray-900 dark:text-white mb-3 tracking-tighter uppercase leading-none">{{ greeting }}, {{ user.name.split(' ')[0] }}!</h1>
                            <p class="text-gray-400 max-w-2xl leading-relaxed font-bold text-sm uppercase tracking-tight">
                                Sistem Manajemen Terintegrasi untuk <span class="text-pail-gold">Yayasan Abu Hurairah</span>. Anda memiliki <span class="text-gray-900 dark:text-white underline decoration-pail-gold decoration-4 underline-offset-4">{{ stats.pending_requests }} pengajuan aktif</span> yang menunggu protokol verifikasi.
                            </p>
                        </div>
                        <div class="flex items-center gap-6">
                            <Link :href="route('admin.requests.index')" class="px-6 py-4 bg-gray-900 text-pail-gold rounded-[1.5rem] font-black text-xs uppercase tracking-[0.2em] hover:bg-black transition-all shadow-2xl shadow-black/20 flex items-center gap-3 group/btn">
                                Pusat Intelijen
                                <svg class="w-5 h-5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Strategic Intelligence Grid -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm relative group overflow-hidden transition-all duration-500 hover:shadow-2xl hover:-translate-y-1">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50/50 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-700 scale-0 group-hover:scale-110"></div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-4 relative z-10">Lembaga</h4>
                        <div class="flex items-end gap-2 relative z-10 leading-none">
                            <div class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">{{ stats.total_institutions }}</div>
                            <span class="text-[9px] font-black text-gray-300 uppercase mb-1">Units</span>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-10 rounded-[3rem] border border-gray-100 dark:border-gray-700 shadow-sm relative group overflow-hidden transition-all duration-500 hover:shadow-2xl hover:-translate-y-1">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-50/50 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-700 scale-0 group-hover:scale-110"></div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-4 relative z-10">SDM & Personil</h4>
                        <div class="flex items-end gap-2 relative z-10 leading-none">
                            <div class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">{{ stats.total_users }}</div>
                            <span class="text-[9px] font-black text-gray-300 uppercase mb-1">Staff</span>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-10 rounded-[3rem] border border-gray-100 dark:border-gray-700 shadow-sm relative group overflow-hidden transition-all duration-500 hover:shadow-2xl hover:-translate-y-1">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-orange-50/50 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-700 scale-0 group-hover:scale-110"></div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-4 relative z-10">Total Inventaris</h4>
                        <div class="flex items-end gap-2 relative z-10 leading-none">
                            <div class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">{{ stats.total_items }}</div>
                            <span class="text-[9px] font-black text-gray-300 uppercase mb-1">SKUs</span>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-red-500 to-red-600 p-6 rounded-[2rem] shadow-2xl shadow-red-500/20 relative overflow-hidden group hover:scale-[1.02] transition-all duration-500">
                        <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <h4 class="text-[10px] font-black text-red-100 uppercase tracking-[0.3em] mb-4 relative z-10 italic">Peringatan Stok Kritis</h4>
                        <div class="text-3xl font-black text-white tracking-tighter relative z-10 leading-none">{{ stats.low_stock_items }}</div>
                        <div class="mt-4 flex items-center gap-2 text-[10px] font-black text-white uppercase relative z-10">
                            <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                            Pesanan Ulang Mendesak
                        </div>
                    </div>
                </div>

                <!-- Financial Architecture -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col justify-between group hover:shadow-xl transition-all">
                        <div>
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-3">Liabilitas Finansial Tertunda</h4>
                            <div class="text-2xl font-black text-gray-900 dark:text-white tracking-tighter group-hover:text-pail-gold transition-colors leading-none mb-2">{{ formatRupiah(stats.total_cost_pending) }}</div>
                            <p class="text-[10px] font-black text-gray-300 uppercase">Estimasi pengeluaran antrian</p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-gray-50 dark:border-gray-700/50">
                            <div class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] mb-2">Pencairan Disetujui</div>
                            <div class="text-xl font-black text-gray-700 dark:text-gray-300 tracking-tighter leading-none">{{ formatRupiah(stats.total_cost_approved) }}</div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-[#D4B876] via-pail-gold to-[#B89648] p-6 rounded-[2rem] shadow-2xl shadow-pail-gold/30 flex flex-col justify-between relative overflow-hidden group hover:-translate-y-1 transition-all duration-500">
                        <div class="absolute top-0 right-0 p-6 opacity-10 group-hover:rotate-12 transition-transform duration-700">
                            <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <div class="relative z-10">
                            <h4 class="text-[10px] font-black text-white/70 uppercase tracking-[0.3em] mb-3">Penyusutan Operasional</h4>
                            <div class="text-4xl font-black text-white tracking-tighter leading-none mb-2">{{ stats.out_of_stock }}</div>
                            <p class="text-xs text-black/40 font-black uppercase tracking-widest italic">Aset Stok Habis</p>
                        </div>
                        <Link :href="route('admin.items.index')" class="relative z-10 mt-10 text-[10px] font-black text-white uppercase tracking-[0.3em] flex items-center gap-4 group/link">
                            Asesmen Inventaris
                            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-md group-hover/link:bg-white group-hover/link:text-pail-gold transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7-7 7"></path></svg>
                            </div>
                        </Link>
                    </div>

                    <div class="bg-gray-900 dark:bg-black p-6 rounded-[2rem] shadow-2xl flex flex-col justify-center border border-white/5 relative group overflow-hidden">
                        <div class="absolute inset-0 bg-pail-gold/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative z-10 flex items-center gap-6">
                            <div class="w-16 h-16 rounded-[1.5rem] bg-pail-gold/10 border border-pail-gold/20 flex items-center justify-center text-pail-gold group-hover:scale-110 transition-transform shadow-2xl shadow-pail-gold/20">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-[10px] font-black text-pail-gold/70 uppercase tracking-[0.3em] mb-2">Koneksi Aktif</h4>
                                <div class="text-2xl font-black text-white tracking-tighter leading-none mb-2">{{ stats.online_users }} Node</div>
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.5)] animate-pulse"></span>
                                    <p class="text-[10px] text-green-500 font-black uppercase tracking-widest">Akses Terotorisasi</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Strategic Operations & Activity -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    
                    <!-- Insightful Requests List -->
                    <div class="lg:col-span-2 space-y-8">
                        <div class="flex items-center justify-between px-6">
                            <div class="flex items-center gap-4">
                                <div class="w-1.5 h-6 bg-pail-gold rounded-full"></div>
                                <h3 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">Proposal Strategis Terbaru</h3>
                            </div>
                            <Link :href="route('admin.requests.index')" class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] hover:text-pail-gold transition-colors border-b-2 border-transparent hover:border-pail-gold pb-1">
                                Eksplorasi Katalog &rarr;
                            </Link>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden relative">
                            <div v-if="recent_requests.length === 0" class="p-40 text-center relative overflow-hidden">
                                <div class="absolute inset-0 bg-gray-50/50 opacity-20 dark:bg-transparent"></div>
                                <div class="relative z-10 flex flex-col items-center text-gray-400">
                                    <InboxIcon className="w-16 h-16 mb-6 opacity-20" />
                                    <p class="font-black uppercase tracking-[0.3em] text-sm">Silo Komunikasi Kosong</p>
                                    <p class="text-[10px] text-gray-300 font-bold mt-2 uppercase">Tidak ada proposal yang tertunda dalam antrian.</p>
                                </div>
                            </div>
                            <div v-else class="p-4 space-y-4">
                                <div v-for="req in recent_requests" :key="req.id" class="p-8 hover:bg-gray-50/80 dark:hover:bg-gray-900/50 rounded-[2.5rem] transition-all flex items-center justify-between group border border-transparent hover:border-gray-100 dark:hover:border-gray-700">
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
                                                <div class="flex items-center gap-2">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-pail-gold/40"></div>
                                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ req.institution?.name || 'Central Administration' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-8">
                                        <div class="text-right hidden sm:block">
                                            <div class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mb-1">Estimasi Biaya</div>
                                            <div class="text-xl font-black text-gray-900 dark:text-white font-mono tracking-tighter">{{ formatRupiah(req.estimated_cost) }}</div>
                                        </div>
                                        <Link :href="route('admin.requests.edit', req.id)" class="w-14 h-14 rounded-2xl bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-gray-400 hover:bg-pail-gold hover:text-white hover:scale-110 transition-all shadow-inner border border-gray-100 dark:border-gray-600 group/btn">
                                            <svg class="w-6 h-6 group-hover/btn:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Side Activity Trace -->
                    <div class="space-y-8">
                        <div class="flex items-center justify-between px-6">
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">Pulse Sistem</h3>
                            <button class="text-[10px] font-black text-gray-300 uppercase tracking-widest hover:text-gray-500 transition-colors italic">Realtime Trace</button>
                        </div>

                        <div class="bg-gray-900 dark:bg-black rounded-[3.5rem] shadow-2xl p-10 relative overflow-hidden group">
                            <div class="absolute inset-0 bg-pail-gold/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            
                            <div class="space-y-10 relative z-10">
                                <div class="absolute left-[15px] top-4 bottom-4 w-[1px] bg-white/10"></div>
                                
                                <div v-for="log in recent_activity" :key="log.id" class="flex gap-6 relative group/item">
                                    <div class="w-8 h-8 rounded-2xl bg-gray-800 border-2 border-white/5 flex items-center justify-center shrink-0 shadow-2xl relative z-10 group-hover/item:border-pail-gold/50 transition-colors">
                                        <div class="w-2 h-2 rounded-full shadow-[0_0_10px_rgba(201,166,88,0.5)]" :class="log.description.includes('deleted') ? 'bg-red-500 shadow-red-500/50' : 'bg-pail-gold'"></div>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-400 leading-relaxed uppercase tracking-tight">
                                            <span class="text-white font-black">{{ log.causer?.name || 'Neural System' }}</span>
                                            <span class="mx-1 lowercase opacity-50">telah menjalankan</span>
                                            <span class="text-pail-gold">{{ log.description }}</span>
                                        </p>
                                        <div class="flex items-center gap-3 mt-2">
                                            <span class="text-[9px] font-black text-white/30 uppercase tracking-[0.2em] font-mono">{{ formatTime(log.created_at) }}</span>
                                            <span class="w-1 h-1 rounded-full bg-white/10"></span>
                                            <span class="text-[9px] font-black text-pail-gold/40 uppercase tracking-widest italic font-mono">{{ log.log_name }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="recent_activity.length === 0" class="py-20 text-center relative flex flex-col items-center">
                                    <MoonIcon className="w-12 h-12 mb-4 text-white opacity-10" />
                                    <p class="text-[10px] font-black text-white/20 uppercase tracking-[0.4em]">Nol latensi terdeteksi</p>
                                </div>
                            </div>
                            
                            <Link :href="route('admin.activity_log.index')" class="mt-12 block w-full py-5 bg-white/5 border border-white/10 rounded-[1.5rem] text-center text-[10px] font-black text-white/40 uppercase tracking-[0.3em] hover:bg-white hover:text-black hover:border-white transition-all transform active:scale-[0.98]">
                                Arsip Aktivitas Global
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
