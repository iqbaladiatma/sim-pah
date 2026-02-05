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
                <!-- Desktop Table View (hidden on mobile) -->
                <div class="hidden md:block bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead>
                                <tr class="text-[10px] font-extrabold uppercase text-gray-500 tracking-wider bg-gray-50/80 dark:bg-gray-900/50">
                                    <th class="px-6 py-4 text-left">Waktu</th>
                                    <th class="px-6 py-4 text-left">User</th>
                                    <th class="px-6 py-4 text-left">Aksi</th>
                                    <th class="px-6 py-4 text-left">Detail</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800 font-mono text-xs">
                                <tr v-for="log in logs.data" :key="log.id" class="hover:bg-gray-50/80 dark:hover:bg-gray-900/30 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">
                                        {{ new Date(log.created_at).toLocaleString('id-ID') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900 dark:text-white">
                                        {{ log.causer?.name || 'System' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="{
                                            'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': log.event === 'created',
                                            'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': log.event === 'updated',
                                            'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': log.event === 'deleted',
                                            'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400': !['created', 'updated', 'deleted'].includes(log.event)
                                        }" class="px-2 py-1 rounded text-[10px] font-black uppercase tracking-wider">
                                            {{ log.event }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-gray-700 dark:text-gray-300 font-sans text-sm mb-1">{{ log.description }}</p>
                                        <div v-if="log.properties" class="p-2 bg-gray-50 dark:bg-gray-900/50 rounded-lg text-[10px] text-gray-500 overflow-x-auto">
                                            {{ JSON.stringify(log.properties).substring(0, 100) }}...
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View (visible only on mobile) -->
                <div class="md:hidden space-y-4">
                    <div v-for="log in logs.data" :key="log.id" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="p-5">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs font-mono text-gray-400">{{ new Date(log.created_at).toLocaleString('id-ID') }}</span>
                                <span :class="{
                                    'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': log.event === 'created',
                                    'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': log.event === 'updated',
                                    'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': log.event === 'deleted'
                                }" class="px-2 py-1 rounded text-[10px] font-black uppercase tracking-wider">
                                    {{ log.event }}
                                </span>
                            </div>

                            <div class="mb-4">
                                <span class="text-[10px] uppercase font-black text-gray-400 block mb-1">User</span>
                                <h3 class="font-bold text-gray-900 dark:text-white text-base">{{ log.causer?.name || 'System' }}</h3>
                            </div>

                            <div>
                                <span class="text-[10px] uppercase font-black text-gray-400 block mb-1">Detail</span>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">{{ log.description }}</p>
                                <div v-if="log.properties" class="p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl font-mono text-[10px] text-gray-500 break-all">
                                    {{ JSON.stringify(log.properties).substring(0, 150) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
