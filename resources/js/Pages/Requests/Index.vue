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
            <h2 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-200">
                Pengajuan Saya (Lembaga)
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                
                <!-- Action Bar -->
                <div class="flex justify-between items-center bg-white dark:bg-gray-800 p-6 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Daftar Pengajuan</h3>
                        <p class="text-sm text-gray-500">Utilitas, B7, Darurat</p>
                    </div>
                    <Link
                        :href="route('requests.create')"
                        class="px-5 py-2.5 bg-pail-gold text-white rounded-xl hover:bg-yellow-600 transition-all duration-200 shadow-md hover:shadow-lg font-semibold text-sm flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Buat Pengajuan Baru
                    </Link>
                </div>

                <!-- Desktop Table View -->
                <div class="hidden md:block bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-[2.5rem] border border-gray-100 dark:border-gray-700">
                    <div class="p-8">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead>
                                <tr class="text-[10px] font-black uppercase text-gray-400 tracking-wider bg-gray-50/50 dark:bg-gray-900/50 rounded-xl">
                                    <th class="px-6 py-4 text-left first:rounded-l-xl">Tipe</th>
                                    <th class="px-6 py-4 text-left">Judul & Deskripsi</th>
                                    <th class="px-6 py-4 text-left">Est. Biaya</th>
                                    <th class="px-6 py-4 text-left">Status</th>
                                    <th class="px-6 py-4 text-right last:rounded-r-xl">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                <tr v-for="req in requests.data" :key="req.id" class="hover:bg-gray-50/80 dark:hover:bg-gray-900/30 transition text-sm">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-xs font-bold uppercase text-gray-500 tracking-wider bg-gray-100 px-2 py-1 rounded-md">{{ req.type }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 dark:text-white mb-1">{{ req.title }}</div>
                                        <div class="text-xs text-gray-500 line-clamp-1">{{ req.description }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono font-bold text-gray-700 dark:text-gray-300">
                                        {{ formatRupiah(req.estimated_cost) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-black rounded-lg uppercase tracking-wider shadow-sm" :class="getStatusColor(req.status)">
                                            {{ req.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a v-if="req.photo_evidence" :href="`/storage/${req.photo_evidence}`" target="_blank" class="text-blue-600 hover:text-blue-800 font-bold text-xs underline">
                                            Lihat Bukti
                                        </a>
                                        <div v-if="req.admin_note" class="mt-2 text-[10px] text-gray-400 italic">
                                            Note: {{ req.admin_note }}
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="requests.data.length === 0">
                                    <td colspan="5" class="px-6 py-20 text-center text-gray-400 italic font-medium">Belum ada pengajuan. Silakan buat baru.</td>
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
                                    {{ req.status }}
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

                            <a v-if="req.photo_evidence" :href="`/storage/${req.photo_evidence}`" target="_blank" class="block w-full py-3 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition font-bold text-sm text-center">
                                Lihat Bukti Foto
                            </a>
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
