<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { formatRupiah } from "@/Utils/format";

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
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Manajemen Pengajuan (Utilitas, B7, Darurat)
            </h2>
        </template>

        <div class="py-12">

            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                
                 <!-- Stats Overview -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-yellow-50 dark:bg-yellow-900/10 rounded-2xl p-6 border border-yellow-100 dark:border-yellow-900/30">
                        <h3 class="text-yellow-600 dark:text-yellow-500 font-black uppercase tracking-wider text-[10px] mb-1">Pending</h3>
                        <div class="text-2xl font-black text-yellow-700 dark:text-yellow-400">{{ stats.pending }}</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/10 rounded-2xl p-6 border border-green-100 dark:border-green-900/30">
                        <h3 class="text-green-600 dark:text-green-500 font-black uppercase tracking-wider text-[10px] mb-1">Disetujui</h3>
                        <div class="text-2xl font-black text-green-700 dark:text-green-400">{{ stats.approved }}</div>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/10 rounded-2xl p-6 border border-red-100 dark:border-red-900/30">
                        <h3 class="text-red-600 dark:text-red-500 font-black uppercase tracking-wider text-[10px] mb-1">Ditolak</h3>
                        <div class="text-2xl font-black text-red-700 dark:text-red-400">{{ stats.rejected }}</div>
                    </div>
                     <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
                        <h3 class="text-gray-400 font-black uppercase tracking-wider text-[10px] mb-1">Est. Biaya Pending</h3>
                        <div class="text-lg font-black text-gray-900 dark:text-white font-mono">{{ formatRupiah(stats.total_cost_pending) }}</div>
                    </div>
                </div>

                <!-- Add Button Container -->
                <div class="flex justify-between items-center bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Manajemen Pengajuan</h3>
                        <p class="text-sm text-gray-500">Utilitas, B7, Darurat</p>
                    </div>
                    <Link 
                        :href="route('admin.requests.create')" 
                        class="px-5 py-2.5 bg-pail-gold text-white rounded-xl hover:bg-yellow-600 transition-all duration-200 shadow-md hover:shadow-lg font-semibold text-sm flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Buat Pengajuan Baru
                    </Link>
                </div>

                <!-- Desktop Table View (hidden on mobile) -->
                <div class="hidden md:block bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead>
                                <tr class="text-[10px] font-extrabold uppercase text-gray-500 tracking-wider bg-gray-50/80 dark:bg-gray-900/50">
                                    <th class="px-6 py-4 text-left">No</th>
                                    <th class="px-6 py-4 text-left">Lembaga</th>
                                    <th class="px-6 py-4 text-left">Detail Pengajuan</th>
                                    <th class="px-6 py-4 text-left">Biaya</th>
                                    <th class="px-6 py-4 text-left">Status</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                <tr v-for="(req, index) in requests.data" :key="req.id" class="hover:bg-gray-50/80 dark:hover:bg-gray-900/30 transition text-sm">
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-500">
                                        {{ (requests.current_page - 1) * requests.per_page + index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 rounded text-[10px] font-black uppercase tracking-wider bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                            {{ req.user.institution?.code || 'ADMIN' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-[10px] uppercase font-bold text-gray-400 tracking-widest">{{ req.type }}</span>
                                            <span class="font-bold text-gray-900 dark:text-white line-clamp-1">{{ req.title }}</span>
                                            <p class="text-xs text-gray-500 line-clamp-1">{{ req.description }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-mono font-bold text-gray-700 dark:text-gray-300">
                                            {{ formatRupiah(req.estimated_cost) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider shadow-sm"
                                            :class="getStatusColor(req.status)">
                                            {{ req.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-3">
                                            <a v-if="req.photo_evidence" :href="`/storage/${req.photo_evidence}`" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs font-bold underline">
                                                Foto
                                            </a>
                                            <Link 
                                                v-if="req.status === 'pending'"
                                                :href="route('admin.requests.edit', req.id)"
                                                class="px-3 py-1.5 bg-green-500 text-white rounded-lg hover:bg-green-600 transition shadow-sm font-bold text-xs"
                                            >
                                                Proses
                                            </Link>
                                            <Link 
                                                v-else
                                                :href="route('admin.requests.edit', req.id)"
                                                class="text-gray-400 hover:text-gray-600 text-xs font-bold underline"
                                            >
                                                Detail
                                            </Link>
                                        </div>
                                        <div v-if="req.admin_note" class="mt-2 text-[10px] text-gray-500 italic max-w-[150px] ml-auto truncate">
                                            Note: {{ req.admin_note }}
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="requests.data.length === 0">
                                    <td colspan="5" class="px-6 py-20 text-center text-gray-400 italic font-medium">Belum ada pengajuan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View (visible only on mobile) -->
                <div class="md:hidden space-y-4">
                    <div v-for="req in requests.data" :key="req.id" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="p-5">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-2 py-1 rounded text-[10px] font-black uppercase tracking-wider bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    {{ req.user.institution?.code || 'ADMIN' }}
                                </span>
                                <span class="px-2 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider shadow-sm"
                                    :class="getStatusColor(req.status)">
                                    {{ req.status }}
                                </span>
                            </div>

                            <div class="mb-4">
                                <span class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-1 block">{{ req.type }}</span>
                                <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-1">{{ req.title }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ req.description }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl mb-4 border border-gray-100 dark:border-gray-700/50">
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] uppercase font-black text-gray-400">Estimasi Biaya</span>
                                    <span class="font-mono font-black text-gray-800 dark:text-gray-200 text-base">
                                        {{ formatRupiah(req.estimated_cost) }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <a 
                                    v-if="req.photo_evidence" 
                                    :href="`/storage/${req.photo_evidence}`" 
                                    target="_blank"
                                    class="flex-1 py-2.5 bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-900/30 transition text-center font-bold text-sm"
                                >
                                    Lihat Bukti Foto
                                </a>
                                <Link 
                                    :href="route('admin.requests.edit', req.id)"
                                    class="flex-1 py-2.5 bg-green-500 text-white rounded-xl hover:bg-green-600 transition text-center font-bold text-sm shadow-md shadow-green-500/20"
                                >
                                    {{ req.status === 'pending' ? 'Proses Pengajuan' : 'Lihat Detail' }}
                                </Link>
                            </div>
                            
                            <div v-if="req.admin_note" class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-100 dark:border-yellow-900/30 rounded-xl">
                                <span class="text-[10px] uppercase font-black text-yellow-600 dark:text-yellow-500 block mb-1">Catatan Admin</span>
                                <p class="text-xs text-yellow-800 dark:text-yellow-200 italic">"{{ req.admin_note }}"</p>
                            </div>
                        </div>
                    </div>
                    
                    <div v-if="requests.data.length === 0" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-16 text-center">
                        <p class="text-gray-400 italic font-medium">Belum ada pengajuan.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
