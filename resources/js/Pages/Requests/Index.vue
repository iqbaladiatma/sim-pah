<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { formatRupiah } from "@/Utils/format";

const props = defineProps({
    requests: Object,
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
    <Head title="Pengajuan Saya" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                        Manajemen Pengajuan
                    </h2>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Utilitas, Biaya & Darurat</p>
                </div>
                <div v-if="$page.props.system_settings.allow_new_requests === '1'">
                    <Link
                        :href="route('requests.create')"
                        class="px-6 py-4 bg-gray-900 text-pail-gold rounded-[1.5rem] font-black text-xs uppercase tracking-[0.2em] hover:bg-black transition-all shadow-2xl shadow-black/20 flex items-center gap-3 group"
                    >
                        <svg class="w-5 h-5 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        Buat Pengajuan
                    </Link>
                </div>
                <div v-else class="flex flex-col items-end">
                    <div class="px-6 py-4 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-[1.5rem] font-black text-xs uppercase tracking-[0.2em] flex items-center gap-3 cursor-not-allowed border border-gray-200 dark:border-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                        Fitur Dinonaktifkan
                    </div>
                    <span class="text-[8px] font-black text-red-500 uppercase tracking-widest mt-2 animate-pulse text-right">Ditutup Sementara oleh Admin URT</span>
                </div>
            </div>
        </template>

        <div class="pt-6 pb-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
                
                <!-- Requests Table -->
                <div class="hidden md:block bg-white dark:bg-gray-800 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tgl / ID</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Detail Pengajuan</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tipe</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Estimasi Biaya</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                <tr v-for="req in requests.data" :key="req.id" class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-all">
                                    <td class="px-8 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gray-900 flex flex-col items-center justify-center border border-pail-gold/20 shrink-0">
                                                <span class="text-[8px] font-black text-pail-gold uppercase leading-none mb-0.5 opacity-70">{{ new Date(req.created_at).toLocaleDateString('id-ID', { month: 'short' }) }}</span>
                                                <span class="text-base font-black text-white leading-none tracking-tighter">{{ new Date(req.created_at).getDate() }}</span>
                                            </div>
                                            <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">#{{ req.id.toString().padStart(4, '0') }}</div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-4">
                                        <div>
                                            <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-tight group-hover:text-pail-gold transition-colors">{{ req.title }}</p>
                                            <p class="text-[9px] text-gray-400 truncate max-w-[250px] italic">{{ req.description }}</p>
                                        </div>
                                    </td>
                                    <td class="px-8 py-4">
                                        <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 text-[9px] font-black uppercase tracking-widest rounded-md border border-gray-100 dark:border-gray-600">
                                            {{ req.type }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-4">
                                        <p class="text-[11px] font-black text-gray-900 dark:text-white font-mono tracking-tighter">{{ formatRupiah(req.estimated_cost) }}</p>
                                    </td>
                                    <td class="px-8 py-4 text-center">
                                        <span class="px-3 py-1 text-[8px] font-black rounded-full uppercase tracking-widest shadow-sm inline-block" :class="getStatusColor(req.status)">
                                            {{ req.status === 'pending' ? 'Tinjauan' : req.status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a v-if="req.photo_evidence" :href="`/storage/${req.photo_evidence}`" target="_blank" class="p-2 rounded-lg bg-blue-50 text-blue-400 hover:text-blue-600 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </a>
                                            <Link v-if="req.status === 'pending'" :href="route('requests.edit', req.id)" class="p-2 rounded-lg bg-gray-50 dark:bg-gray-900 text-gray-400 hover:text-pail-gold transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="requests.data.length === 0">
                                    <td colspan="6" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center text-gray-400">
                                            <svg class="w-12 h-12 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <p class="text-[10px] font-black uppercase tracking-[0.2em]">Belum Ada Pengajuan</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Mobile Card View -->
                <div class="md:hidden space-y-4">
                    <div v-for="req in requests.data" :key="req.id" class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest block mb-1">{{ req.type }}</span>
                                    <h3 class="font-bold text-gray-900 dark:text-white text-lg">{{ req.title }}</h3>
                                </div>
                                <span class="px-3 py-1 text-[10px] font-black rounded-lg uppercase tracking-wider shadow-sm" :class="getStatusColor(req.status)">
                                    {{ req.status === 'pending' ? 'Tinjauan' : req.status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                                </span>
                            </div>

                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 leading-relaxed bg-gray-50 p-4 rounded-xl border border-gray-50">{{ req.description }}</p>

                            <div class="flex items-center justify-between py-3 border-t border-gray-100 dark:border-gray-700 mb-4">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Estimasi</span>
                                <span class="font-mono font-black text-gray-800 dark:text-gray-200 text-base">{{ formatRupiah(req.estimated_cost) }}</span>
                            </div>

                            <div v-if="req.admin_note" class="mb-4 p-3 bg-yellow-50 rounded-xl border border-yellow-100">
                                <span class="text-[10px] font-black text-yellow-600 uppercase block mb-1">Admin Note</span>
                                <p class="text-xs text-yellow-800 italic">"{{ req.admin_note }}"</p>
                            </div>

                            <div class="flex gap-2">
                                <a v-if="req.photo_evidence" :href="`/storage/${req.photo_evidence}`" target="_blank" class="flex-1 py-3 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition font-bold text-sm text-center">
                                    Lihat Bukti
                                </a>
                                <Link 
                                    v-if="req.status === 'pending'"
                                    :href="route('requests.edit', req.id)"
                                    class="flex-1 py-3 bg-pail-gold text-white rounded-xl hover:bg-yellow-600 transition font-bold text-sm text-center shadow-lg shadow-pail-gold/20"
                                >
                                    Edit Pengajuan
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div v-if="requests.data.length === 0" class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
                        <p class="text-gray-400 italic font-medium">Belum ada pengajuan.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
