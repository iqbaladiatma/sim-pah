<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { formatRupiah } from "@/Utils/format";
import PlusIcon from "@/Components/Icons/PlusIcon.vue";
import InboxIcon from "@/Components/Icons/InboxIcon.vue";
import ZapIcon from "@/Components/Icons/ZapIcon.vue";

const props = defineProps({
    requests: Object,
    stats: Object,
});

const getStatusColor = (status) => {
    switch (status) {
        case "pending":
            return "bg-yellow-100 text-yellow-800";
        case "approved":
            return "bg-green-100 text-green-800";
        case "rejected":
            return "bg-red-100 text-red-800";
        default:
            return "bg-gray-100 text-gray-800";
    }
};
</script>

<template>
    <Head title="Manajemen Pengajuan (Admin)" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-4 sm:px-0">
                <div class="flex items-center gap-4">
                    <div class="w-1.5 h-8 md:w-2 md:h-10 bg-pail-gold rounded-full shrink-0"></div>
                    <div>
                        <h2 class="text-xl md:text-2xl font-black text-gray-800 dark:text-gray-200 uppercase tracking-tighter leading-tight">
                            Manajemen Pengajuan
                        </h2>
                        <p class="text-[8px] md:text-[10px] font-black text-pail-gold uppercase tracking-widest mt-1">Sistem Informasi Manajemen Unit Rumah Tangga</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
                     <span class="hidden lg:flex px-5 py-2.5 rounded-xl bg-gray-900 text-white text-[10px] font-black uppercase tracking-widest border border-black shadow-lg shadow-black/20">
                        Total Audit: {{ requests.total }}
                    </span>
                    <a 
                        :href="route('admin.requests.export')" 
                        class="px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all shadow-lg shadow-green-600/20 font-black text-[10px] sm:text-xs uppercase tracking-widest flex items-center justify-center gap-2 group"
                    >
                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export Excel
                    </a>
                    <Link 
                        :href="route('admin.requests.create')" 
                        class="px-6 py-3 bg-pail-gold text-white rounded-xl hover:bg-yellow-600 transition-all shadow-lg shadow-pail-gold/20 font-black text-[10px] sm:text-xs uppercase tracking-widest flex items-center justify-center gap-2"
                    >
                        <PlusIcon class="w-4 h-4" />
                        Buat Baru
                    </Link>
                </div>
            </div>
        </template>

        <div class="pt-6 pb-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
                
                <!-- Stats Carousel Style Overview -->
                <div class="grid grid-cols-2 gap-3 sm:gap-6">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                        <div class="relative z-10">
                            <h3 class="text-gray-400 font-black uppercase tracking-widest text-[8px] sm:text-[9px] mb-1">Menunggu Review</h3>
                            <div class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">{{ stats.pending }}</div>
                            <div class="text-[8px] sm:text-[9px] font-black text-yellow-600 uppercase tracking-widest mt-1.5">Butuh Persetujuan</div>
                        </div>
                        <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-yellow-50 dark:bg-yellow-900/10 rounded-full group-hover:scale-110 transition-transform duration-700"></div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                        <div class="relative z-10">
                            <h3 class="text-gray-400 font-black uppercase tracking-widest text-[8px] sm:text-[9px] mb-1">Anggaran Pending</h3>
                            <div class="text-xl font-black text-gray-900 dark:text-white font-mono tracking-tighter">{{ formatRupiah(stats.total_cost_pending) }}</div>
                            <div class="text-[8px] sm:text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1.5 italic">Estimasi Serapan</div>
                        </div>
                        <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-gray-50 dark:bg-gray-900/20 rounded-full group-hover:scale-110 transition-transform duration-700"></div>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-3xl shadow-xl shadow-green-500/10 relative overflow-hidden group">
                        <div class="relative z-10">
                            <h3 class="text-white/70 font-black uppercase tracking-widest text-[8px] sm:text-[9px] mb-1">Realisasi Anggaran</h3>
                            <div class="text-xl font-black text-white font-mono tracking-tighter">{{ formatRupiah(stats.total_cost_approved) }}</div>
                            <div class="text-[8px] sm:text-[9px] font-black text-white uppercase tracking-widest mt-1.5">{{ stats.approved }} Pengajuan Disetujui</div>
                        </div>
                        <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-white/10 rounded-full group-hover:scale-110 transition-transform duration-700"></div>
                    </div>

                    <div class="bg-gradient-to-br from-gray-900 to-black p-6 rounded-3xl shadow-xl relative overflow-hidden group">
                        <div class="relative z-10">
                            <h3 class="text-gray-500 font-black uppercase tracking-widest text-[8px] sm:text-[9px] mb-1">Efisiensi Biaya</h3>
                            <div class="text-xl font-black text-white tracking-tighter">{{ stats.rejected }} DITOLAK</div>
                            <div class="text-[8px] sm:text-[9px] font-black text-pail-gold uppercase tracking-widest mt-1.5">Optimalisasi Aset</div>
                        </div>
                        <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-white/5 rounded-full group-hover:scale-110 transition-transform duration-700"></div>
                    </div>
                </div>

                <!-- Main Section: Header & Filters -->
                <div class="flex items-center justify-between px-4">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tighter">Daftar Pengajuan Masuk</h3>
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 flex items-center justify-center">
                            <ZapIcon className="w-5 h-5 text-pail-gold" />
                        </div>
                    </div>
                </div>

                <!-- Desktop Table -->
                <div class="hidden md:block bg-white dark:bg-gray-800 shadow-2xl rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <table class="min-w-full">
                        <thead>
                            <tr class="text-[10px] font-black uppercase text-gray-400 tracking-[0.2em] border-b border-gray-50 dark:border-gray-700">
                                <th class="px-6 py-6 text-left">Unit Lembaga</th>
                                <th class="px-6 py-6 text-left">Informasi Pengajuan</th>
                                <th class="px-6 py-6 text-left">Estimasi Biaya</th>
                                <th class="px-6 py-6 text-left">Status Progres</th>
                                <th class="px-6 py-6 text-right">Opsi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            <tr v-for="(req, index) in requests.data" :key="req.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-all group">
                                <td class="px-6 py-6 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center font-black text-gray-400 group-hover:bg-pail-gold group-hover:text-white transition-all shadow-inner border border-gray-100/50 dark:border-gray-700 text-xs">
                                            {{ req.institution?.code?.substring(0,2) || '??' }}
                                        </div>
                                        <div>
                                            <span class="px-2 py-0.5 rounded-lg text-[8px] font-black uppercase tracking-widest bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 border border-gray-200/50 block w-fit mb-0.5">
                                                {{ req.institution?.code || '-' }}
                                            </span>
                                            <p class="text-[9px] font-bold text-gray-400 truncate max-w-[100px]">{{ req.institution?.name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[8px] uppercase font-black text-pail-gold tracking-[0.2em]">{{ req.type }}</span>
                                            <span v-if="req.photo_evidence" class="w-1 h-1 rounded-full bg-blue-500"></span>
                                        </div>
                                        <span class="font-black text-gray-900 dark:text-white text-sm leading-tight mb-1 tracking-tight group-hover:text-pail-gold transition-colors">{{ req.title }}</span>
                                        <p class="text-[9px] text-gray-400 font-medium leading-relaxed line-clamp-1 italic max-w-xs">{{ req.description }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-6 whitespace-nowrap">
                                    <div class="space-y-0.5">
                                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Estimasi</p>
                                        <span class="font-mono font-black text-gray-900 dark:text-white tracking-tighter text-base leading-none block">
                                            {{ formatRupiah(req.estimated_cost) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-6 whitespace-nowrap text-center">
                                    <span class="px-4 py-1.5 rounded-xl text-[8px] font-black uppercase tracking-widest shadow-sm border inline-block min-w-[90px]"
                                        :class="req.status === 'pending' ? 'bg-yellow-50 text-yellow-700 border-yellow-100 dark:bg-yellow-900/10 dark:text-yellow-500' : 
                                                req.status === 'approved' ? 'bg-green-50 text-green-700 border-green-100 dark:bg-green-900/10 dark:text-green-500' : 
                                                'bg-red-50 text-red-700 border-red-100 dark:bg-red-900/10 dark:text-red-500'">
                                        Menunggu
                                    </span>
                                </td>
                                <td class="px-6 py-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a v-if="req.photo_evidence" :href="`/storage/${req.photo_evidence}`" target="_blank" class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-900 text-gray-400 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm border border-gray-100 dark:border-gray-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </a>
                                        <Link 
                                            :href="route('admin.requests.edit', req.id)"
                                            class="h-10 px-5 bg-gray-900 text-white rounded-xl hover:bg-pail-gold transition-all shadow-xl shadow-black/10 font-black text-[9px] uppercase tracking-[0.2em] flex items-center"
                                        >
                                            {{ req.status === 'pending' ? 'Proses' : 'Detail' }}
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="requests.data.length === 0">
                                <td colspan="5" class="px-8 py-40 text-center">
                                    <div class="max-w-xs mx-auto flex flex-col items-center">
                                        <InboxIcon className="w-16 h-16 mb-4 text-gray-300 opacity-30" />
                                        <p class="text-gray-400 font-black uppercase tracking-widest text-xs italic leading-loose">Antrian pengajuan kosong. Seluruh permohonan telah selesai ditinjau.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card Layout -->
                <div class="md:hidden space-y-6">
                    <div v-for="req in requests.data" :key="req.id" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden relative">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-2.5 py-1 rounded-lg text-[8px] font-black uppercase tracking-[0.2em] bg-gray-50 dark:bg-gray-900 text-gray-500 border border-gray-100 dark:border-gray-700 shadow-inner">
                                    {{ req.institution?.code || '-' }}
                                </span>
                                <span class="px-2.5 py-1 rounded-lg text-[8px] font-black uppercase tracking-[0.2em] border shadow-sm"
                                    :class="req.status === 'pending' ? 'bg-yellow-50 text-yellow-700 border-yellow-100' : 
                                            req.status === 'approved' ? 'bg-green-50 text-green-700 border-green-100' : 
                                            'bg-red-50 text-red-700 border-red-100'">
                                    Menunggu
                                </span>
                            </div>

                            <div class="mb-6">
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span class="text-[8px] uppercase font-black text-pail-gold tracking-[0.3em] block">{{ req.type }}</span>
                                    <span v-if="req.photo_evidence" class="text-[8px] font-black text-blue-500 uppercase tracking-widest">📸 GAMBAR</span>
                                </div>
                                <h3 class="font-black text-gray-900 dark:text-white text-lg leading-tight mb-2 tracking-tighter truncate">{{ req.title }}</h3>
                                <p class="text-[10px] text-gray-500 font-medium line-clamp-2 leading-relaxed italic mb-4 border-l-2 border-gray-100 dark:border-gray-700 pl-3">{{ req.description }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl mb-6 border border-gray-100 dark:border-gray-700/50">
                                <div class="flex justify-between items-center">
                                    <span class="text-[8px] uppercase font-black text-gray-400 tracking-widest">Serapan Biaya</span>
                                    <span class="font-mono font-black text-gray-900 dark:text-white text-base">
                                        {{ formatRupiah(req.estimated_cost) }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <a 
                                    v-if="req.photo_evidence" 
                                    :href="`/storage/${req.photo_evidence}`" 
                                    target="_blank"
                                    class="w-12 h-12 bg-gray-100 dark:bg-gray-900 text-gray-400 rounded-xl flex items-center justify-center border border-gray-200 dark:border-gray-800 transition shadow-sm"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </a>
                                <Link 
                                    :href="route('admin.requests.edit', req.id)"
                                    class="flex-1 py-3.5 bg-gray-900 text-white rounded-xl text-center font-black text-[9px] uppercase tracking-[0.2em] shadow-xl shadow-black/20 group hover:bg-pail-gold transition-all flex items-center justify-center gap-2"
                                >
                                    {{ req.status === 'pending' ? 'Tinjau' : 'Detail' }}
                                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
