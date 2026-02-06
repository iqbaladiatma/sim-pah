<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { formatRupiah } from "@/Utils/format";
import PackageIcon from "@/Components/Icons/PackageIcon.vue";

defineProps({
    stats: Object,
    last_requests: Array,
});
</script>

<template>
    <Head title="Dashboard Karyawan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-200">
                Dashboard Unit
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl">
                <!-- Welcome Banner -->
                <div class="mb-8 p-8 bg-gradient-to-r from-pail-gold to-yellow-600 rounded-2xl shadow-lg text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <h1 class="text-2xl font-bold mb-2">Selamat Datang, {{ $page.props.auth.user.name }}!</h1>
                        <p class="opacity-95 max-w-xl text-sm leading-relaxed">
                            Kelola inventaris dan pengajuan operasional unit Anda dengan cepat dan transparan melalui sistem SIM PAH.
                        </p>
                    </div>
                    <!-- Decorative element -->
                    <div class="absolute -right-16 -bottom-16 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <Link :href="route('items.index')" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-200 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-pail-gold/10 rounded-xl group-hover:bg-pail-gold/20 transition">
                                <PackageIcon className="w-6 h-6 text-pail-gold" />
                            </div>
                            <span class="text-3xl font-bold text-gray-800 dark:text-white">{{ stats.my_items }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Item Barang Unit</h3>
                        <p class="text-sm text-gray-500 mt-1">Total inventaris aktif di unit Anda.</p>
                    </Link>

                    <Link :href="route('requests.index')" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-200 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-xl group-hover:bg-red-100 dark:group-hover:bg-red-900/30 transition">
                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <span class="text-3xl font-bold text-red-500">{{ stats.my_pending_requests }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pengajuan Menunggu</h3>
                        <p class="text-sm text-gray-500 mt-1">Permintaan biaya/stok yang belum disetujui URT.</p>
                    </Link>
                </div>

                <!-- Recent Requests -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Aktivitas Pengajuan Terakhir</h3>
                        <Link :href="route('requests.index')" class="text-xs font-semibold text-pail-gold hover:underline">Lihat Semua</Link>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 dark:bg-gray-900/50 text-[10px] font-extrabold uppercase text-gray-500">
                                <tr>
                                    <th class="px-6 py-4">Judul</th>
                                    <th class="px-6 py-4">Tipe</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-right">Biaya</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="req in last_requests" :key="req.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ req.title }}</td>
                                    <td class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">{{ req.type }}</td>
                                    <td class="px-6 py-4">
                                        <span :class="{
                                            'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400': req.status === 'pending',
                                            'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400': req.status === 'approved',
                                            'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400': req.status === 'rejected'
                                        }" class="px-2.5 py-1 rounded-full text-[10px] font-semibold uppercase">
                                            Menunggu
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-right font-mono font-semibold text-gray-700 dark:text-gray-300">
                                        {{ formatRupiah(req.estimated_cost) }}
                                    </td>
                                </tr>
                                <tr v-if="last_requests.length === 0">
                                    <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500 italic">Belum ada pengajuan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
