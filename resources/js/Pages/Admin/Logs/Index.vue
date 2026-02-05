<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

defineProps({
    logs: Object,
});
</script>

<template>
    <Head title="Audit Trail" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Audit Trail (Aktivitas Sistem)
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detail</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 font-mono text-xs">
                                <tr v-for="log in logs.data" :key="log.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ new Date(log.created_at).toLocaleString() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ log.causer?.name || 'System' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="{
                                            'text-green-600': log.event === 'created',
                                            'text-blue-600': log.event === 'updated',
                                            'text-red-600': log.event === 'deleted'
                                        }" class="font-bold uppercase">
                                            {{ log.event }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ log.description }}
                                        <div v-if="log.properties" class="mt-1 text-[10px] text-gray-400 max-w-xs truncate">
                                            {{ JSON.stringify(log.properties) }}
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
