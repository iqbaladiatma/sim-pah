<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

defineProps({
    requests: Object,
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
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div
                            v-if="requests.data.length === 0"
                            class="text-center py-8 text-gray-500"
                        >
                            Tidak ada permintaan update stok pending.
                        </div>

                        <table
                            v-else
                            class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <thead>
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Lembaga
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Barang
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Perubahan
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Alasan
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-gray-200 dark:divide-gray-700"
                            >
                                <tr v-for="req in requests.data" :key="req.id">
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        {{ req.user.institution.code }} -
                                        {{ req.user.institution.name }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap font-bold"
                                    >
                                        {{ req.item.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span
                                                class="text-gray-500 line-through mr-2"
                                                >{{ req.old_data.stock }}</span
                                            >
                                            <span
                                                class="text-lg font-bold text-blue-600"
                                                >{{ req.new_data.stock }}</span
                                            >
                                            <span
                                                class="ml-1 text-xs text-gray-400"
                                                >{{ req.item.unit }}</span
                                            >
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        {{ req.reason }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button
                                            @click="approve(req.id)"
                                            class="text-green-600 hover:text-green-900 mr-4 font-bold"
                                        >
                                            Terima
                                        </button>
                                        <button
                                            @click="reject(req.id)"
                                            class="text-red-600 hover:text-red-900 font-bold"
                                        >
                                            Tolak
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div
                            class="mt-4"
                            v-if="requests.links && requests.links.length > 3"
                        >
                            <!-- Implement Pagination here if needed -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
