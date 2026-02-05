<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { formatRupiah } from "@/Utils/format";

defineProps({
    stats: Array,
});
</script>

<template>
    <Head title="Laporan Institusi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-black leading-tight text-gray-800 dark:text-gray-200 uppercase tracking-tighter">
                Laporan Per Lembaga
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100 dark:border-gray-700">
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="inst in stats" :key="inst.code" class="p-6 rounded-2xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30 hover:shadow-lg transition flex flex-col justify-between">
                                <div>
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <p class="text-[10px] font-black text-pail-gold uppercase tracking-[0.2em]">{{ inst.code }}</p>
                                            <h3 class="text-lg font-black text-gray-900 dark:text-white line-clamp-1 tracking-tight">{{ inst.name }}</h3>
                                        </div>
                                        <div v-if="inst.pending_requests > 0" class="px-2 py-1 rounded-lg bg-red-100 text-red-600 text-[10px] font-black uppercase">
                                            {{ inst.pending_requests }} PENDING
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div class="p-4 rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-50 dark:border-gray-700">
                                            <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mb-1">Items</p>
                                            <p class="text-2xl font-black text-gray-800 dark:text-white">{{ inst.items_count }}</p>
                                        </div>
                                        <div class="p-4 rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-50 dark:border-gray-700">
                                            <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mb-1">Requests</p>
                                            <p class="text-2xl font-black text-gray-800 dark:text-white">{{ inst.requests_count }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-700/50">
                                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">Total Serapan Anggaran</p>
                                    <p class="text-xl font-black text-pail-gold font-mono">{{ formatRupiah(inst.total_cost) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
