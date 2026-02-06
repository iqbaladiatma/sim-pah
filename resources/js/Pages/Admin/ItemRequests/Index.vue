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
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                        Audit & Validasi Stok
                    </h2>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-1">Gudang Pusat v2.0</p>
                </div>
            </div>
        </template>

        <div class="pt-6 pb-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-12">
                
                <!-- Premium Stats Grid -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-xl transition-all duration-500">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-yellow-500/10 rounded-full blur-2xl group-hover:bg-yellow-500/20 transition-all"></div>
                        <h3 class="text-yellow-600 dark:text-yellow-500 font-black uppercase tracking-[0.2em] text-[10px] mb-4">Pending Audit</h3>
                        <div class="flex items-end gap-2">
                            <span class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter">{{ stats.pending }}</span>
                            <span class="text-[10px] font-black text-gray-400 uppercase mb-2">Request</span>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-xl transition-all duration-500">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-green-500/10 rounded-full blur-2xl group-hover:bg-green-500/20 transition-all"></div>
                        <h3 class="text-green-600 dark:text-green-500 font-black uppercase tracking-[0.2em] text-[10px] mb-4">Disetujui</h3>
                        <div class="flex items-end gap-2">
                            <span class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter">{{ stats.approved }}</span>
                            <span class="text-[10px] font-black text-gray-400 uppercase mb-2">Aksi</span>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-xl transition-all duration-500">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-red-500/10 rounded-full blur-2xl group-hover:bg-red-500/20 transition-all"></div>
                        <h3 class="text-red-600 dark:text-red-500 font-black uppercase tracking-[0.2em] text-[10px] mb-4">Ditolak</h3>
                        <div class="flex items-end gap-2">
                            <span class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter">{{ stats.rejected }}</span>
                            <span class="text-[10px] font-black text-gray-400 uppercase mb-2">Aksi</span>
                        </div>
                    </div>
                    <div class="bg-pail-gold rounded-[2.5rem] p-8 shadow-xl shadow-pail-gold/20 relative overflow-hidden group hover:scale-[1.02] transition-all duration-500">
                        <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"></div>
                        <h3 class="text-white/80 font-black uppercase tracking-[0.2em] text-[10px] mb-4">Total Siklus</h3>
                        <div class="flex items-end gap-2 text-white">
                            <span class="text-4xl font-black tracking-tighter">{{ stats.total }}</span>
                            <span class="text-[10px] font-black uppercase mb-2 opacity-80">Update</span>
                        </div>
                    </div>
                </div>

                <!-- Main Content Canvas -->
                <div class="bg-white dark:bg-gray-800 rounded-[3rem] shadow-2xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-10 border-b border-gray-50 dark:border-gray-700/50 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div>
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tighter uppercase">Daftar Audit Menunggu</h3>
                            <p class="text-sm text-gray-400 font-medium">Validasi perubahan stok inventaris dari setiap lembaga.</p>
                        </div>
                    </div>

                    <div v-if="requests.data.length > 0" class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 transition">
                                    <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Info Lembaga</th>
                                    <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Item & Perubahan</th>
                                    <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Alasan / Note</th>
                                    <th class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                <tr v-for="req in requests.data" :key="req.id" class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/40 transition-all duration-300">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-900 flex items-center justify-center font-black text-gray-400 border border-gray-200/50 group-hover:bg-pail-gold group-hover:text-white group-hover:border-pail-gold transition-all duration-500">
                                                {{ req.user.institution.code.charAt(0) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-black text-gray-900 dark:text-white tracking-tight">{{ req.user.institution.name }}</div>
                                                <div class="text-[10px] font-black text-pail-gold uppercase tracking-widest">{{ req.user.institution.code }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-sm font-black text-gray-900 dark:text-white mb-2 uppercase tracking-tight">{{ req.item.name }}</div>
                                        <div class="flex items-center gap-3">
                                            <span class="px-3 py-1 rounded-lg bg-gray-100 dark:bg-gray-900 text-[10px] font-black text-gray-400 line-through">{{ req.old_data.stock }}</span>
                                            <svg class="w-4 h-4 text-pail-gold animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                            <span class="px-3 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-[11px] font-black text-blue-600 dark:text-blue-400">{{ req.new_data.stock }} Unit</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 leading-relaxed max-w-xs italic line-clamp-2">
                                            "{{ req.reason }}"
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transform translate-x-4 group-hover:translate-x-0 transition-all duration-500">
                                            <button @click="approve(req.id)" class="px-6 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition shadow-lg shadow-green-600/20 font-black text-[10px] uppercase tracking-widest">
                                                Setujui
                                            </button>
                                            <button @click="reject(req.id)" class="px-6 py-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition font-black text-[10px] uppercase tracking-widest">
                                                Tolak
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="py-32 text-center">
                        <div class="inline-flex w-24 h-24 items-center justify-center rounded-[2rem] bg-gray-50 dark:bg-gray-900 text-gray-200 dark:text-gray-800 mb-8 border border-gray-100 dark:border-gray-800 transition-transform transform hover:scale-110 duration-700">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h4 class="text-xl font-black text-gray-900 dark:text-white tracking-tighter uppercase mb-2">Audit Selesai</h4>
                        <p class="text-sm text-gray-400 font-medium">Tidak ada antrian validasi stok. Pekerjaan bagus!</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
