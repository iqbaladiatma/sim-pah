<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: Object,
    last_requests: Array,
});
</script>

<template>
    <Head title="Dashboard Karyawan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Dashboard - {{ $page.props.auth.user.institution?.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-8">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <div class="text-gray-900 dark:text-gray-100 font-bold text-xl">Barang Divisi</div>
                        <div class="text-3xl font-bold text-blue-500">{{ stats.my_items }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-orange-500">
                        <div class="text-gray-900 dark:text-gray-100 font-bold text-xl">Menunggu Approx</div>
                        <div class="text-3xl font-bold text-orange-500">{{ stats.my_pending_requests }}</div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 mb-8">
                    <Link :href="route('dashboard')" class="px-4 py-2 bg-pail-gold text-white rounded hover:bg-yellow-600 transition">
                        + Buat Pengajuan Baru
                    </Link>
                    <Link :href="route('dashboard')" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
                        Update Stok Barang
                    </Link>
                </div>

                <!-- Recent Requests -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-4">Pengajuan Terakhir Saya</h3>
                        <div v-if="last_requests.length === 0" class="text-gray-500 italic">Belum ada pengajuan.</div>
                        <table v-else class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="req in last_requests" :key="req.id">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ req.title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap capitalize">{{ req.type.replace(/_/g, ' ') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800': req.status === 'pending',
                                                'bg-green-100 text-green-800': req.status === 'approved',
                                                'bg-red-100 text-red-800': req.status === 'rejected',
                                            }">
                                            {{ req.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ new Date(req.created_at).toLocaleDateString() }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
