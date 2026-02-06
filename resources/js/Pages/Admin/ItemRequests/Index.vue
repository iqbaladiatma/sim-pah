<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

defineProps({
    requests: Object,
    stats: Object,
});

const form = useForm({});

const approve = (id) => {
    if (confirm("Setujui update stok ini?")) {
        form.post(route("admin.item_requests.approve", id));
    }
};

const reject = (id) => {
    if (confirm("Tolak request ini?")) {
        form.post(route("admin.item_requests.reject", id));
    }
};
</script>

<template>
    <Head title="Validasi Stok Barang" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Validasi Update Stok
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                
                <!-- Stats Overview -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-yellow-50 dark:bg-yellow-900/10 rounded-2xl p-5 border border-yellow-100 dark:border-yellow-900/30">
                        <h3 class="text-yellow-600 dark:text-yellow-500 font-black uppercase tracking-wider text-[10px] mb-1">Pending</h3>
                        <div class="text-2xl font-black text-yellow-700 dark:text-yellow-400">{{ stats.pending }}</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/10 rounded-2xl p-5 border border-green-100 dark:border-green-900/30">
                        <h3 class="text-green-600 dark:text-green-500 font-black uppercase tracking-wider text-[10px] mb-1">Disetujui</h3>
                        <div class="text-2xl font-black text-green-700 dark:text-green-400">{{ stats.approved }}</div>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/10 rounded-2xl p-5 border border-red-100 dark:border-red-900/30">
                        <h3 class="text-red-600 dark:text-red-500 font-black uppercase tracking-wider text-[10px] mb-1">Ditolak</h3>
                        <div class="text-2xl font-black text-red-700 dark:text-red-400">{{ stats.rejected }}</div>
                    </div>
                     <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700">
                        <h3 class="text-gray-400 font-black uppercase tracking-wider text-[10px] mb-1">Total Request</h3>
                        <div class="text-2xl font-black text-gray-900 dark:text-white">{{ stats.total }}</div>
                    </div>
                </div>

                <!-- Page Header -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Validasi Update Stok</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Review dan approve permintaan update stok dari karyawan</p>
                </div>

                <!-- Desktop Table View (hidden on mobile) -->
                <div v-if="requests.data.length > 0" class="hidden md:block bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead>
                                <tr class="text-[10px] font-extrabold uppercase text-gray-500 tracking-wider bg-gray-50/80 dark:bg-gray-900/50">
                                    <th class="px-6 py-3 text-left">No</th>
                                    <th class="px-6 py-3 text-left">Lembaga</th>
                                    <th class="px-6 py-3 text-left">Barang</th>
                                    <th class="px-6 py-3 text-left">Perubahan</th>
                                    <th class="px-6 py-3 text-left">Alasan</th>
                                    <th class="px-6 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                <tr v-for="(req, index) in requests.data" :key="req.id" class="hover:bg-gray-50/80 dark:hover:bg-gray-900/30 transition">
                                    <td class="px-6 py-4 font-bold text-gray-500">
                                        {{ (requests.current_page - 1) * requests.per_page + index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="font-bold text-pail-gold">{{ req.user.institution.code }}</span>
                                        <span class="text-gray-500">- {{ req.user.institution.name }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900 dark:text-white">
                                        {{ req.item.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-400 line-through text-sm">{{ req.old_data.stock }}</span>
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                            </svg>
                                            <span class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ req.new_data.stock }}</span>
                                            <span class="text-xs text-gray-400">{{ req.item.unit }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        {{ req.reason }}
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <button @click="approve(req.id)" class="text-green-600 hover:text-green-900 dark:hover:text-green-400 mr-4 font-semibold underline">
                                            Terima
                                        </button>
                                        <button @click="reject(req.id)" class="text-red-600 hover:text-red-900 dark:hover:text-red-400 font-semibold underline">
                                            Tolak
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View (visible only on mobile) -->
                <div v-if="requests.data.length > 0" class="md:hidden space-y-4">
                    <div v-for="req in requests.data" :key="req.id" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="p-5">
                            <!-- Header -->
                            <div class="mb-4">
                                <span class="px-2 py-0.5 bg-pail-gold/10 text-pail-gold font-bold text-xs rounded inline-block mb-2">{{ req.user.institution.code }}</span>
                                <h3 class="font-bold text-gray-900 dark:text-white text-base">{{ req.item.name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ req.user.institution.name }}</p>
                            </div>

                            <!-- Stock Change -->
                            <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-2">Perubahan Stok</span>
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl font-bold text-gray-400 line-through">{{ req.old_data.stock }}</span>
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                    <span class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ req.new_data.stock }}</span>
                                    <span class="text-sm text-gray-500">{{ req.item.unit }}</span>
                                </div>
                            </div>

                            <!-- Reason -->
                            <div class="mb-4">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-1">Alasan</span>
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ req.reason }}</p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <button @click="approve(req.id)" class="flex-1 py-2.5 bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400 rounded-xl hover:bg-green-100 dark:hover:bg-green-900/30 transition-all duration-200 font-semibold text-sm">
                                    ✓ Terima
                                </button>
                                <button @click="reject(req.id)" class="flex-1 py-2.5 bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400 rounded-xl hover:bg-red-100 dark:hover:bg-red-900/30 transition-all duration-200 font-semibold text-sm">
                                    ✗ Tolak
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="requests.data.length === 0" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-16 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-400 italic font-medium">Tidak ada permintaan update stok pending</p>
                    <p class="text-sm text-gray-400 mt-1">Semua request sudah diproses</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
